<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;

class Authenticate
{
  /**
  * Método que verifica se o usuário poderá acessar a aplicação
  * @param  \Illuminate\Http\Request  $request
  * @return Json or prossegue com a requisição
  */
  public function handle($request, Closure $next)
  {
    // Aplicação não possui acesso
    if ($request->header('host') != env('APP_DOMAIN_ACESSO'))
      return JsonHelper::getResponseErroPermissao("A API Meu Barbeiro é privada!");

    // Verifica se o usuário possui token
    if ($request->header('token') == null)
      return JsonHelper::getResponseErroAutenticacao("Token de acesso à aplicação não informado!");

    // Verifica se o token é válido
    if(!TokenHelper::eValido($request->header('token')))
      return JsonHelper::getResponseErroAutenticacao("Token inválido!");

    if(TokenHelper::dataExpirada($request->header('token')))
      return JsonHelper::getResponseErroAutenticacao("Token expirado!"); 
    
    return $next($request);
  }
} // Fim da classe
