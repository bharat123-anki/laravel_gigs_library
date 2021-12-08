<?php

namespace App\Http\Controllers;

use Validator;
use Auth;
use Illuminate\Http\Request;
use App\Rules\SameBookUntilReturn;
use App\Models\BooksUserRented;

class BooksUserRentedController extends Controller
{
  public function bookRentedByUser(Request $request)
  {
    try {
      $book_id = $request->book_id;
      $user_id = $request->user_id;

      $validator = Validator::make($request->all(), ['user_id' => 'required', 'book_id' => ['required', new SameBookUntilReturn($book_id, $user_id)]]);
      if ($validator->fails()) {
        foreach ($validator->messages()->getMessages() as $field_name => $messages) {
          foreach ($messages as $message) {
            return response()->json(['code' => '203', 'message' => $message]);
            break;
          }
        }
      }
      if ($validator->passes()) {
        $user_id = $request->user_id;
        $book_id = $request->book_id;
        $objBookReturned = new BooksUserRented;
        $returnData = $objBookReturned->bookAssignedToUser($user_id, $book_id);
        if (count($returnData) < 0) {
          return response()->json(['code' => '500', 'message' => 'Something Went Wrong']);
        } else {
          return response()->json($returnData);
        }
      }
    } catch (\Exception $e) {
      return response()->json(['code' => '203', 'message' => $e->getMessage()]);
    }
  }
  public function bookReturnedByUser(Request $request)
  {
    try {
      $book_id = $request->book_id;
      $user_id = $request->user_id;

      $validator = Validator::make($request->all(), ['user_id' => 'required', 'book_id' => ['required']]);
      if ($validator->fails()) {
        foreach ($validator->messages()->getMessages() as $field_name => $messages) {
          foreach ($messages as $message) {
            return response()->json(['code' => '203', 'message' => $message]);
            break;
          }
        }
      }
      if ($validator->passes()) {
        $user_id = $user_id;
        $book_id = $book_id;
        $objBookReturned = new BooksUserRented;
        $returnData = $objBookReturned->bookReturnedByUser($user_id, $book_id);
        if (count($returnData) < 0) {
          return response()->json(['code' => '500', 'message' => 'Something Went Wrong']);
        } else {
          return response()->json($returnData);
        }
      }
    } catch (\Exception $e) {
      return response()->json(['code' => '203', 'message' => $e->getMessage()]);
    }
  }
  public function listAllBookRentedByUser(Request $request)
  {
    if (empty($request->user_id)) {
      return response()->json(['code' => '203', 'message' => 'Please Insert User Id']);
    }
    $userId = $request->user_id;
    $book = BooksUserRented::with(['user', 'book'])->whereHas('user', function ($q) use ($userId) {
      $q->where('users.id', $userId);
    })->get();
    return response()->json(['code' => '200', 'message' => $book]);
  }
}
