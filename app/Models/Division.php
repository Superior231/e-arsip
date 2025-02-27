<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql_inventory';
    protected $table = 'divisions';

    protected $fillable = ['name', 'place', 'status'];
}
