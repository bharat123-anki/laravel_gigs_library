<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class BookUnique implements Rule
{
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  private $id = '';
  public function __construct($id)
  {
    $this->id = $id;
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
    $id = $this->id;
    $query = "SELECT * FROM books where `book_name` = '" . $value . "' AND `id` != '" . $id . "' ";
    $resultSet = DB::select(DB::raw($query));
    if (empty($resultSet)) {
      return true;
    } else {
      $a = "bharat";
      $this->message($a);
    }
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return 'Please Select Other Book There Books Already Exist. ';
  }
}
