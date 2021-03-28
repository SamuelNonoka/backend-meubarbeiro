<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\TokenHelper;

// Classe responsável pela autenticacao na aplicacao
class AutenticacaoController extends Controller
{
  // Faz a autenticacao na aplicacao
	public function autenticar(Request $request) 
	{
    // Aplicação não possui acesso
    if (env('APP_AMBIENTE') != 'DEV') {
      $domains = explode(',', env('APP_DOMAIN_ACESSO'));
      $has_permission = false;
      foreach ($domains as $domain) {
        if ($request->header('origin') == $domain) {
          $has_permission = true;
          break;
        }  
      }
    
      if (!$has_permission)
        return JsonHelper::getResponseErroPermissao("A api meu Barbeiro é privada! " . $request->header('origin'));
    }
    
		return TokenHelper::gerarToken($request);
	} // Fim do método autenticar

} // Fim da classe
