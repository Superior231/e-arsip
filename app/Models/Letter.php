<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    use HasFactory;

    protected $connection = 'mysql_earsip';
    protected $table = 'letters';

    protected $fillable = [
        'user_id',
        'archive_id',
        'item_id',
        'letter_id',
        'no_letter',
        'letter_code',
        'type',
        'name',
        'content',
        'detail',
        'date',
    ];


    public function childrens()
    {
        return $this->hasMany(Letter::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function archive()
    {
        return $this->belongsTo(Archive::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
