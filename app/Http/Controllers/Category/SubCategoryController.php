<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubCategoryController extends Controller
{
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
        $existingSubCategory = SubCategory::where('name', $request->name)->where('category_id', $request->category_id)->first();
        if ($existingSubCategory) {
            return redirect()->route('category.index', $existingSubCategory->category->slug)->with('error', 'Nama sub kategori sudah digunakan!');
        }

        $data = $request->all();
        $subCategory = SubCategory::create($data);

        if ($subCategory) {
            History::create([
                'type_id' => $subCategory->category->id,
                'title' => "Membuat Sub Kategori Baru",
                'name' => $subCategory->name,
                'description' => "Sub kategori baru telah dibuat oleh " . Auth::user()->name . "." . "\n" .
                "[" . $subCategory->category->name . "=>" . $subCategory->name . "].",
                'type' => 'subcategory',
                'method' => 'create',
                'user_id' => Auth::user()->id,
            ]);
            
            return redirect()->route('category.show', $subCategory->category->slug)->with('success', 'Sub kategori berhasil dibuat!');
        } else {
            return redirect()->route('category.index', $subCategory->category->slug)->with('error', 'Sub kategori gagal dibuat!');
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
        $existingSubCategory = SubCategory::where('name', $request->name)->where('id', '!=', $id)->first();
        if ($existingSubCategory) {
            return redirect()->route('category.index', $existingSubCategory->slug)->with('error', 'Nama sub kategori sudah digunakan!');
        }

        $data = $request->all();
        $subCategory = SubCategory::findOrFail($id);
        if (!$subCategory) {
            return redirect()->route('category.show', $subCategory->category->slug)->with('error', 'Sub kategori tidak ditemukan!');
        }

        // Simpan nilai lama sebelum update
        $oldName = $subCategory->name;
        $oldStatus = $subCategory->status;

        $isMutate = false;
        $isUpdate = false;
        $updates = [];

        if ($oldName !== $request->name) {
            $updates[] = "Nama sub kategori dari '$oldName' menjadi '$request->name'";
            $isUpdate = true;
        }
        if ($oldStatus !== $request->status) {
            $updates[] = "Status sub kategori dari '$oldStatus' menjadi '$request->status'";
            $isMutate = true;
        }

        // Update data dengan nilai baru
        $subCategory->name = $request->name;
        $subCategory->status = $request->status;
        $subCategory->save();

        // Tentukan metode yang digunakan
        $methods = [];
        if ($isMutate) {
            $methods[] = 'mutate';
        }
        if ($isUpdate) {
            $methods[] = 'update';
        }

        $title = count($methods) > 1 ? "Mutasi dan Update Sub Kategori" : (in_array('mutate', $methods) ? "Mutasi Sub Kategori" : "Update Sub Kategori");
        $method = implode(', ', $methods);

        // Cek apakah ada perubahan atau tidak
        if (!empty($updates)) {
            $description = "Sub Kategori telah diupdate oleh " . Auth::user()->name . ".";
            $description .= "\n" . implode(", \n", $updates);

            History::create([
                'type_id' => $subCategory->category->id,
                'title' => $title,
                'name' => $subCategory->name,
                'description' => $description . '.',
                'type' => 'subcategory',
                'method' => $method,
                'user_id' => Auth::user()->id,
            ]);
            return redirect()->route('category.show', $subCategory->category->slug)->with('success', 'Sub kategori berhasil diupdate!');
        } else {
            return redirect()->route('category.show', $subCategory->category->slug)->with('error', 'Tidak ada perubahan yang dilakukan!');
        }
    }
}
