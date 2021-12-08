<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BooksUserRented extends Model
{
  use HasFactory;
  protected $fillable = [
    'id', 'user_id', 'book_id', 'rented_date', 'returned_date', 'total_days', 'is_book_returned', 'created_at', 'updated_at'
  ];
  protected $table = 'books_user_renteds';

  public function user()
  {
    return  $this->belongsTo(User::class, 'user_id');
  }
  public function book()
  {
    return $this->belongsTo(Book::class, 'book_id');
  }
  public function bookAssignedToUser($user_id, $book_id)
  {
    DB::beginTransaction();
    try {
      $return_array = [];
      $bookAllocated = DB::table('books')->where('id', $book_id)->first();
      if (!empty($bookAllocated)) {
        if ($bookAllocated->stock_quantity != "" || $bookAllocated->stock_quantity != "0") {
          $insert_data['user_id'] = $user_id;
          $insert_data['book_id'] = $book_id;
          $insert_data['rented_date'] = date("Y-m-d");
          BooksUserRented::create($insert_data);
          $upd_stock = $bookAllocated->stock_quantity - 1;
          $bookAllocated = DB::table('books')->where('id', $book_id)->update(array('stock_quantity' => $upd_stock));
          if ($bookAllocated) {
            $return_array =  ['code' => '200', 'message' => 'Book Allotted Sucessfully'];
          } else {
            $return_array = ['code' => '500', 'message' => 'Something Went Wrong'];
          }
        } else {
          $return_array = ['code' => '203', 'message' => 'Book Out Of Stock'];
        }
      } else {
        $return_array = ['code' => '203', 'message' => 'No Such Book Found'];
      }
      DB::commit();
      return $return_array;
    } catch (\Exception $e) {
      DB::rollback();
      var_dump($e->getMessage());
      return false;
    }
  }

  public function bookReturnedByUser($user_id = '', $book_id)
  {
    DB::beginTransaction();
    try {
      $return_array = [];
      $bookAllocated = DB::table('books')->where('id', $book_id)->first();
      if (!empty($bookAllocated)) {
        $conditions['user_id'] = $user_id;
        $conditions['book_id'] = $book_id;
        $conditions['is_book_returned'] = '0';
        $isUserAssignedThatBook =   BooksUserRented::where($conditions)->get();
        if (!empty($isUserAssignedThatBook->toArray())) {
          $issued_date = $isUserAssignedThatBook->toArray();
          $update_data['returned_date'] = date("Y-m-d");
          $update_data['is_book_returned'] = '1';
          $update_data['total_days'] = $this->getDateDiffrenceBetweenTwoDates($issued_date[0]['rented_date'], $update_data['returned_date']);
          BooksUserRented::where($conditions)->update($update_data);
          $bookDetail = DB::table('books')->where('id', $book_id)->first();
          $stock_quantity = $bookDetail->stock_quantity + 1;
          $bookAllocated = DB::table('books')->where('id', $book_id)->update(array('stock_quantity' => $stock_quantity));
          if ($bookAllocated) {
            $return_array =  ['code' => '200', 'message' => 'Book Returned Sucessfully'];
          } else {
            $return_array = ['code' => '500', 'message' => 'Something Went Wrong'];
          }
        } else {
          $return_array =  ['code' => '203', 'message' => 'No Such Book Is Assigned To User'];
        }
      } else {
        $return_array = ['code' => '203', 'message' => 'No Such Book Found'];
      }
      DB::commit();
      return $return_array;
    } catch (\Exception $e) {
      DB::rollback();
      var_dump($e->getMessage());
      return false;
    }
  }
  protected function getDateDiffrenceBetweenTwoDates($issued_date = '', $returned_date = '')
  {
    $date1 = date_create($issued_date);
    $date2 = date_create($returned_date);
    $diff = date_diff($date1, $date2);
    return $diff->format("%a");
  }
}
