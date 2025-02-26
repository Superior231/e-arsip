<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        $approved_users = $users->where('status', 'approved');
        $suspended_users = $users->where('status', 'suspend');
        $superadmin = $users->where('roles', 'superadmin');
        $admin = $users->where('roles', 'admin');
        $staff = $users->where('roles', 'user');
        $histories = History::latest()->get();

        return view('pages.staff.index', [
            'title' => 'Staff - Putra Panggil Jaya',
            'navTitle' => 'Staff',
            'active' => 'staff',
            'users' => $users,
            'approved_users' => $approved_users,
            'suspended_users' => $suspended_users,
            'superadmin' => $superadmin,
            'admin' => $admin,
            'staff' => $staff,
            'histories' => $histories
        ]);
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255|unique:users',
            'password' => 'required|min:8|max:255',
        ], [
            'name.required' => 'Nama harus diisi!',
            'name.unique' => 'Nama sudah digunakan!',
            'name.max' => 'Nama maksimal 255 karakter!',
            'password.min' => 'Password minimal 8 karakter!',
            'password.max' => 'Password maksimal 255 karakter!',
            'password.required' => 'Password harus diisi!',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $staff = User::create($data);

        if ($staff) {
            History::create([
                'type_id' => $staff->id,
                'title' => "Membuat Staff Baru",
                'name' => $staff->name,
                'description' => "Staff baru telah dibuat oleh " . Auth::user()->name . ".\n" . "[" . $staff->name . "].",
                'type' => 'staff',
                'method' => 'create',
                'user_id' => Auth::user()->id,
            ]);
            return redirect()->route('staff.index')->with('success', 'Staff berhasil ditambahkan');
        } else {
            return redirect()->route('staff.index')->with('error', 'Staff gagal ditambahkan');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $id,
            'avatar' => 'image|mimes:jpg,jpeg,png,webp|max:10048',
            'password' => 'nullable|min:8',
        ], [
            'name.required' => 'Nama harus diisi!',
            'name.unique' => 'Nama sudah digunakan!',
            'name.max' => 'Nama maksimal 255 karakter!',
            'avatar.image' => 'Avatar harus berupa gambar!',
            'avatar.mimes' => 'Format avatar tidak valid! Gunakan format jpg, jpeg, png, atau webp!',
            'avatar.max' => 'Ukuran avatar tidak boleh lebih dari 10MB!',
            'password.min' => 'Password minimal 8 karakter!',
        ]);

        // Cek apakah pengguna ada dalam database
        $staff = User::find($id);
        if (!$staff) {
            return redirect()->route('staff.index')->with('error', 'Staff tidak ditemukan!');
        }
        if (Auth::user()->roles === 'admin' && ($staff->roles === 'admin' || $staff->roles === 'superadmin')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengubah staff ini!');
        }

        // Simpan nilai lama sebelum update
        $oldName = $staff->name;
        $oldAvatar = $staff->avatar;
        $oldStatus = $staff->status;
        $oldRoles = $staff->roles;

        // Update data pengguna
        $staff->name = $request->input('name', $staff->name);
        $staff->status = $request->input('status', $staff->status);
        if ($request->filled('password')) {
            $staff->password = Hash::make($request->input('password'));
        }

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if (!empty($oldAvatar) && Storage::disk('public')->exists('avatars/' . $oldAvatar)) {
                Storage::disk('public')->delete('avatars/' . $oldAvatar);
            }

            // Simpan avatar baru
            $file = $request->file('avatar');
            $fileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
            $image = Image::make($file)->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode('webp', 90);
            
            Storage::disk('public')->put('avatars/' . $fileName, (string) $image);
            $staff->avatar = $fileName;
        }

        // Jika superadmin, izinkan mengubah role dan status
        if (Auth::user()->roles === 'superadmin') {
            $staff->roles = $request->input('roles', $staff->roles);
        }

        // Pastikan admin tidak dapat merubah role
        if (Auth::user()->roles === 'admin') {
            $staff->roles = $oldRoles;
        }

        $staff->save();

        // Buat deskripsi perubahan
        $description = "Staff telah diupdate oleh " . Auth::user()->name . ".";
        $updates = [];

        if ($oldName !== $staff->name) {
            $updates[] = "Nama staff dari '$oldName' menjadi '$staff->name'";
        }
        if ($oldStatus !== $staff->status) {
            $updates[] = "Status staff dari '$oldStatus' menjadi '$staff->status'";
        }
        if (Auth::user()->roles === 'superadmin' && $oldRoles !== $staff->roles) {
            $updates[] = "Role staff dari '$oldRoles' menjadi '$staff->roles'";
        }
        if ($request->hasFile('avatar') && $oldAvatar !== $staff->avatar) {
            $updates[] = "Avatar staff dari '$oldAvatar' menjadi '$staff->avatar'";
        }
        if ($request->filled('password')) {
            $updates[] = "Password staff telah diubah";
        }

        if (!empty($updates)) {
            $description .= "\n" . implode(", \n", $updates);

            History::create([
                'type_id' => $staff->id,
                'title' => "Update Staff",
                'name' => $staff->name,
                'description' => $description . '.',
                'type' => 'staff',
                'method' => 'update',
                'user_id' => Auth::user()->id,
            ]);
            return redirect()->route('staff.index')->with('success', 'Staff berhasil diupdate!');
        } else {
            return redirect()->route('staff.index')->with('error', 'Tidak ada perubahan yang dilakukan!');
        }
    }
}
