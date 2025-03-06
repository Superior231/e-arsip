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

        if ($request->type == 'letter_out') {
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

        $archive = Archive::findOrFail($request->archive_id);
        $oldStatus = $archive->status;
        $updates = [];

        // Jika status archive bukan pending, ubah ke pending
        if ($oldStatus !== 'pending') {
            $archive->status = 'pending';
            $archive->save();
            $updates[] = "Status arsip otomatis berubah menjadi 'pending' karena ada penambahan surat baru";

            $description = "Arsip telah diupdate oleh " . Auth::user()->name . ".";
            if (!empty($updates)) {
                $description .= "\n" . implode(", \n", $updates);
            }

            History::create([
                'type_id' => $archive->id,
                'title' => 'Update Status Arsip',
                'name' => $archive->archive_id . ' - ' . $archive->name,
                'description' => $description . '.',
                'type' => 'archive',
                'method' => 'update status',
                'user_id' => Auth::user()->id,
            ]);
        }

        if ($letter) {
            History::create([
                'type_id' => $letter->archive->id,
                'title' => "Buat Surat Baru",
                'name' => $letter->no_letter . ' - ' . $letter->name,
                'description' =>  "Surat baru telah dibuat oleh " . Auth::user()->name . "\n" . "[{$letter->no_letter} => {$letter->name}].",
                'type' => 'letter',
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
        $archive = $letter->archive;
        $histories = History::latest()->get();

        return view('pages.letter.show', [
            'title' => 'Detail Surat',
            'navTitle' => 'Detail Surat',
            'active' => 'archive',
            'letter' => $letter,
            'archive' => $archive,
            'histories' => $histories
        ]);
    }

    public function edit(string $no_letter)
    {
        $letter = Letter::where('no_letter', $no_letter)->with('archive')->firstOrFail();
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
        $request->validate([
            'no_letter' => 'required',
            'letter_code' => 'required',
            'name' => 'required',
        ], [
            'no_letter.required' => 'Nomor surat harus diisi!',
            'letter_code.required' => 'Kode surat harus diisi!',
            'name.required' => 'Judul surat harus diisi!',
        ]);

        $letter = Letter::findOrFail($no_letter);

        $oldLetterCode = $letter->letter_code;
        $oldItemId = $letter->item_id;
        $oldItemName = '[' . $letter->item->inventory->name . ' => ' . $letter->item->name . ']';
        $oldLetterName = $letter->name;
        $oldLampiran = $letter->lampiran;
        $oldPerihal = $letter->perihal;
        $oldStatus = $letter->status;
        $oldType = $letter->type;
        $oldContent = $letter->content;
        $oldDetail = $letter->detail;
        $oldDate = $letter->date;
        $oldFile = $letter->file;

        $letter->letter_code = $request->letter_code;
        $letter->item_id = $request->item_id;
        $letter->name = $request->name;
        $letter->lampiran = $request->lampiran;
        $letter->perihal = $request->perihal;
        $letter->status = $request->status;
        $letter->type = $request->type;
        $letter->content = $request->content;
        $letter->detail = $request->detail;
        $letter->date = $request->date;

        $newItem = Item::find($request->item_id);
        $newItemName = $newItem ? '[' . $newItem->inventory->name . ' => ' . $newItem->name . ']' : $oldItemId;

        $letter->save();

        $isUpdateStatus = false;
        $isUpdate = false;
        $updates = [];

        if ($oldStatus !== $request->status) {
            $updates[] = "Status surat dari '$oldStatus' menjadi '$request->status'";
            $isUpdateStatus = true;
        }
        if ($oldType !== $request->type) {
            $updates[] = "Type surat dari '$oldType' menjadi '$request->type'";
            $isUpdate = true;
        }
        if ($oldItemId != $request->item_id) {
            $updates[] = "Inventory dari '$oldItemName' menjadi '$newItemName'";
            $isUpdate = true;
        }
        if ($oldLetterName !== $request->name) {
            $updates[] = "Nama surat dari '$oldLetterName' menjadi '$request->name'";
            $isUpdate = true;
        }
        if ($oldLetterCode !== $request->letter_code) {
            $updates[] = "Kode surat dari '$oldLetterCode' menjadi '$request->letter_code'";
            $isUpdate = true;
        }
        if ($oldLampiran !== $request->lampiran) {
            $updates[] = "Lampiran surat dari '$oldLampiran' menjadi '$request->lampiran'";
            $isUpdate = true;
        }
        if ($oldPerihal !== $request->perihal) {
            $updates[] = "Perihal surat dari '$oldPerihal' menjadi '$request->perihal'";
            $isUpdate = true;
        }
        if ($oldContent !== $request->content) {
            $updates[] = "Isi surat dari '$oldContent' menjadi '$request->content'";
            $isUpdate = true;
        }
        if ($oldDetail !== $request->detail) {
            $updates[] = "Detail surat dari '$oldDetail' menjadi '$request->detail'";
            $isUpdate = true;
        }
        if ($oldDate !== $request->date) {
            $updates[] = "Tanggal surat dari '$oldDate' menjadi '$request->date'";
            $isUpdate = true;
        }


        $methods = [];
        if ($isUpdateStatus) {
            $methods[] = 'update status';
        }
        if ($isUpdate) {
            $methods[] = 'update';
        }

        $title = count($methods) > 1 ? "Update Surat dan Status" : (in_array('update status', $methods) ? "Update Status Surat" : "Update Surat");
        $method = implode(', ', $methods);

        if (empty($updates)) {
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('error', 'Tidak ada perubahan yang dilakukan!');
        }
        
        // Cek apakah status archive bukan 'pending'
        $archive = $letter->archive;
        if ($archive->status !== 'pending') {
            $archive->status = 'pending';
            $archive->save();
            $updates[] = "Status arsip otomatis berubah menjadi 'pending' karena ada perubahan data pada surat";

            $description = "Arsip telah diupdate oleh " . Auth::user()->name . ".";
            if (!empty($updates)) {
                $description .= "\n" . implode(", \n", $updates);
            }

            History::create([
                'type_id' => $archive->id,
                'title' => 'Update Status Arsip',
                'name' => $archive->archive_id . ' - ' . $archive->name,
                'description' => $description . '.',
                'type' => 'archive',
                'method' => 'update status',
                'user_id' => Auth::user()->id,
            ]);
        }

        $description = "Surat telah diupdate oleh " . Auth::user()->name . ".";
        if (!empty($updates)) {
            $description .= "\n" . implode(", \n", $updates);

            History::create([
                'type_id' => $letter->archive->id,
                'title' => $title,
                'name' => $letter->no_letter . ' - ' . $letter->name,
                'description' => $description . '.',
                'type' => 'letter',
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
        $oldArchiveId = $letter->archive->id;
        $oldCode = $letter->no_letter;
        $oldName = $letter->name;

        // Cek apakah status archive bukan 'pending'
        $archive = $letter->archive;
        if ($archive->status !== 'pending') {
            $archive->status = 'pending';
            $archive->save();
            $updates[] = "Status arsip otomatis berubah menjadi 'pending' karena ada penghapusan surat";

            // Buat deskripsi perubahan
            $description = "Arsip telah diupdate oleh " . Auth::user()->name . ".";
            if (!empty($updates)) {
                $description .= "\n" . implode(", \n", $updates);
            }

            // Simpan history perubahan
            History::create([
                'type_id' => $archive->id,
                'title' => 'Update Status Arsip',
                'name' => $archive->archive_id . ' - ' . $archive->name,
                'description' => $description . '.',
                'type' => 'archive',
                'method' => 'update status',
                'user_id' => Auth::user()->id,
            ]);
        }

        $description = "Surat [" . $oldCode . ' => ' . $oldName . "] di Arsip [" . $letter->archive->archive_id . ' => ' . $letter->archive->name . "] dihapus oleh " . Auth::user()->name . ".";
        
        if ($letter) {
            History::create([
                'type_id' => $oldArchiveId,
                'title' => "Hapus Surat",
                'name' => $letter->no_letter . ' - ' . $letter->name,
                'description' => $description,
                'type' => 'letter',
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
}
