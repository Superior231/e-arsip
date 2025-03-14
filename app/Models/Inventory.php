<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $connection = 'mysql_inventory';
    protected $table = 'inventories';


    public function division()
    {
        return $this->belongsTo(DivisionInventory::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
