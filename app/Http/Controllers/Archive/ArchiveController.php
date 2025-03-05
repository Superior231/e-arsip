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
        $category = Category::where('name', 'Laporan')->first();
        if (!empty($category) && $category) {
            $archives = Archive::with('category')->where('category_id', $category->id)->latest()->get();
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
        $archive_code = $division->name . '/' . $division->place . '/' . $category->slug;
        $data['archive_code'] = $data['archive_id'] . '.' . $archive_code;

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

            return redirect()->route('archive.index')->with('success', 'Arsip berhasil dibuat!');
        } else {
            return redirect()->route('archive.index')->with('error', 'Arsip gagal dibuat!');
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
        $oldDetail = $archive->detail;
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
        if ($oldDetail !== $request->detail) {
            $updates[] = "Detail arsip dari '$oldDetail' menjadi '$request->detail'";
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
            $archive_code = $newDivision->name . '/' . $newDivision->place . '/' . $newCategory->slug;
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
        $archive->detail = $request->detail;
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

    public function show(string $archive_id)
    {
        $archive = Archive::where('archive_id', $archive_id)->with('letters')->firstOrFail();
        $archives = Archive::where('archive_id', $archive_id)->firstOrFail();
        $histories = History::latest()->get();
        $history_letter = $histories->where('type', 'letter')->whereIn('type_id', $archives->id);

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

    public function destroy(string $id)
    {
        $archive = Archive::findOrFail($id);
        $oldCode = $archive->archive_id;
        $oldName = $archive->name;

        if ($archive->status !== 'approve') {
            $archive->delete();
    
            $description = "Arsip [" . $oldCode . ' - ' . $oldName . "] dihapus oleh " . Auth::user()->name . ".";
            
            if ($archive) {
                History::create([
                    'type_id' => $archive->id,
                    'title' => "Hapus Arsip",
                    'name' => $oldCode . ' - ' . $oldName,
                    'description' => $description,
                    'type' => 'archive',
                    'method' => 'delete',
                    'user_id' => Auth::user()->id,
                ]);
    
                return redirect()->route('archive.index')->with('success', 'Arsip berhasil dihapus!');
            } else {
                return redirect()->route('archive.index')->with('error', 'Arsip gagal dihapus!');
            }
        } else {
            return redirect()->route('archive.index')->with('error', 'Arsip tidak dapat dihapus karena sudah diapprove!');
        }
    }
}
