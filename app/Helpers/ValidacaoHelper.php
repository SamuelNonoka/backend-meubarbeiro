<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;

// Classe que auxilia na validação 
class ValidacaoHelper 
{
  // Faz a validacao da request
  public static function validar ($request, $rules, $messages = null) 
  {
    if (!$messages) 
    {
      $messages = [
        'required'  => 'O campo :attribute precisa ser preenchido.',
        'min'       => 'O campo :attribute precisa ter no mínimo :min caracteres.',
        'max'       => 'O campo :attribute deve ter no máximo :max caracteres.'
      ];
    }
  
    $validator = Validator::make($request, $rules, $messages);
    return ($validator->fails()) ? $validator->errors()->first() : null;
  } // Fim do método validar

} // Fim da classe