<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $connection = 'mysql_inventory';
    protected $table = 'items';

    public function letters()
    {
        return $this->hasOne(Letter::class);
    }
}
