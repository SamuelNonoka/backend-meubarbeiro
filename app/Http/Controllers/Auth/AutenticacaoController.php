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
    $domains = explode(',', env('APP_DOMAIN_ACESSO'));
    $has_permission = false;
    
    foreach ($domains as $domain) {
      if ($request->header('host') == $domain) {
        $has_permission = true;
        break;
      }  
    }
    
    if (!$has_permission)
      return JsonHelper::getResponseErroPermissao("A API Meu Barbeiro é privada!, e1");
    
		return TokenHelper::gerarToken($request);
	} // Fim do método autenticar

} // Fim da classe
