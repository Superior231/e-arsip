<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $histories = History::latest()->get();

        return view('pages.profile.index', [
            'title' => 'My Profile',
            'navTitle' => 'Profile',
            'active' => 'profile',
            'user' => $user,
            'histories' => $histories
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $id,
            'avatar' => 'image|mimes:jpg,jpeg,png,webp|max:10048',
            'password' => 'nullable|min:8|max:255',
        ], [
            'name.required' => 'Nama harus diisi!',
            'name.unique' => 'Nama sudah digunakan!',
            'name.string' => 'Nama harus berupa teks!',
            'name.max' => 'Nama maksimal 255 karakter!',
            'avatar.image' => 'Avatar harus berupa gambar!',
            'avatar.mimes' => 'Format avatar tidak valid! Gunakan format jpg, jpeg, png, atau webp!',
            'avatar.max' => 'Ukuran avatar tidak boleh lebih dari 10MB!',
            'password.min' => 'Password minimal 8 karakter!',
            'password.max' => 'Password maksimal 255 karakter!',
        ]);

        // Cek apakah pengguna ada dalam database
        $user = User::find($id);
        if (!$user) {
            return redirect()->route('profile.index')->with('error', 'User tidak ditemukan!');
        }
        if (Auth::id() !== (int) $id) {
            return redirect()->route('profile.index')->with('error', 'Oops, terjadi kesalahan!');
        }

        // Simpan nilai lama sebelum update
        $oldName = $user->name;
        $oldAvatar = $user->avatar;
        $oldStatus = $user->status;
        $oldRoles = $user->roles;

        // Update data pengguna
        $user->name = $request->input('name', $user->name);
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
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
            $user->avatar = $fileName;
        }

        // Jika superadmin, izinkan mengubah role dan status
        if (Auth::user()->roles === 'superadmin') {
            $user->status = $request->input('status', $user->status);
            $user->roles = $request->input('roles', $user->roles);
        }

        $user->save();

        // Buat deskripsi perubahan
        $description = "Profile telah diupdate oleh " . Auth::user()->name . ".";
        $updates = [];

        if ($oldName !== $user->name) {
            $updates[] = "Nama profile dari '$oldName' menjadi '$user->name'";
        }
        if (Auth::user()->roles === 'superadmin' && $oldRoles !== $user->roles) {
            $updates[] = "Role profile dari '$oldRoles' menjadi '$user->roles'";
        }
        if (Auth::user()->roles === 'superadmin' && $oldStatus !== $user->status) {
            $updates[] = "Status profile dari '$oldStatus' menjadi '$user->status'";
        }
        if ($request->hasFile('avatar') && $oldAvatar !== $user->avatar) {
            $updates[] = "Avatar profile dari '$oldAvatar' menjadi '$user->avatar'";
        }
        if ($request->filled('password')) {
            $updates[] = "Password profile telah diubah";
        }

        if (!empty($updates)) {
            $description .= "\n" . implode(", \n", $updates);

            History::create([
                'type_id' => $user->id,
                'title' => "Update Profile",
                'name' => $user->name,
                'description' => $description . '.',
                'type' => 'staff',
                'method' => 'update',
                'user_id' => Auth::user()->id,
            ]);
            return redirect()->route('profile.index')->with('success', 'Profil berhasil diupdate!');
        } else {
            return redirect()->route('profile.index')->with('error', 'Tidak ada perubahan yang dilakukan!');
        }
    }

    public function deleteAvatar($id)
    {
        if (Auth::id() !== (int) $id) {
            return redirect()->route('profile.index')->with('error', 'Oops, terjadi kesalahan!');
        }
    
        $user = User::find($id);
        if (!$user) {
            return redirect()->route('profile.index')->with('error', 'User tidak ditemukan!');
        }

        $oldAvatar = $user->avatar;

        // Hapus file avatar jika ada
        if (!empty($user->avatar)) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
            $user->avatar = null;
        }

        $user->save();

        History::create([
            'type_id' => $user->id,
            'title' => "Hapus Avatar",
            'name' => $user->name,
            'description' => "Avatar {$oldAvatar} telah dihapus oleh " . Auth::user()->name . '.',
            'type' => 'staff',
            'method' => 'delete',
            'user_id' => Auth::user()->id,
        ]);

        if ($user) {
            return redirect()->back()->with('success', 'Avatar berhasil dihapus!');
        } else {
            return redirect()->route('profile.index')->with('error', 'Avatar gagal dihapus!');
        }
    }
}
