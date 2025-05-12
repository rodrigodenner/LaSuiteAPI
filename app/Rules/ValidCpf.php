<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidCpf implements Rule
{
  public function passes($attribute, $value): bool
  {

    $cpf = preg_replace('/\D/', '', $value);

    if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) {
      return false;
    }

    for ($t = 9; $t < 11; $t++) {
      $sum = 0;
      for ($i = 0; $i < $t; $i++) {
        $sum += $cpf[$i] * (($t + 1) - $i);
      }
      $digit = ((10 * $sum) % 11) % 10;
      if ($cpf[$t] != $digit) {
        return false;
      }
    }

    return true;
  }

  public function message(): string
  {
    return 'O campo :attribute não é um CPF válido.';
  }
}

