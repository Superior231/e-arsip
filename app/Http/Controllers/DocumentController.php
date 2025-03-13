<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\History;
use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file.*' => 'mimes:jpg,jpeg,png,webp,doc,docx,pdf|max:10048',
        ], [
            'file.*.mimes' => 'File harus berekstensi jpg, jpeg, png, webp, doc, docx, atau pdf!',
            'file.*.max' => 'Ukuran file maksimal 10MB!',
        ]);

        $letter = Letter::findOrFail($request->letter_id);

        if ($request->hasFile('file')) {
            // Cek apakah status letter bukan 'pending'
            if ($letter->status !== 'pending') {
                $letter->status = 'pending';
                $letter->save();
                $updates[] = "Status otomatis berubah menjadi 'pending' karena ada penambahan dokumen pada surat";

                $description = "Surat telah diupdate oleh " . Auth::user()->name . ".";
                if (!empty($updates)) {
                    $description .= "\n" . implode(", \n", $updates);
                }

                History::create([
                    'type_id' => $letter->archive->id,
                    'title' => 'Update Status',
                    'name' => $letter->archive->archive_id . ' - ' . $letter->archive->name,
                    'description' => $description . '.',
                    'detail' => $description . '.',
                    'type' => $letter->type,
                    'method' => 'update status',
                    'user_id' => Auth::user()->id,
                ]);
            }

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
                    'letter_id' => $request->letter_id,
                    'file' => $fileName,
                    'type' => $fileType,
                ]);

                History::create([
                    'type_id' => $letter->archive->id,
                    'title' => "Update " . $letter->type,
                    'name' => $letter->no_letter . ' - ' . $letter->name,
                    'description' =>  "Surat telah diupdate oleh " . Auth::user()->name . "." . "\n" . "Dokumen baru => " . $file->getClientOriginalName() . ".",
                    'detail' =>  "Surat telah diupdate oleh " . Auth::user()->name . "." . "\n" . "Dokumen baru => " . $file->getClientOriginalName() . ".",
                    'type' => $letter->type,
                    'method' => 'update',
                    'user_id' => Auth::user()->id,
                ]);
            }
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('success', 'Dokumen surat berhasil ditambahkan!');
        } else {
            return redirect()->route('archive.show', $letter->archive->archive_id)->with('error', 'Tidak ada perubahan yang dilakukan!');
        }
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'file' => 'mimes:jpg,jpeg,png,webp,doc,docx,pdf|max:10048',
        ], [
            'file.mimes' => 'File harus berekstensi jpg, jpeg, png, webp, doc, docx, atau pdf!',
            'file.max' => 'Ukuran file maksimal 10MB!',
        ]);

        $document = Document::findOrFail($id);
        $letter = Letter::findOrFail($request->letter_id);
        $updates = [];

        if ($request->hasFile('file')) {
            // Ubah status dokumen lama menjadi delete
            $oldDocument = $document->file;
            $document->status = 'delete';
            $document->save();

            // Cek apakah status letter bukan 'pending'
            if ($letter->status !== 'pending') {
                $letter->status = 'pending';
                $letter->save();
                $updates[] = "Status otomatis berubah menjadi 'pending' karena ada penambahan dokumen pada surat";

                $description = "Surat telah diupdate oleh " . Auth::user()->name . ".";
                if (!empty($updates)) {
                    $description .= "\n" . implode(", \n", $updates);
                }

                History::create([
                    'type_id' => $letter->archive->id,
                    'title' => 'Update Status',
                    'name' => $letter->archive->archive_id . ' - ' . $letter->archive->name,
                    'description' => $description . '.',
                    'detail' => $description . '.',
                    'type' => $letter->type,
                    'method' => 'update status',
                    'user_id' => Auth::user()->id,
                ]);
            }

            $uploadedFile = $request->file('file');
            $originalExtension = $uploadedFile->getClientOriginalExtension();
            $fileName = time() . '_' . pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $originalExtension;
            $imageExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($originalExtension, $imageExtensions)) {
                $fileName = time() . '_' . pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
                $image = Image::make($uploadedFile);
                $image->resize(500, 500, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode('webp', 90);

                Storage::disk('public')->put('documents/' . $fileName, $image->stream()->__toString());
                $fileType = 'image';
            } else {
                Storage::disk('public')->put('documents/' . $fileName, file_get_contents($uploadedFile));
                $fileType = 'file';
            }

            // Tambah dokumen baru
            Document::create([
                'letter_id' => $request->letter_id,
                'file' => $fileName,
                'type' => $fileType,
            ]);

            History::create([
                'type_id' => $document->letter->archive->id,
                'title' => "Update " . $document->letter->type,
                'name' => $document->letter->no_letter . ' - ' . $document->letter->name,
                'description' =>  "Dokumen surat telah diupdate oleh " . Auth::user()->name . "." . "\n" . "Dokumen lama => " . $oldDocument . " ke dokumen baru => " . $fileName,
                'detail' =>  "Dokumen surat telah diupdate oleh " . Auth::user()->name . "." . "\n" . "Dokumen lama => " . $oldDocument . " ke dokumen baru => " . $fileName,
                'type' => $document->letter->type,
                'method' => 'update',
                'user_id' => Auth::user()->id,
            ]);

            return redirect()->route('archive.show', $document->letter->archive->archive_id)
                ->with('success', 'Dokumen surat berhasil diupdate!');
        } else {
            return redirect()->route('archive.show', $document->letter->archive->archive_id)
                ->with('error', 'Tidak ada perubahan yang dilakukan!');
        }
    }
    
    public function delete_document(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $oldName = $document->file;
        $document->status = $request->status;
        $document->save();

        History::create([
            'type_id' => $document->letter->archive->id,
            'title' => "Update " . $document->letter->type,
            'name' => $document->letter->no_letter . ' - ' . $document->letter->name,
            'description' => "Dokumen surat '$oldName' telah dihapus oleh " . Auth::user()->name . ".",
            'detail' => "Dokumen surat '$oldName' telah dihapus oleh " . Auth::user()->name . ".",
            'type' => $document->letter->type,
            'method' => 'delete',
            'user_id' => Auth::user()->id,
        ]);

        return redirect()->route('archive.show', $document->letter->archive->archive_id)
            ->with('success', 'Dokumen surat berhasil dihapus!');
    }
}
