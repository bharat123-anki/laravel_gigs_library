<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Book extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable =['book_name','author','cover_image','stock_quantity'];
   private function book_user_rented(){
        return $this->hasMany(Book::class,'book_id');
    }
}
