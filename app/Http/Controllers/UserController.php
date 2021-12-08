<?php

namespace App\Http\Controllers;

use Validator;
use Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Rules\EmailUnique;
use App\Rules\MobileUnique;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserController extends Controller
{
  public function register(Request $request)
  {

    try {
      $validator = Validator::make($request->all(), [
        'first_name' => 'required|alpha',
        'last_name' => 'required|alpha',
        'email' => 'required|email|unique:users',
        'mobile' => 'required|numeric|unique:users',
        'password' => 'required',
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
        $password = Hash::make($request->password);
        $request->merge(['password' => $password]);
        $newUser = User::create($request->all());
      }
      if ($newUser) {
        return response()->json(['code' => '200', 'message' => 'User Created Sucessfully']);
      } else {
        return response()->json(['code' => '500', 'message' => 'Something Went Wrong']);
      }
    } catch (\Exception $e) {
      return response()->json(['code' => '203', 'message' => $e->getMessage()]);
    }
  }
  public function login(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'email' => 'required',
        'password' => 'required',
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

        if (!Auth::attempt($request->all())) {
          return response()->json(['code' => '401', 'message' => 'Credientials Mismatch']);
        }
        return response()->json([
          'code' => 200,
          'user' => auth()->user(),
          'token' => auth()->user()->createToken('API Token')->plainTextToken
        ]);
      }
    } catch (\Exception $e) {
      return response()->json(['code' => '203', 'message' => $e->getMessage()]);
    }
  }
  public function update(Request $request, $id)
  {

    try {
      $validator = Validator::make($request->all(), [
        'first_name' => 'required|alpha',
        'last_name' => 'required|alpha',
        'email' =>  ['required', 'email', 'string', new EmailUnique($id)],
        'mobile' =>  ['required', 'numeric', new MobileUnique($id)],
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
        $user = User::find($id);
        if (empty($user)) {
          return response()->json(['code' => '203', 'message' => 'No Such Record Exist']);
        } else {
          $user = User::where('id', $id)->update($request->all());
          if ($user) {
            return response()->json(['code' => '200', 'message' => 'User Updated Sucessfully']);
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

  public function list()
  {
    $user = User::all();
    return response()->json(['code' => '200', 'message' => $user]);
  }

  public function delete(Request $request, $id)
  {
    $user = User::find($id);
    if (empty($user)) {
      return response()->json(['code' => '203', 'message' => 'No Such Record Exist']);
    } else {
      $user->delete();
      return response()->json(['code' => '200', 'message' => 'User Deleted Sucessfully']);
    }
  }

  public function listSingleUser($id = '')
  {
    $user = User::find($id);
    if (empty($user)) {
      return response()->json(['code' => '203', 'message' => 'No Such Record Exist']);
    } else {
      return response()->json(['code' => '200', 'message' => $user]);
    }
  }

  public function userBookListing(Request $request)
  {
    if (empty($request->user_id)) {
      return response()->json(['code' => '203', 'message' => 'Please Insert User Id']);
    }
    $userId = $request->user_id;
    $book = User::with(['book_user_rented'])->where('users.id', $userId)->get();
    return response()->json(['code' => '200', 'message' => $book]);
  }
  public function userBookListingNotReturnedBooks(Request $request)
  {
    if (empty($request->user_id)) {
      return response()->json(['code' => '203', 'message' => 'Please Insert User Id']);
    }
    $userId = $request->user_id;
    $book = User::with(['book_user_rented' => function ($q) {
      $q->where('books_user_renteds.is_book_returned', '0');
    }])->where('users.id', $userId)->get();
    return response()->json(['code' => '200', 'message' => $book]);
  }
}
