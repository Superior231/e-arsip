<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $connection = 'mysql_earsip';
    protected $table = 'sub_categories';

    protected $fillable = ['category_id', 'name', 'slug', 'status'];

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
            // Administrasi

            // Surat
            'Pemberitahuan' => 'PBT',
            'Pengajuan' => 'PGJ',
            'Pengumuman' => 'PMN',
            'Permohonan' => 'PMH',
            'Persetujuan' => 'PSJ',
            'Pertimbangan' => 'PTB',
            'Undangan' => 'UDG',
            'Lamaran' => 'LMR',

            // Faktur
            'Pajak' => 'PJK',
            'Pembelian' => 'PBL',
            'Penjualan' => 'PNJ',

            // Laporan

        ];

        return $nameToSlug[$name] ?? strtoupper(substr($name, 0, 3));
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
