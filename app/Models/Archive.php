<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $connection = 'mysql_earsip';
    protected $table = 'archives';

    protected $fillable = [
        'user_id',
        'division_id',
        'category_id',
        'archive_id',
        'archive_code',
        'name',
        'status',
        'image',
        'detail',
        'date',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function letters()
    {
        return $this->hasMany(Letter::class);
    }
}
