<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SameBookUntilReturn implements Rule
{
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  private $book_id = '';
  private $user_id = '';
  public function __construct($book_id, $user_id)
  {
    $this->book_id = $book_id;
    $this->user_id = $user_id;
  }

  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */

  public function passes($attribute, $value)
  {
    $book_id = $this->book_id;
    $user_id = $this->user_id;
    // print_r($user_id);
    $query = "SELECT * FROM   `books_user_renteds` where `user_id` = '" . $user_id . "' AND `book_id` = '" . $book_id . "' AND is_book_returned = '0' ";
    $resultSet = DB::select(DB::raw($query));
    if (empty($resultSet)) {
      return true;
    } else {
      $this->message();
    }
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return 'The Above book is already being allocated';
  }
}
