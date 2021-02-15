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
    if ($request->header('origin') != env('APP_DOMAIN_ACESSO'))
      return JsonHelper::getResponseErroPermissao("A API Meu Barbeiro é privada!");
    
		return TokenHelper::gerarToken($request);
	} // Fim do método autenticar

} // Fim da classe
