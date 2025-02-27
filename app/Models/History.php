<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $connection = 'mysql_earsip';
    protected $table = 'histories';

    protected $fillable = ['user_id', 'type_id', 'title', 'name', 'description', 'method','type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
