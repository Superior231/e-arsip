<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archive;
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
            'image' => 'image|mimes:jpg,jpeg,png,webp|max:10048',
            'letter' => 'file|mimes:pdf,doc,docx|max:5048',
        ], [
            'archive_id.required' => 'Arsip harus dipilih!',
            'no_letter.required' => 'Nomor surat harus diisi!',
            'letter_code.required' => 'Kode surat harus diisi!',
            'name.required' => 'Judul surat harus diisi!',
            'image.image' => 'File gambar harus berupa gambar!',
            'image.mimes' => 'File gambar harus berekstensi jpg, jpeg, png, atau webp!',
            'image.max' => 'Ukuran file gambar maksimal 10MB!',
            'letter.file' => 'File surat harus berupa file!',
            'letter.mimes' => 'File surat harus berekstensi pdf, doc, atau docx!',
            'letter.max' => 'Ukuran file maksimal 5MB!',
        ]);

        $date = Carbon::parse($request->date);
        $month = str_pad($date->month, 2, '0', STR_PAD_LEFT);
        $year = $date->year;
        $formattedLetterCode = "{$request->letter_code}/{$month}/{$year}";

        $data = $request->all();
        $data['letter_code'] = $formattedLetterCode;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
            $image = Image::make($file)->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode('webp', 90);

            Storage::disk('public')->put('letter_image/' . $fileName, (string) $image);
            $data['image'] = $fileName;
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->put('letter/' . $fileName, file_get_contents($file));
            $data['file'] = $fileName;
        }
        $letter = Letter::create($data);

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
                'title' => 'Mutasi Arsip',
                'name' => $archive->archive_id . ' - ' . $archive->name,
                'description' => $description . '.',
                'type' => 'archive',
                'method' => 'mutate',
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
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('success', 'Surat berhasil ditambahkan!');
        } else {
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('error', 'Surat gagal ditambahkan!');
        }
    }

    public function edit(string $no_letter)
    {
        $letter = Letter::where('no_letter', $no_letter)->with('archive')->firstOrFail();
        $archive = $letter->archive;
        $histories = History::latest()->get();
        $items = Item::latest()->get();

        return view('pages.letter.edit', [
            'title' => 'Edit Surat - ' . $letter->name,
            'navTitle' => 'Edit Surat',
            'active' => 'archive',
            'letter' => $letter,
            'archive' => $archive,
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
            'letter' => 'file|mimes:pdf,doc,docx|max:5048',
        ], [
            'no_letter.required' => 'Nomor surat harus diisi!',
            'letter_code.required' => 'Kode surat harus diisi!',
            'name.required' => 'Judul surat harus diisi!',
            'letter.file' => 'File surat harus berupa file!',
            'letter.mimes' => 'File surat harus berekstensi pdf, doc, atau docx!',
            'letter.max' => 'Ukuran file maksimal 5MB!',
        ]);

        $letter = Letter::findOrFail($no_letter);

        $oldLetterCode = $letter->letter_code;
        $oldItemId = $letter->item_id;
        $oldItemName = '[' . $letter->item->inventory->name . ' => ' . $letter->item->name . ']';
        $oldLetterName = $letter->name;
        $oldLampiran = $letter->lampiran;
        $oldPerihal = $letter->perihal;
        $oldStatus = $letter->status;
        $oldContent = $letter->content;
        $oldDetail = $letter->detail;
        $oldDate = $letter->date;
        $oldImage = $letter->image;
        $oldFile = $letter->file;

        $letter->letter_code = $request->letter_code;
        $letter->item_id = $request->item_id;
        $letter->name = $request->name;
        $letter->lampiran = $request->lampiran;
        $letter->perihal = $request->perihal;
        $letter->status = $request->status;
        $letter->content = $request->content;
        $letter->detail = $request->detail;
        $letter->date = $request->date;

        $newItem = Item::find($request->item_id);
        $newItemName = $newItem ? '[' . $newItem->inventory->name . ' => ' . $newItem->name . ']' : $oldItemId;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
            $image = Image::make($file)->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode('webp', 90);

            Storage::disk('public')->put('letter_image/' . $fileName, (string) $image);
            $data['image'] = $fileName;
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->put('letter/' . $fileName, file_get_contents($file));
            $data['file'] = $fileName;
        }

        $letter->save();

        $updates = [];
        $methods = [];

        if ($oldStatus !== $request->status) {
            $updates[] = "Status surat dari '$oldStatus' menjadi '$request->status'";
        }
        if ($oldItemId != $request->item_id) {
            $updates[] = "Inventory dari '$oldItemName' menjadi '$newItemName'";
        }
        if ($oldLetterName !== $request->name) {
            $updates[] = "Nama surat dari '$oldLetterName' menjadi '$request->name'";
        }
        if ($oldLetterCode !== $request->letter_code) {
            $updates[] = "Kode surat dari '$oldLetterCode' menjadi '$request->letter_code'";
        }
        if ($oldLampiran !== $request->lampiran) {
            $updates[] = "Lampiran surat dari '$oldLampiran' menjadi '$request->lampiran'";
        }
        if ($oldPerihal !== $request->perihal) {
            $updates[] = "Perihal surat dari '$oldPerihal' menjadi '$request->perihal'";
        }
        if ($oldContent !== $request->content) {
            $updates[] = "Isi surat dari '$oldContent' menjadi '$request->content'";
        }
        if ($oldDetail !== $request->detail) {
            $updates[] = "Detail surat dari '$oldDetail' menjadi '$request->detail'";
        }
        if ($oldDate !== $request->date) {
            $updates[] = "Tanggal surat dari '$oldDate' menjadi '$request->date'";
        }
        if ($request->hasFile('image') && $oldImage !== $data['image']) {
            $updates[] = "Gambar surat dari '$oldImage' menjadi '$data[image]'";
        }
        if ($request->hasFile('file') && $oldFile !== $data['file']) {
            $updates[] = "File surat dari '$oldFile' menjadi '$data[file]'";
        }
        if (!empty(array_diff($updates, ["Status item dari '$oldStatus' menjadi '$request->status'"]))) {
            $methods[] = 'update';
        }

        $methods = array_unique($methods);
        $title = count($methods) > 1 ? "Mutasi dan Update Surat" : (in_array('mutate', $methods) ? "Mutasi Surat" : "Update Surat");
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
                'title' => 'Mutasi Arsip',
                'name' => $archive->archive_id . ' - ' . $archive->name,
                'description' => $description . '.',
                'type' => 'archive',
                'method' => 'mutate',
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

    public function destroy(string $id)
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
            $description = "Archive telah diupdate oleh " . Auth::user()->name . ".";
            if (!empty($updates)) {
                $description .= "\n" . implode(", \n", $updates);
            }

            // Simpan history perubahan
            History::create([
                'type_id' => $archive->id,
                'title' => 'Mutasi Arsip',
                'name' => $archive->archive_id . ' - ' . $archive->name,
                'description' => $description . '.',
                'type' => 'archive',
                'method' => 'mutate',
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

            $letter->delete();

            return redirect()->route('archive.show', $letter->archive->archive_id)->with('success', 'Surat berhasil dihapus!');
        } else {
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('error', 'Surat gagal dihapus!');
        }
    }
}
