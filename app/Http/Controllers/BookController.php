<?php

namespace App\Http\Controllers;

use Validator;
use Auth;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Rules\BookUnique;

class BookController extends Controller
{
  public function createBooks(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'author' => 'required',
        'book_name' => 'required|unique:books',
        'stock_quantity' => 'required|numeric',
      ]);

      if ($validator->fails()) {
        foreach ($validator->messages()->getMessages() as $field_name => $messages) {
          foreach ($messages as $message) {
            return response()->json(['code' => '203', 'message' => $message]);
            break;
          }
        }
      }
      if ($validator->passes()) {
        $newBook = Book::create($request->all());
      }
      if ($newBook) {
        return response()->json(['code' => '200', 'message' => 'Book Created Sucessfully']);
      } else {
        return response()->json(['code' => '500', 'message' => 'Something Went Wrong']);
      }
    } catch (\Exception $e) {
      return response()->json(['code' => '203', 'message' => $e->getMessage()]);
    }
  }
  public function updateBook(Request $request, $id)
  {
    try {
      $validator = Validator::make($request->all(), [
        'author' => 'required',
        'book_name' =>  ['required', new BookUnique($id)],
      ]);
      if ($validator->fails()) {
        foreach ($validator->messages()->getMessages() as $field_name => $messages) {
          foreach ($messages as $message) {
            return response()->json(['code' => '203', 'message' => $message]);
            break;
          }
        }
      }
      if(empty($id)){
            return response()->json(['code' => '203', 'message' => "please insert Id"]);
      }
      if ($validator->passes()) {
        $book = Book::find($id);
        if (empty($book)) {
          return response()->json(['code' => '203', 'message' => 'No Such Record Exist']);
        } else {
          $book = Book::where('id', $id)->update($request->all());
          if ($book) {
            return response()->json(['code' => '200', 'message' => 'Book Updated Sucessfully']);
          } else {
            return response()->json(['code' => '500', 'message' => 'Something Went Wrong']);
          }
        }
        // print_r($user);
      }
    } catch (\Exception $e) {

      return response()->json(['code' => '203', 'message' => $e->getMessage()]);
    }
  }
  public function listBooks()
  {
    $book = Book::all();
    return response()->json(['code' => '200', 'message' => $book]);
  }
  public function deleteBooks(Request $request, $id)
  {
    $book = Book::find($id);
    if (empty($book)) {
      return response()->json(['code' => '203', 'message' => 'No Such Record Exist']);
    } else {
      $book->delete();
      return response()->json(['code' => '200', 'message' => 'Book Deleted Sucessfully']);
    }
  }

  public function listSingleBook(Request $request, $id)
  {
    $book = Book::find($id);
    if (empty($book)) {
      return response()->json(['code' => '203', 'message' => 'No Such Record Exist']);
    } else {
      return response()->json(['code' => '200', 'message' => $book]);
    }
  }
}
