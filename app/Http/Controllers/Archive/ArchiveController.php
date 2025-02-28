<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Division;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ArchiveController extends Controller
{
    public function index()
    {
        $archives = Archive::latest()->get();
        $histories = History::latest()->get();
        $history_archive = $histories->where('type', 'archive');
        
        return view('pages.archive.index', [
            'title' => 'Semua Arsip - Putra Panggil Jaya',
            'navTitle' => 'Semua Arsip',
            'active' => 'archive',
            'archives' => $archives,
            'histories' => $histories,
            'history_archive' => $history_archive,
        ]);
    }

    public function create()
    {
        $categories = Category::latest()->get();
        $divisions = Division::latest()->get();
        $histories = History::latest()->get();

        return view('pages.archive.create', [
            'title' => 'Tambah Arsip - Putra Panggil Jaya',
            'navTitle' => 'Tambah Arsip',
            'active' => 'archive',
            'categories' => $categories,
            'divisions' => $divisions,
            'histories' => $histories,
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required',
            'division_id' => 'required',
            'date' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,webp|max:10048',
        ], [
            'category_id' => 'Kategori harus dipilih!',
            'division_id' => 'Divisi harus dipilih!',
            'date.required' => 'Tanggal harus diisi!',
            'name.required' => 'Nama arsip harus diisi!',
            'name.max' => 'Nama arsip maksimal 255 karakter!',
            'image.image' => 'File harus berupa gambar!',
            'image.mimes' => 'File harus berekstensi jpg, jpeg, png, atau webp!',
            'image.max' => 'Ukuran file maksimal 10MB!',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
            $image = Image::make($file)->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode('webp', 90);

            Storage::disk('public')->put('archive/' . $fileName, (string) $image);
            $data['image'] = $fileName;
        }

        // Generate code
        $latestArchive = Archive::latest('id')->first();
        $nextId = $latestArchive ? $latestArchive->id + 1 : 1;
        $data['archive_id'] = 'PPJ.' . sprintf('%03d', $nextId); 

        // Generate code archive
        $division = Division::find($data['division_id']);
        $category = Category::find($data['category_id']);
        $archive_code = $division->name . '/' . $division->place . '/' . $category->slug . '/' .  $data['name'];
        $data['archive_code'] = $archive_code;

        $archive = Archive::create($data);

        if ($archive) {
            History::create([
                'type_id' => $archive->id,
                'title' => "Buat Arsip Baru",
                'name' => $archive->archive_id . ' - ' . $archive->name,
                'description' =>  "Arsip baru telah dibuat oleh " . Auth::user()->name . "\n" . "[{$archive->archive_id}/{$archive_code} => {$archive->name}].",
                'type' => 'archive',
                'method' => 'create',
                'user_id' => Auth::user()->id,
            ]);

            return redirect()->route('archive.index')->with('success', 'Arsip berhasil ditambahkan!');
        } else {
            return redirect()->route('archive.index')->with('error', 'Arsip gagal ditambahkan!');
        }
    }

    public function edit(string $archive_id)
    {
        $archive = Archive::where('archive_id', $archive_id)->with('division', 'category')->firstOrFail();
        $categories = Category::latest()->get();
        $divisions = Division::latest()->get();
        $histories = History::latest()->get();

        return view('pages.archive.edit', [
            'title' => 'Edit Arsip',
            'navTitle' => 'Edit Arsip',
            'active' => 'archive',
            'archive' => $archive,
            'categories' => $categories,
            'divisions' => $divisions,
            'histories' => $histories
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required',
            'division_id' => 'required',
            'date' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,webp|max:10048',
        ], [
            'category_id' => 'Kategori harus dipilih!',
            'division_id' => 'Divisi harus dipilih!',
            'date.required' => 'Tanggal harus diisi!',
            'name.required' => 'Nama arsip harus diisi!',
            'name.max' => 'Nama arsip maksimal 255 karakter!',
            'image.image' => 'File harus berupa gambar!',
            'image.mimes' => 'File harus berekstensi jpg, jpeg, png, atau webp!',
            'image.max' => 'Ukuran file maksimal 10MB!',
        ]);

        $archive = Archive::findOrFail($id);

        // Simpan nilai lama sebelum update
        $oldDivision = $archive->division;
        $oldCategory = $archive->category;
        $oldId = $archive->archive_id;
        $oldCode = $archive->archive_id . '/' . $archive->archive_code;
        $oldName = $archive->name;
        $oldStatus = $archive->status;
        $oldImage = $archive->image;
        $oldDescription = $archive->description;
        $oldDate = $archive->date;

        // Ambil data division dan category baru
        $newDivision = Division::findOrFail($request->division_id);
        $newCategory = Category::findOrFail($request->category_id);

        $isMutate = false;
        $isUpdate = false;
        $updates = [];

        if ($oldDivision->id !== $newDivision->id) {
            $updates[] = "Divisi dari '{$oldDivision->name} ({$oldDivision->place})' menjadi '{$newDivision->name} ({$newDivision->place})'";
            $isMutate = true;
        }
        if ($oldCategory->id !== $newCategory->id) {
            $updates[] = "Kategori dari '{$oldCategory->name}' menjadi '{$newCategory->name}'";
            $isUpdate = true;
        }
        if ($oldName !== $request->name) {
            $updates[] = "Nama arsip dari '$oldName' menjadi '$request->name'";
            $isUpdate = true;
        }
        if ($oldDate !== $request->date) {
            $updates[] = "Tanggal pengadaan arsip dari '$oldDate' menjadi '$request->date'";
            $isUpdate = true;
        }
        if ($oldDescription !== $request->description) {
            $updates[] = "Detail arsip dari '$oldDescription' menjadi '$request->description'";
            $isUpdate = true;
        }

        // Cek perubahan status (hanya superadmin yang bisa mengubahnya)
        if (Auth::user()->roles == 'superadmin' && $oldStatus !== $request->status) {
            $updates[] = "Status arsip dari '$oldStatus' menjadi '$request->status'";
            $isMutate = true;
            $archive->status = $request->status;
        }

        // Cek perubahan gambar
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
            $image = Image::make($file)->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode('webp', 90);

            if ($oldImage !== $fileName) {
                $updates[] = "Gambar arsip dari '$oldImage' menjadi '$fileName'";
                $isUpdate = true;
            }

            Storage::disk('public')->put('archive/' . $fileName, (string) $image);
            $archive->image = $fileName;
        }

        // Update archive_code jika division, category, atau name berubah
        if ($oldDivision->id !== $newDivision->id || $oldCategory->id !== $newCategory->id || $oldName !== $request->name) {
            $archive_code = $newDivision->name . '/' . $newDivision->place . '/' . $newCategory->slug . '/' . $request->name;
            $archive->archive_code = $archive_code;

            $updates[] = "Kode arsip dari '$oldCode' menjadi '$oldId/$archive_code'";
            $isMutate = true;
        }

        // Jika ada perubahan selain status dan status saat ini bukan pending, ubah status menjadi pending
        if ($isUpdate && $oldStatus !== 'pending') {
            $archive->status = 'pending';
            $isMutate = true;
            $updates[] = "Status arsip otomatis berubah menjadi 'pending' karena ada perubahan data";
        }

        // Simpan perubahan data
        $archive->division_id = $newDivision->id;
        $archive->category_id = $newCategory->id;
        $archive->name = $request->name;
        $archive->description = $request->description;
        $archive->date = $request->date;
        $archive->save();

        // Tentukan method yang digunakan
        $methods = [];
        if ($isMutate) {
            $methods[] = 'mutate';
        }
        if ($isUpdate) {
            $methods[] = 'update';
        }

        $title = count($methods) > 1 ? "Mutasi dan Update Arsip" : (in_array('mutate', $methods) ? "Mutasi Arsip" : "Update Arsip");
        $method = implode(', ', $methods);
        $description = "Arsip telah diupdate oleh " . Auth::user()->name . ".";

        // Cek apakah ada perubahan atau tidak
        if (!empty($updates)) {
            $description .= "\n" . implode(", \n", $updates);

            History::create([
                'type_id' => $archive->id,
                'title' => $title,
                'name' => $archive->archive_id . ' - ' . $archive->name,
                'description' => $description . '.',
                'type' => 'archive',
                'method' => $method,
                'user_id' => Auth::user()->id,
            ]);
            return redirect()->route('archive.index')->with('success', 'Arsip berhasil diupdate!');
        } else {
            return redirect()->route('archive.index')->with('error', 'Tidak ada perubahan yang dilakukan!');
        }
    }
}
