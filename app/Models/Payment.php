<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'totalPaid',
    ];
public function books()
{
    return $this->hasMany(Book::class);
}

}
