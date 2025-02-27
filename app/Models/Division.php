<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $connection = 'mysql_earsip';
    protected $table = 'divisions';

    protected $fillable = ['name', 'place', 'status'];

    public function archives()
    {
        return $this->hasMany(Archive::class);
    }
}
