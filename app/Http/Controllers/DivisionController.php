<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::latest()->get();
        $histories = History::latest()->get();
        $history_division = $histories->where('type', 'division');

        return view('pages.division.index', [
            'title' => 'Divisi - Putra Panggil Jaya',
            'navTitle' => 'Divisi',
            'active' => 'division',
            'divisions' => $divisions,
            'histories' => $histories,
            'history_division' => $history_division
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'place' => 'required',
        ], [
            'name.required' => 'Harap isi Nama Divisi',
            'place.required' => 'Harap isi Tempat Divisi',
        ]);

        $data = $request->all();
        $division = Division::create($data);

        if ($division) {
            History::create([
                'type_id' => $division->id,
                'title' => "Membuat Divisi Baru",
                'name' => $division->name . ' - ' . $division->place,
                'description' => "Divisi baru telah dibuat oleh " . Auth::user()->name . ".",
                'type' => 'division',
                'method' => 'create',
                'user_id' => Auth::user()->id,
            ]);
            return redirect()->route('division.index')->with('success', 'Divisi berhasil dibuat!');
        } else {
            return redirect()->route('division.index')->with('error', 'Divisi gagal dibuat!');
        }
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'place' => 'required',
            'status' => 'required',
        ], [
            'name.required' => 'Harap isi Nama Divisi',
            'place.required' => 'Harap isi Tempat Divisi',
            'status.required' => 'Harap isi Status Divisi',
        ]);

        $division = Division::find($id);
        if (!$division) {
            return redirect()->route('division.index')->with('error', 'Divisi tidak ditemukan!');
        }

        // Simpan nilai lama sebelum update
        $oldName = $division->name;
        $oldPlace = $division->place;
        $oldStatus = $division->status;

        // Variabel untuk pengecekan perubahan
        $isMutate = false;
        $isUpdate = false;
        $updates = [];

        // Cek perubahan status (mutate)
        if ($oldStatus !== $request->status) {
            $updates[] = "Status divisi dari '$oldStatus' menjadi '$request->status'";
            $isMutate = true;
        }

        // Cek perubahan name & place (update)
        if ($oldName !== $request->name) {
            $updates[] = "Nama divisi dari '$oldName' menjadi '$request->name'";
            $isUpdate = true;
        }
        if ($oldPlace !== $request->place) {
            $updates[] = "Tempat divisi dari '$oldPlace' menjadi '$request->place'";
            $isUpdate = true;
        }

        // Update data dengan nilai baru
        $division->name = $request->name;
        $division->place = $request->place;
        $division->status = $request->status;
        $division->save();

        // Tentukan metode yang digunakan
        $methods = [];
        if ($isMutate) {
            $methods[] = 'mutate';
        }
        if ($isUpdate) {
            $methods[] = 'update';
        }

        // Tentukan judul berdasarkan metode yang digunakan
        $title = count($methods) > 1 ? "Mutasi dan Update Divisi" : (in_array('mutate', $methods) ? "Mutasi Divisi" : "Update Divisi");
        $method = implode(', ', $methods);
        $description = "Divisi telah diupdate oleh " . Auth::user()->name . ".";


        if (!empty($updates)) {
            $description .= "\n" . implode(", \n", $updates);

            History::create([
                'type_id' => $division->id,
                'title' => $title,
                'name' => $division->name . ' - ' . $division->place,
                'description' => $description . '.',
                'type' => 'division',
                'method' => $method,
                'user_id' => Auth::user()->id,
            ]);
            return redirect()->route('division.index')->with('success', 'Divisi berhasil diupdate!');
        } else {
            return redirect()->route('division.index')->with('error', 'Tidak ada perubahan yang dilakukan!');
        }
    }
}
