<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Document;
use App\Models\History;
use App\Models\Item;
use App\Models\Letter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class LetterController extends Controller
{
    public function letter_index()
    {
        $archives = Archive::where('name', 'LIKE', 'Surat%')->latest()->get();
        $histories = History::latest()->get();
        $history_archive = $histories->where('type', 'archive');
        
        return view('pages.archive.index', [
            'title' => 'Surat - Putra Panggil Jaya',
            'navTitle' => 'Surat',
            'tableTitle' => 'Daftar Surat',
            'active' => 'archive',
            'archives' => $archives,
            'histories' => $histories,
            'history_archive' => $history_archive,
        ]);
    }

    public function letterIn_index()
    {
        $letters = Letter::where('type', 'letter_in')->latest()->get();
        $histories = History::latest()->get();
        $history_letter = $histories->where('type', 'letter_in');
        
        return view('pages.letter.index', [
            'title' => 'Surat Masuk - Putra Panggil Jaya',
            'navTitle' => 'Surat Masuk',
            'tableTitle' => 'Daftar Surat Masuk',
            'active' => 'archive',
            'letters' => $letters,
            'histories' => $histories,
            'historyLetter' => $history_letter,
            'historyLetterName' => 'Riwayat Surat Masuk'
        ]);
    }

    public function letterOut_index()
    {
        $letters = Letter::where('type', 'letter_out')->latest()->get();
        $histories = History::latest()->get();
        $history_letter = $histories->where('type', 'letter_out');
        
        return view('pages.letter.index', [
            'title' => 'Surat Keluar - Putra Panggil Jaya',
            'navTitle' => 'Surat Keluar',
            'tableTitle' => 'Daftar Surat Keluar',
            'active' => 'archive',
            'letters' => $letters,
            'histories' => $histories,
            'historyLetter' => $history_letter,
            'historyLetterName' => 'Riwayat Surat Keluar'
        ]);
    }

    public function letter_pending_index()
    {
        $letters = Letter::where('status', 'pending')->latest()->get();
        $histories = History::latest()->get();
        $historyLetter = $histories->reject(function ($history) {
            return in_array($history->type, ['staff', 'division', 'category', 'archive']);
        });

        if (Auth::user()->roles === 'superadmin') {
            return view('pages.letter.index', [
                'title' => 'Surat Pending - Putra Panggil Jaya',
                'navTitle' => 'Surat Pending',
                'tableTitle' => 'Daftar Surat Pending',
                'active' => 'pending',
                'letters' => $letters,
                'histories' => $histories,
                'historyLetter' => $historyLetter,
                'historyLetterName' => 'Riwayat Surat'
            ]);
        } else {
            return redirect()->route('archive.index');
        }
    }

    public function create(string $archive_id)
    {
        $archive = Archive::where('archive_id', $archive_id)->firstOrFail();
        $lastLetter = $archive->letters()->orderBy('no_letter', 'DESC')->first();
        $lastNumber = $lastLetter ? (int) last(explode('.', $lastLetter->no_letter)) : 0;
        $newNumber = sprintf('%03d', $lastNumber + 1);
        $no_letter = $archive_id . '.' . $newNumber;
        $letter_code = $no_letter . '/' . $archive->division->name . '/' . $archive->division->place;

        $histories = History::latest()->get();
        $items = Item::latest()->get();

        return view('pages.letter.create', [
            'title' => 'Tambah Surat - Putra Panggil Jaya',
            'navTitle' => 'Tambah Surat',
            'active' => 'archive',
            'archive' => $archive,
            'no_letter' => $no_letter,
            'letter_code' => $letter_code,
            'histories' => $histories,
            'items' => $items
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'archive_id' => 'required',
            'no_letter' => 'required',
            'letter_code' => 'required',
            'name' => 'required',
            'file.*' => 'mimes:jpg,jpeg,png,webp,doc,docx,pdf|max:10048',
        ], [
            'archive_id.required' => 'Arsip harus dipilih!',
            'no_letter.required' => 'Nomor surat harus diisi!',
            'letter_code.required' => 'Kode surat harus diisi!',
            'name.required' => 'Judul surat harus diisi!',
            'file.*.mimes' => 'File harus berekstensi jpg, jpeg, png, webp, doc, docx, atau pdf!',
            'file.*.max' => 'Ukuran file maksimal 10MB!',
        ]);

        if ($request->type == 'letter_out' || $request->type == 'faktur') {
            $date = Carbon::parse($request->date);
            $month = str_pad($date->month, 2, '0', STR_PAD_LEFT);
            $year = $date->year;
            $formattedLetterCode = "{$request->letter_code}/{$month}/{$year}";
        } else {
            $formattedLetterCode = $request->letter_code;
        }

        $data = $request->all();
        $data['letter_code'] = $formattedLetterCode;

        $letter = Letter::create($data);

        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $originalExtension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $originalExtension;
                $imageExtensions = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($originalExtension, $imageExtensions)) {
                    $fileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
                    $image = Image::make($file);
                    $image->resize(500, 500, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->encode('webp', 90);

                    Storage::disk('public')->put('documents/' . $fileName, $image->stream()->__toString());
                    $fileType = 'image';
                } else {
                    Storage::disk('public')->put('documents/' . $fileName, file_get_contents($file));
                    $fileType = 'file';
                }

                Document::create([
                    'letter_id' => $letter->id,
                    'file' => $fileName,
                    'type' => $fileType,
                ]);
            }
        }

        if ($letter) {
            $type = [];
            if ($letter->type == 'letter_in') {
                $type = 'Surat Masuk';
            } else if ($letter->type == 'letter_out') {
                $type = 'Surat Keluar';
            } else if ($letter->type == 'faktur') {
                $type = 'Faktur';
            } else {
                $type = 'Surat';
            }
            
            History::create([
                'type_id' => $letter->archive->id,
                'title' => "Buat " . $type . " Baru",
                'name' => $letter->no_letter . ' - ' . $letter->name,
                'description' =>  "Surat baru telah dibuat oleh " . Auth::user()->name . "\n" . "[{$letter->no_letter} => {$letter->name}].",
                'detail' => "Surat baru telah dibuat oleh " . Auth::user()->name . "\n" . "[{$letter->no_letter} => {$letter->name}].",
                'type' => $letter->type,
                'method' => 'create',
                'user_id' => Auth::user()->id,
            ]);
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('success', 'Surat berhasil dibuat!');
        } else {
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('error', 'Surat gagal dibuat!');
        }
    }

    public function show(string $no_letter)
    {
        $letter = Letter::where('no_letter', $no_letter)->firstOrFail();
        $letter_reply = Letter::where('letter_id', $letter->id)->get();
        $archive = $letter->archive;
        $histories = History::latest()->get();

        return view('pages.letter.show', [
            'title' => 'Detail Surat',
            'navTitle' => 'Detail Surat',
            'active' => 'archive',
            'letter_reply' => $letter_reply,
            'letter' => $letter,
            'archive' => $archive,
            'histories' => $histories
        ]);
    }

    public function edit(string $no_letter)
    {
        $letter = Letter::where('no_letter', $no_letter)->with('archive')->firstOrFail();
        if (Auth::user()->roles !== 'superadmin' && $letter->status === 'approve') {
            return redirect()->back()->with('error', 'Surat ini telah disetujui!');
        }
        if (Auth::user()->roles === 'user' && Auth::user()->id !== $letter->user_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk mengubah surat ini!');
        }

        $archive = $letter->archive;
        $documents = $letter->documents;
        $histories = History::latest()->get();
        $items = Item::latest()->get();

        return view('pages.letter.edit', [
            'title' => 'Edit Surat - ' . $letter->name,
            'navTitle' => 'Edit Surat',
            'active' => 'archive',
            'letter' => $letter,
            'archive' => $archive,
            'documents' => $documents,
            'histories' => $histories,
            'items' => $items
        ]);
    }
    
    public function update(Request $request, $no_letter)
    {
        $letter = Letter::findOrFail($no_letter);
        if (Auth::user()->roles !== 'superadmin' && $letter->status === 'approve') {
            return redirect()->back()->with('error', 'Surat ini telah disetujui!');
        }
        if (Auth::user()->roles === 'user' && Auth::user()->id !== $letter->user_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk mengubah surat ini!');
        }

        $isUpdateStatus = false;
        $isUpdate = false;
        $updates = [];
        $detailUpdates = [];

        $oldLetterCode = $letter->letter_code;
        if ($letter->item_id !== null) {
            $oldItemId = $letter->item_id;
            $oldItemName = '[' . $letter->item->inventory->name . ' => ' . $letter->item->name . ']';
        }
        $oldLetterName = $letter->name;
        $oldStatus = $letter->status;
        $oldType = $letter->type;
        $oldContent = $letter->content;
        $oldDetail = $letter->detail;
        $oldDate = $letter->date;
        $oldFile = $letter->file;

        $request->validate([
            'no_letter' => 'required',
            'letter_code' => 'required',
            'name' => 'required',
        ], [
            'no_letter.required' => 'Nomor surat harus diisi!',
            'letter_code.required' => 'Kode surat harus diisi!',
            'name.required' => 'Judul surat harus diisi!',
        ]);


        if ($letter->item_id !== null) {
            $letter->item_id = $request->item_id;
        }
        if ($letter->item_id !== null) {
            $newItem = Item::find($request->item_id);
            $newItemName = $newItem ? '[' . $newItem->inventory->name . ' => ' . $newItem->name . ']' : $oldItemId;
        }
        if (Auth::user()->roles == 'superadmin' && $oldStatus !== $request->status) {
            $letter->status = $request->status;
            $updates[] = "Status dari '$oldStatus' menjadi '$request->status'";
            $detailUpdates[] = "Status dari '$oldStatus' menjadi '$request->status'";
            $isUpdateStatus = true;
        }
        if ($oldType !== $request->type) {
            $updates[] = "Type surat dari '$oldType' menjadi '$request->type'";
            $detailUpdates[] = "Type surat dari '$oldType' menjadi '$request->type'";
            $isUpdate = true;
        }
        if ($letter->item_id !== null) {
            if ($oldItemId != $request->item_id) {
                $updates[] = "Inventory dari '$oldItemName' menjadi '$newItemName'";
                $detailUpdates[] = "Inventory dari '$oldItemName' menjadi '$newItemName'";
                $isUpdate = true;
            }
        }
        if ($oldLetterName !== $request->name) {
            $updates[] = "Nama surat dari '$oldLetterName' menjadi '$request->name'";
            $detailUpdates[] = "Nama surat dari '$oldLetterName' menjadi '$request->name'";
            $isUpdate = true;
        }
        if ($oldLetterCode !== $request->letter_code) {
            $updates[] = "Kode surat dari '$oldLetterCode' menjadi '$request->letter_code'";
            $detailUpdates[] = "Kode surat dari '$oldLetterCode' menjadi '$request->letter_code'";
            $isUpdate = true;
        }
        if ($oldContent !== $request->content) {
            $updates[] = "Isi surat diupdate";
            $detailUpdates[] = "Isi surat dari '$oldContent' menjadi '$request->content'";
            $isUpdate = true;
        }
        if ($oldDetail !== $request->detail) {
            $updates[] = "Detail surat diupdate";
            $detailUpdates[] = "Detail surat dari '$oldDetail' menjadi '$request->detail'";
            $isUpdate = true;
        }
        if ($oldDate !== $request->date) {
            $updates[] = "Tanggal surat dari '$oldDate' menjadi '$request->date'";
            $detailUpdates[] = "Tanggal surat dari '$oldDate' menjadi '$request->date'";
            $isUpdate = true;
        }

        // Cek apakah status bukan 'pending'
        if ($isUpdate && $oldStatus !== 'pending') {
            $letter->status = 'pending';
            $updates[] = "Status otomatis berubah menjadi 'pending' karena ada perubahan data pada surat";
            $detailUpdates[] = "Status otomatis berubah menjadi 'pending' karena ada perubahan data pada surat";
            $isUpdateStatus = true;
        }


        if ($request->type == 'letter_out' || $request->type == 'faktur') {
            $date = Carbon::parse($request->date);
            $month = str_pad($date->month, 2, '0', STR_PAD_LEFT);
            $year = $date->year;

            $oldLetterCode = $letter->letter_code;
            $oldLetterCodeExploded = explode('/', $oldLetterCode);
            $oldLetterCode = $oldLetterCodeExploded[0];
            $formattedLetterCode = $oldLetterCode . "/{$letter->archive->division->name}/{$letter->archive->division->place}/{$month}/{$year}";
        } else {
            $formattedLetterCode = $request->letter_code;
        }
        $letter->letter_code = $formattedLetterCode;
        $letter->name = $request->name;
        $letter->type = $request->type;
        $letter->content = $request->content;
        $letter->detail = $request->detail;
        $letter->date = $request->date;
        $letter->save();

        $methods = [];
        if ($isUpdateStatus) {
            $methods[] = 'update status';
        }
        if ($isUpdate) {
            $methods[] = 'update';
        }

        $type = [];
        if ($letter->type == 'letter_in') {
            $type = 'Surat Masuk';
        } else if ($letter->type == 'letter_out') {
            $type = 'Surat Keluar';
        } else if ($letter->type == 'faktur') {
            $type = 'Faktur';
        } else {
            $type = 'Surat';
        }

        $title = count($methods) > 1 ? "Update " . $type . " dan Status" : (in_array('update status', $methods) ? "Update Status " . $type : "Update " . $type);
        $method = implode(', ', $methods);

        if (empty($updates)) {
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('error', 'Tidak ada perubahan yang dilakukan!');
        }

        $description = "Surat telah diupdate oleh " . Auth::user()->name . ".";
        $descriptionDetail = "Surat telah diupdate oleh " . Auth::user()->name . ".";
        if (!empty($updates && $detailUpdates)) {
            $description .= "\n" . implode(", \n", $updates);
            $descriptionDetail .= "\n" . implode(", \n", $detailUpdates);

            History::create([
                'type_id' => $letter->archive->id,
                'title' => $title,
                'name' => $letter->no_letter . ' - ' . $letter->name,
                'description' => $description . '.',
                'detail' => $descriptionDetail. '.',
                'type' => $letter->type,
                'method' => $method,
                'user_id' => Auth::user()->id,
            ]);
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('success', 'Surat berhasil diupdate!');
        } else {
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('error', 'Tidak ada perubahan yang dilakukan!');
        }
    }

    public function delete_letter(Request $request, $id)
    {
        $letter = Letter::findOrFail($id);
        if (Auth::user()->roles !== 'superadmin' && $letter->status === 'approve') {
            return redirect()->back()->with('error', 'Surat ini telah disetujui!');
        }
        if (Auth::user()->roles === 'user' && Auth::user()->id !== $letter->user_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus surat ini!');
        }

        $oldArchiveId = $letter->archive->id;
        $oldCode = $letter->no_letter;
        $oldName = $letter->name;

        $description = "Surat [" . $oldCode . ' => ' . $oldName . "] di Arsip [" . $letter->archive->archive_id . ' => ' . $letter->archive->name . "] dihapus oleh " . Auth::user()->name . ".";
        
        if ($letter) {
            $type = [];
            if ($letter->type == 'letter_in') {
                $type = 'Surat Masuk';
            } else {
                $type = 'Surat Keluar';
            }

            History::create([
                'type_id' => $oldArchiveId,
                'title' => "Hapus " . $type,
                'name' => $letter->no_letter . ' - ' . $letter->name,
                'description' => $description,
                'detail' => $description,
                'type' => $letter->type,
                'method' => 'delete',
                'user_id' => Auth::user()->id,
            ]);

            $letter->status = $request->status;
            $letter->save();

            return redirect()->route('archive.show', $letter->archive->archive_id)->with('success', 'Surat berhasil dihapus!');
        } else {
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('error', 'Surat gagal dihapus!');
        }
    }

    public function letter_reply(String $no_letter)
    {
        $letter = Letter::where('no_letter', $no_letter)->with('archive')->firstOrFail();
        $archive = $letter->archive;
        $lastLetter = $archive->letters()->orderBy('no_letter', 'DESC')->first();
        $lastNumber = $lastLetter ? (int) last(explode('.', $lastLetter->no_letter)) : 0;
        $newNumber = sprintf('%03d', $lastNumber + 1);
        $newNoLetter = $archive->archive_id . '.' . $newNumber;
        $letter_code = $newNoLetter . '/' . $archive->division->name . '/' . $archive->division->place;

        $letter_id = $letter->id;

        $documents = $letter->documents;
        $histories = History::latest()->get();
        $items = Item::latest()->get();

        return view('pages.letter.create', [
            'title' => 'Balas Surat - ' . $letter->name,
            'navTitle' => 'Balas Surat',
            'active' => 'archive',
            'letter' => $letter,
            'no_letter' => $newNoLetter,
            'letter_code' => $letter_code,
            'letter_id' => $letter_id,
            'archive' => $archive,
            'documents' => $documents,
            'histories' => $histories,
            'items' => $items
        ]);
    }

    public function approve_letter(Request $request)
    {
        if (Auth::user()->roles === 'superadmin') {
           $no_lettes = explode(',', $request->no_letters);

            if (empty($no_lettes)) {
                return redirect()->back()->with('error', 'Tidak ada yang dipilih!');
            }

            Letter::whereIn('no_letter', $no_lettes)->update(['status' => 'approve']);

            foreach ($no_lettes as $no_letter) {
                $letter = Letter::where('no_letter', $no_letter)->first();
                $description = "[" . $no_letter . ' - ' . $letter->name . "] diapprove oleh " . Auth::user()->name . ".";
                History::create([
                    'type_id' => $letter->archive->id,
                    'title' => "Update Status",
                    'name' => $letter->no_letter . ' - ' . $letter->name,
                    'description' => $description,
                    'detail' => $description,
                    'type' => $letter->type,
                    'method' => 'update status',
                    'user_id' => Auth::user()->id,
                ]);
            }

            return redirect()->back()->with('success', 'Surat berhasil diapprove!');
        } else {
            return redirect()->back()->with('error', 'Oppss... terjadi kesalahan!');
        }
    }
}
