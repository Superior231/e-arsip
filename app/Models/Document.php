<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $connection = 'mysql_earsip';
    protected $table = 'documents';

    protected $fillable = ['letter_id', 'file', 'type', 'status'];

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }
}
