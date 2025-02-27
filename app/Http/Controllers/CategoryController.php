<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        $histories = History::where('type', 'category')->latest()->get();

        return view('pages.category.index', [
            'title' => 'Kategori - Putra Panggil Jaya',
            'navTitle' => 'Kategori',
            'active' => 'category',
            'categories' => $categories,
            'histories' => $histories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ], [
            'name.required' => 'Nama kategori wajib diisi!',
            'name.string' => 'Nama kategori harus berupa teks!',
            'name.max' => 'Nama kategori maksimal 255 karakter!',
            'name.unique' => 'Nama kategori sudah digunakan!',
        ]);

        // Cek apakah kategori sudah ada
        $existingCategory = Category::where('name', $request->name)->first();
        if ($existingCategory) {
            return redirect()->route('category.index', $existingCategory->slug)->with('error', 'Nama kategori sudah digunakan!');
        }

        $data = $request->all();
        $category = Category::create($data);

        if ($category) {
            History::create([
                'type_id' => $category->id,
                'title' => "Membuat Kategori Baru",
                'name' => $category->name,
                'description' => "Kategori baru telah dibuat oleh " . Auth::user()->name . ".",
                'type' => 'category',
                'method' => 'create',
                'user_id' => Auth::user()->id,
            ]);
            
            return redirect()->route('category.index')->with('success', 'Kategori berhasil dibuat!');
        } else {
            return redirect()->route('category.index')->with('error', 'Kategori gagal dibuat!');
        }
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$id
        ], [
            'name.required' => 'Nama kategori wajib diisi!',
            'name.string' => 'Nama kategori harus berupa teks!',
            'name.max' => 'Nama kategori maksimal 255 karakter!',
            'name.unique' => 'Nama kategori sudah digunakan!',
        ]);

        // Cek apakah kategori sudah ada
        $existingCategory = Category::where('name', $request->name)->where('id', '!=', $id)->first();
        if ($existingCategory) {
            return redirect()->route('category.index', $existingCategory->slug)->with('error', 'Nama kategori sudah digunakan!');
        }

        $data = $request->all();
        $category = Category::findOrFail($id);
        if (!$category) {
            return redirect()->route('category.index')->with('error', 'Kategory tidak ditemukan!');
        }

        // Simpan nilai lama sebelum update
        $oldName = $category->name;
        $oldStatus = $category->status;

        $isMutate = false;
        $isUpdate = false;
        $updates = [];

        if ($oldName !== $request->name) {
            $updates[] = "Nama kategori dari '$oldName' menjadi '$request->name'";
            $isUpdate = true;
        }
        if ($oldStatus !== $request->status) {
            $updates[] = "Status kategori dari '$oldStatus' menjadi '$request->status'";
            $isMutate = true;
        }

        // Update data dengan nilai baru
        $category->name = $request->name;
        $category->status = $request->status;
        $category->save();

        // Tentukan metode yang digunakan
        $methods = [];
        if ($isMutate) {
            $methods[] = 'mutate';
        }
        if ($isUpdate) {
            $methods[] = 'update';
        }

        $title = count($methods) > 1 ? "Mutasi dan Update Kategori" : (in_array('mutate', $methods) ? "Mutasi Kategori" : "Update Kategori");
        $method = implode(', ', $methods);

        // Cek apakah ada perubahan atau tidak
        if (!empty($updates)) {
            $description = "Kategori telah diupdate oleh " . Auth::user()->name . ".";
            $description .= "\n" . implode(", \n", $updates);

            History::create([
                'type_id' => $category->id,
                'title' => $title,
                'name' => $category->name,
                'description' => $description . '.',
                'type' => 'category',
                'method' => $method,
                'user_id' => Auth::user()->id,
            ]);
            return redirect()->route('category.index')->with('success', 'Kategori berhasil diupdate!');
        } else {
            return redirect()->route('category.index')->with('error', 'Tidak ada perubahan yang dilakukan!');
        }
    }
}
