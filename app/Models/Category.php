<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $connection = 'mysql_earsip';
    protected $table = 'categories';

    protected $fillable = ['name', 'slug', 'status'];

    
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);

        // Jika slug belum diatur, maka generate otomatis
        if (!isset($this->attributes['slug']) || empty($this->attributes['slug'])) {
            $this->attributes['slug'] = $this->generateSlug($this->attributes['name']);
        }
    }

    public function generateSlug($name)
    {
        $nameToSlug = [
            'Administrasi' => 'ADM',
            'Berita Acara' => 'BAC',
            'Faktur' => 'FKT',
            'Surat' => 'SRT',
            'Surat Masuk' => 'SMK',
            'Surat Keluar' => 'SKL',
            'Laporan' => 'LPR',
            'Laporan Harian' => 'LPH',
            'Laporan Mingguan' => 'LMG',
            'Laporan Bulanan' => 'LBN',
            'Laporan Tahunan' => 'LTA',
            'Laporan Keuangan' => 'LKU',
            'Laporan Pajak' => 'LPJ',
            'Inventaris' => 'INV',
            'Pengadaan' => 'PGD',
            'Notulen' => 'NTL',
            'Memo' => 'MMO',
            'Proposal' => 'PRP',
            'Pengumuman' => 'PGM',
            'Permohonan' => 'PMH',
            'Panggilan' => 'PGL',
            'Surat Panggilan' => 'PGL',
            'Pemberitahuan' => 'PBM',
        ];

        return $nameToSlug[$name] ?? strtoupper(substr($name, 0, 3));
    }



    public function archives()
    {
        return $this->hasMany(Archive::class);
    }
}
