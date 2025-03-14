<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Document;
use App\Models\History;
use App\Models\Letter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MemoController extends Controller
{
    public function index()
    {
        $categories = Category::whereIn('name', ['Memo', 'Notulen'])->get();
        if ($categories->isNotEmpty()) {
            $archives = Archive::with('category')->whereIn('category_id', $categories->pluck('id'))->latest()->get();
        } else {
            $archives = collect();
        }
        
        $histories = History::latest()->get();
        $history_archive = $histories->where('type', 'archive');
        
        return view('pages.archive.index', [
            'title' => 'Arsip Memo - Putra Panggil Jaya',
            'navTitle' => 'Arsip Memo',
            'tableTitle' => 'Daftar Semua Memo',
            'active' => 'archive',
            'archives' => $archives,
            'histories' => $histories,
            'history_archive' => $history_archive,
        ]);
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

        return view('pages.memo.create', [
            'title' => 'Tambah ' . $archive->category->name . ' - Putra Panggil Jaya',
            'navTitle' => 'Tambah ' . $archive->category->name,
            'active' => 'archive',
            'archive' => $archive,
            'no_letter' => $no_letter,
            'letter_code' => $letter_code,
            'histories' => $histories
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

        $date = Carbon::parse($request->date);
        $month = str_pad($date->month, 2, '0', STR_PAD_LEFT);
        $year = $date->year;
        $formattedLetterCode = "{$request->letter_code}/{$month}/{$year}";

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
            if ($letter->type == 'memo') {
                $type = 'Memo';
            } else {
                $type = 'Notulen';
            }
            
            History::create([
                'type_id' => $letter->archive->id,
                'title' => "Buat " . $type . " Baru",
                'name' => $letter->no_letter . ' - ' . $letter->name,
                'description' => $type . " baru telah dibuat oleh " . Auth::user()->name . "\n" . "[{$letter->no_letter} => {$letter->name}].",
                'detail' => $type . " baru telah dibuat oleh " . Auth::user()->name . "\n" . "[{$letter->no_letter} => {$letter->name}].",
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

        return view('pages.memo.show', [
            'title' => 'Detail ' . $archive->category->name,
            'navTitle' => 'Detail ' . $archive->category->name,
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

        return view('pages.memo.edit', [
            'title' => 'Edit ' . $archive->category->name . ' - ' . $letter->name,
            'navTitle' => 'Edit ' . $archive->category->name,
            'active' => 'archive',
            'letter' => $letter,
            'archive' => $archive,
            'documents' => $documents,
            'histories' => $histories,
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
        $oldLetterName = $letter->name;
        $oldStatus = $letter->status;
        $oldContent = $letter->content;
        $oldDetail = $letter->detail;
        $oldDate = $letter->date;
        $oldFile = $letter->file;
        $oldStartTime = $letter->start_time;
        $oldEndTime = $letter->end_time;
        $oldPlace = $letter->place;
        $oldEvent = $letter->event;
        $oldChairman = $letter->chairman;
        $oldChairmanPosition = $letter->chairman_position;
        $oldNotulis = $letter->notulis;
        $oldParticipant = $letter->participant;
        $oldDecision = $letter->decision;

        $request->validate([
            'no_letter' => 'required',
            'letter_code' => 'required',
            'name' => 'required',
        ], [
            'no_letter.required' => 'Nomor surat harus diisi!',
            'letter_code.required' => 'Kode surat harus diisi!',
            'name.required' => 'Judul surat harus diisi!',
        ]);

        
        if (Auth::user()->roles == 'superadmin' && $oldStatus !== $request->status) {
            $letter->status = $request->status;
            $updates[] = "Status dari '$oldStatus' menjadi '$request->status'";
            $detailUpdates[] = "Status dari '$oldStatus' menjadi '$request->status'";
            $isUpdateStatus = true;
        }
        if ($oldLetterName !== $request->name) {
            $updates[] = "Nama dari '$oldLetterName' menjadi '$request->name'";
            $detailUpdates[] = "Nama dari '$oldLetterName' menjadi '$request->name'";
            $isUpdate = true;
        }
        if ($oldLetterCode !== $request->letter_code) {
            $updates[] = "Kode dari '$oldLetterCode' menjadi '$request->letter_code'";
            $detailUpdates[] = "Kode dari '$oldLetterCode' menjadi '$request->letter_code'";
            $isUpdate = true;
        }
        if ($oldContent !== $request->content) {
            $updates[] = "Isi repat diupdate";
            $detailUpdates[] = "Isi repat dari '$oldContent' menjadi '$request->content'";
            $isUpdate = true;
        }
        if ($oldDetail !== $request->detail) {
            $updates[] = "Detail surat diupdate";
            $detailUpdates[] = "Detail surat dari '$oldDetail' menjadi '$request->detail'";
            $isUpdate = true;
        }
        if ($oldDate !== $request->date) {
            $updates[] = "Tanggal dari '$oldDate' menjadi '$request->date'";
            $detailUpdates[] = "Tanggal dari '$oldDate' menjadi '$request->date'";
            $isUpdate = true;
        }
        if ($oldStartTime !== $request->start_time) {
            $updates[] = "Waktu mulai dari '$oldStartTime' menjadi '$request->start_time'";
            $detailUpdates[] = "Waktu mulai dari '$oldStartTime' menjadi '$request->start_time'";
            $isUpdate = true;
        }
        if ($oldEndTime !== $request->end_time) {
            $updates[] = "Waktu selesai dari '$oldEndTime' menjadi '$request->end_time'";
            $detailUpdates[] = "Waktu selesai dari '$oldEndTime' menjadi '$request->end_time'";
            $isUpdate = true;
        }
        if ($oldPlace !== $request->place) {
            $updates[] = "Tempat dari '$oldPlace' menjadi '$request->place'";
            $detailUpdates[] = "Tempat dari '$oldPlace' menjadi '$request->place'";
            $isUpdate = true;
        }
        if ($oldEvent !== $request->event) {
            $updates[] = "Acara dari '$oldEvent' menjadi '$request->event'";
            $detailUpdates[] = "Acara dari '$oldEvent' menjadi '$request->event'";
            $isUpdate = true;
        }
        if ($oldChairman !== $request->chairman) {
            $updates[] = "Ketua dari '$oldChairman' menjadi '$request->chairman'";
            $detailUpdates[] = "Ketua dari '$oldChairman' menjadi '$request->chairman'";
            $isUpdate = true;
        }
        if ($oldChairmanPosition !== $request->chairman_position) {
            $updates[] = "Jabatan ketua dari '$oldChairmanPosition' menjadi '$request->chairman_position'";
            $detailUpdates[] = "Jabatan ketua dari '$oldChairmanPosition' menjadi '$request->chairman_position'";
            $isUpdate = true;
        }
        if ($oldNotulis !== $request->notulis) {
            $updates[] = "Notulis dari '$oldNotulis' menjadi '$request->notulis'";
            $detailUpdates[] = "Notulis dari '$oldNotulis' menjadi '$request->notulis'";
            $isUpdate = true;
        }
        if ($oldParticipant !== $request->participant) {
            $updates[] = "Peserta rapat diupdate";
            $detailUpdates[] = "Peserta rapat dari '$oldParticipant' menjadi '$request->participant'";
            $isUpdate = true;
        }
        if ($oldDecision !== $request->decision) {
            $updates[] = "Kesimpulan rapat diupdate";
            $detailUpdates[] = "Kesimpulan rapat dari '$oldDecision' menjadi '$request->decision'";
            $isUpdate = true;
        }

        // Cek apakah status bukan 'pending'
        if ($isUpdate && $oldStatus !== 'pending') {
            $letter->status = 'pending';
            $updates[] = "Status otomatis berubah menjadi 'pending' karena ada perubahan data pada surat";
            $detailUpdates[] = "Status otomatis berubah menjadi 'pending' karena ada perubahan data pada surat";
            $isUpdateStatus = true;
        }


        $date = Carbon::parse($request->date);
        $month = str_pad($date->month, 2, '0', STR_PAD_LEFT);
        $year = $date->year;

        $oldLetterCode = $letter->letter_code;
        $oldLetterCodeExploded = explode('/', $oldLetterCode);
        $oldLetterCode = $oldLetterCodeExploded[0];
        $formattedLetterCode = $oldLetterCode . "/{$letter->archive->division->name}/{$letter->archive->division->place}/{$month}/{$year}";


        $letter->letter_code = $formattedLetterCode;
        $letter->name = $request->name;
        $letter->type = $request->type;
        $letter->content = $request->content;
        $letter->detail = $request->detail;
        $letter->date = $request->date;
        $letter->start_time = $request->start_time;
        $letter->end_time = $request->end_time;
        $letter->place = $request->place;
        $letter->event = $request->event;
        $letter->chairman = $request->chairman;
        $letter->chairman_position = $request->chairman_position;
        $letter->notulis = $request->notulis;
        $letter->participant = $request->participant;
        $letter->decision = $request->decision;
        $letter->save();

        $methods = [];
        if ($isUpdateStatus) {
            $methods[] = 'update status';
        }
        if ($isUpdate) {
            $methods[] = 'update';
        }

        $type = [];
        if ($letter->type == 'memo') {
            $type = 'Memo';
        } else {
            $type = 'Notulen';
        }

        $title = count($methods) > 1 ? "Update " . $type . " dan Status" : (in_array('update status', $methods) ? "Update Status " . $type : "Update " . $type);
        $method = implode(', ', $methods);

        if (empty($updates)) {
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('error', 'Tidak ada perubahan yang dilakukan!');
        }

        $description = "Surat telah diupdate oleh " . Auth::user()->name . ".";
        $descriptionDetail = "Surat telah diupdate oleh " . Auth::user()->name . ".";
        if (!empty($updates && $descriptionDetail)) {
            $description .= "\n" . implode(", \n", $updates);
            $descriptionDetail .= "\n" . implode(", \n", $detailUpdates);

            History::create([
                'type_id' => $letter->archive->id,
                'title' => $title,
                'name' => $letter->no_letter . ' - ' . $letter->name,
                'description' => $description . '.',
                'detail' => $descriptionDetail . '.',
                'type' => $letter->type,
                'method' => $method,
                'user_id' => Auth::user()->id,
            ]);
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('success', 'Surat berhasil diupdate!');
        } else {
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('error', 'Tidak ada perubahan yang dilakukan!');
        }
    }

    public function delete_memo(Request $request, $id)
    {
        $letter = Letter::findOrFail($id);
        if (Auth::user()->roles === 'user' && Auth::user()->id !== $letter->user_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus surat ini!');
        }

        $oldArchiveId = $letter->archive->id;
        $oldCode = $letter->no_letter;
        $oldName = $letter->name;

        $description = "[" . $oldCode . ' => ' . $oldName . "] di Arsip [" . $letter->archive->archive_id . ' => ' . $letter->archive->name . "] dihapus oleh " . Auth::user()->name . ".";
        
        if ($letter) {
            $type = [];
            if ($letter->type == 'memo') {
                $type = 'Memo';
            } else {
                $type = 'Notulen';
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
}
