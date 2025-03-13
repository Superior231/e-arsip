<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Division;
use App\Models\History;
use App\Models\Letter;
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
            'tableTitle' => 'Daftar Semua Arsip',
            'active' => 'archive',
            'archives' => $archives,
            'histories' => $histories,
            'history_archive' => $history_archive,
        ]);
    }

    public function faktur_index()
    {
        $category = Category::where('name', 'Faktur')->first();
        if (!empty($category) && $category) {
            $archives = Archive::with('category')->where('category_id', $category->id)->latest()->get();
        } else {
            $archives = collect();
        }
        
        $histories = History::latest()->get();
        $history_archive = $histories->where('type', 'archive');
        
        return view('pages.archive.index', [
            'title' => 'Arsip Faktur - Putra Panggil Jaya',
            'navTitle' => 'Arsip Faktur',
            'tableTitle' => 'Daftar Semua Faktur',
            'active' => 'archive',
            'archives' => $archives,
            'histories' => $histories,
            'history_archive' => $history_archive,
        ]);
    }

    public function administrasi_index()
    {
        $category = Category::where('name', 'Administrasi')->first();
        if (!empty($category) && $category) {
            $archives = Archive::with('category')->where('category_id', $category->id)->latest()->get();
        } else {
            $archives = collect();
        }

        $histories = History::latest()->get();
        $history_archive = $histories->where('type', 'archive');
        
        return view('pages.archive.index', [
            'title' => 'Arsip Administrasi - Putra Panggil Jaya',
            'navTitle' => 'Arsip Administrasi',
            'tableTitle' => 'Daftar Semua Administrasi',
            'active' => 'archive',
            'archives' => $archives,
            'histories' => $histories,
            'history_archive' => $history_archive,
        ]);
    }

    public function laporan_index()
    {
        $categories = Category::where('name', 'LIKE', 'Laporan%')->pluck('id');

        if ($categories->isNotEmpty()) {
            $archives = Archive::with('category')->whereIn('category_id', $categories)->latest()->get();
        } else {
            $archives = collect();
        }
        
        $histories = History::latest()->get();
        $history_archive = $histories->where('type', 'archive');
        
        return view('pages.archive.index', [
            'title' => 'Arsip Laporan - Putra Panggil Jaya',
            'navTitle' => 'Arsip Laporan',
            'tableTitle' => 'Daftar Semua Laporan',
            'active' => 'archive',
            'archives' => $archives,
            'histories' => $histories,
            'history_archive' => $history_archive,
        ]);
    }

    public function create()
    {
        if (Auth::user()->roles === 'user') {
            return redirect()->route('archive.index')->with('error', 'Anda tidak memiliki akses untuk menambah arsip!');
        }

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

        // Ambil kategori dan kode kategori
        $category = Category::findOrFail($data['category_id']);
        $categoryCode = $category->slug;

        // Ambil arsip terakhir dengan kategori yang sama
        $latestArchive = Archive::where('category_id', $category->id)
                                ->latest('id')
                                ->first();

        // Hitung ID berikutnya dalam kategori
        if ($latestArchive) {
            preg_match('/\d+$/', $latestArchive->archive_id, $matches);
            $nextId = isset($matches[0]) ? intval($matches[0]) + 1 : 1;
        } else {
            $nextId = 1;
        }

        // Format archive_id: KODE.NUMBER (contoh: NTL.001)
        $data['archive_id'] = $categoryCode . '.' . sprintf('%03d', $nextId);

        // Generate archive_code dengan format yang lebih rapi
        $division = Division::findOrFail($data['division_id']);
        $archive_code = "{$division->name}/{$division->place}";
        $data['archive_code'] = "{$data['archive_id']}/{$archive_code}";

        $archive = Archive::create($data);

        if ($archive) {
            History::create([
                'type_id' => $archive->id,
                'title' => "Buat Arsip Baru",
                'name' => $archive->archive_id . ' - ' . $archive->name,
                'description' =>  "Arsip baru telah dibuat oleh " . Auth::user()->name . "\n" . "[{$archive->archive_id}/{$archive_code} => {$archive->name}].",
                'detail' =>  "Arsip baru telah dibuat oleh " . Auth::user()->name . "\n" . "[{$archive->archive_id}/{$archive_code} => {$archive->name}].",
                'type' => 'archive',
                'method' => 'create',
                'user_id' => Auth::user()->id,
            ]);

            return redirect()->route('archive.index')->with('success', 'Arsip berhasil dibuat!');
        } else {
            return redirect()->route('archive.index')->with('error', 'Arsip gagal dibuat!');
        }
    }

    public function edit(string $archive_id)
    {
        if (Auth::user()->roles === 'user') {
            return redirect()->route('archive.index')->with('error', 'Anda tidak memiliki akses untuk mengedit arsip!');
        }

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
        $archive = Archive::findOrFail($id);
        $updates = [];
        $detailUpdates = [];

        // Simpan nilai lama sebelum update
        $oldDivision = $archive->division;
        $oldCategory = $archive->category;
        $oldId = $archive->archive_id;
        $oldCode = $archive->archive_code;
        $oldName = $archive->name;
        $oldImage = $archive->image;
        $oldDetail = $archive->detail;
        $oldDate = $archive->date;

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


        // Ambil data division dan category baru
        $newDivision = Division::findOrFail($request->division_id);
        $newCategory = Category::findOrFail($request->category_id);

        if ($oldDivision->id !== $newDivision->id) {
            $updates[] = "Divisi dari '{$oldDivision->name} ({$oldDivision->place})' menjadi '{$newDivision->name} ({$newDivision->place})'";
            $detailUpdates[] = "Divisi dari '{$oldDivision->name} ({$oldDivision->place})' menjadi '{$newDivision->name} ({$newDivision->place})'";
        }
        if ($oldCategory->id !== $newCategory->id) {
            $updates[] = "Kategori dari '{$oldCategory->name}' menjadi '{$newCategory->name}'";
            $detailUpdates[] = "Kategori dari '{$oldCategory->name}' menjadi '{$newCategory->name}'";
        }
        if ($oldName !== $request->name) {
            $updates[] = "Nama arsip dari '$oldName' menjadi '$request->name'";
            $detailUpdates[] = "Nama arsip dari '$oldName' menjadi '$request->name'";
        }
        if ($oldDate !== $request->date) {
            $updates[] = "Tanggal pengadaan arsip dari '$oldDate' menjadi '$request->date'";
            $detailUpdates[] = "Tanggal pengadaan arsip dari '$oldDate' menjadi '$request->date'";
        }
        if ($oldDetail !== $request->detail) {
            $updates[] = "Detail arsip diupdate";
            $detailUpdates[] = "Detail arsip dari '$oldDetail' menjadi '$request->detail'";
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
                $detailUpdates[] = "Gambar arsip dari '$oldImage' menjadi '$fileName'";
            }

            Storage::disk('public')->put('archive/' . $fileName, (string) $image);
            $archive->image = $fileName;
        }


        // Update archive_id jika kategori berubah
        if ($oldCategory->id !== $newCategory->id) {
            $categoryCode = strtoupper($newCategory->code ?? $newCategory->slug);
            $latestArchive = Archive::where('archive_id', 'like', "$categoryCode.%")
                                    ->latest('archive_id')
                                    ->first();

            if ($latestArchive) {
                preg_match('/\d+$/', $latestArchive->archive_id, $matches);
                $nextId = isset($matches[0]) ? intval($matches[0]) + 1 : 1;
            } else {
                $nextId = 1;
            }

            $archive->archive_id = $categoryCode . '.' . sprintf('%03d', $nextId);
            $updates[] = "ID arsip berubah menjadi {$archive->archive_id}";
            $detailUpdates[] = "ID arsip berubah menjadi {$archive->archive_id}";
        }
        if ($oldDivision->id !== $newDivision->id || $oldCategory->id !== $newCategory->id) {
            $archive_code = $archive->archive_id . '/' . $newDivision->name . '/' . $newDivision->place;
            $archive->archive_code = $archive_code;

            $updates[] = "Kode arsip berubah menjadi '$archive_code'";
            $detailUpdates[] = "Kode arsip berubah menjadi '$archive_code'";
        }


        // Simpan perubahan data
        $archive->division_id = $newDivision->id;
        $archive->category_id = $newCategory->id;
        $archive->archive_code = $archive_code;
        $archive->name = $request->name;
        $archive->detail = $request->detail;
        $archive->date = $request->date;
        $archive->save();

        $description = "Arsip telah diupdate oleh " . Auth::user()->name . ".";
        $descriptionDetail = "Arsip telah diupdate oleh " . Auth::user()->name . ".";

        // Cek apakah ada perubahan atau tidak
        if (!empty($updates && $detailUpdates)) {
            $description .= "\n" . implode(", \n", $updates);
            $descriptionDetail .= "\n" . implode(", \n", $detailUpdates);

            History::create([
                'type_id' => $archive->id,
                'title' => 'Update Arsip',
                'name' => $archive->archive_id . ' - ' . $archive->name,
                'description' => $description . '.',
                'detail' => $descriptionDetail . '.',
                'type' => 'archive',
                'method' => 'update',
                'user_id' => Auth::user()->id,
            ]);
            return redirect()->route('archive.index')->with('success', 'Arsip berhasil diupdate!');
        } else {
            return redirect()->route('archive.index')->with('error', 'Tidak ada perubahan yang dilakukan!');
        }
    }

    public function show(string $archive_id)
    {
        $archive = Archive::where('archive_id', $archive_id)->with('letters')->firstOrFail();
        $archives = Archive::where('archive_id', $archive_id)->firstOrFail();
        $histories = History::latest()->get();
        if ($archives->category->name === 'Memo' || $archives->category->name === 'Notulen') {
            $history_letter = $histories->whereIn('type', ['memo', 'notulen'])->whereIn('type_id', $archives->id);
        } else {
            $history_letter = $histories->whereIn('type', ['letter_in', 'letter_out', 'faktur'])->whereIn('type_id', $archives->id);
        }
        

        return view('pages.archive.show', [
            'title' => 'Detail Arsip',
            'navTitle' => 'Detail Arsip',
            'active' => 'archive',
            'archive' => $archive,
            'histories' => $histories,
            'history_letter' => $history_letter,
        ]);
    }

    public function inventory_detail(string $no_letter)
    {
        $letter = Letter::where('no_letter', $no_letter)->with('item.inventory')->firstOrFail();
        $item = $letter->item;
        $inventory = $item ? $item->inventory : null;
        $histories = History::latest()->get();

        return view('pages.archive.inventory-detail', [
            'title' => 'Detail Inventory',
            'navTitle' => 'Detail Inventory',
            'active' => 'archive',
            'letter' => $letter,
            'item' => $item,
            'inventory' => $inventory,
            'histories' => $histories
        ]);
    }

    public function delete_archive(Request $request, $id)
    {
        if (Auth::user()->roles === 'user') {
            return redirect()->route('archive.index')->with('error', 'Anda tidak memiliki akses untuk menghapus arsip!');
        }

        $archive = Archive::findOrFail($id);
        $oldCode = $archive->archive_id;
        $oldName = $archive->name;
        $archive->status = $request->status;
        $archive->save();
    
        $description = "Arsip [" . $oldCode . ' - ' . $oldName . "] dihapus oleh " . Auth::user()->name . ".";

        if ($archive) {
            History::create([
                'type_id' => $archive->id,
                'title' => "Hapus Arsip",
                'name' => $oldCode . ' - ' . $oldName,
                'description' => $description,
                'detail' => $description,
                'type' => 'archive',
                'method' => 'delete',
                'user_id' => Auth::user()->id,
            ]);
            return redirect()->route('archive.index')->with('success', 'Arsip berhasil dihapus!');
        } else {
            return redirect()->route('archive.index')->with('error', 'Arsip gagal dihapus!');
        }
    }
}
