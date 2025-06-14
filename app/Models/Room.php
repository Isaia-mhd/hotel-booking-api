<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function classe()
    {
        return $this->belongsTo(Classe::class, "class_id");
    }

    public function book()
    {
        return $this->belongsToMany(Book::class,"room_id");
    }

    protected $casts = [
        'price' => 'float',
    ];
}
