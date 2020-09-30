<?php

namespace App\Helpers;

class EncriptacaoHelper 
{
  // Gera o random para encriptar a senha
  private function gerarRandomSenha ()
  {
    $randomIdLength = 12;
    $token          = '';

    do {
      $bytes = random_bytes($randomIdLength);
      $token .= str_replace(
        ['.','/','='], 
        '',
        base64_encode($bytes)
      );
    } while (strlen($token) < $randomIdLength);
    
    return $token;
  } // Fim do método gerarRandomSenha

  // Encripta a senha
  public static function encriptarSenha ($senha) 
  {
    $random = (new EncriptacaoHelper())->gerarRandomSenha();
    return crypt($senha . env('APP_KEY'), $random);
  } // Fim do método encriptarSenha

  // Verifica a senha
  public static function validarSenha ($senha, $senha_banco) 
  {
    $senha .= env('APP_KEY');
    return (crypt($senha, $senha_banco) == $senha_banco) ? true : false;
  } // Fim do método isSenha

} // Fim da classe