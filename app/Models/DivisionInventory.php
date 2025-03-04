<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivisionInventory extends Model
{
    use HasFactory;

    protected $connection = 'mysql_inventory';
    protected $table = 'divisions';

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
