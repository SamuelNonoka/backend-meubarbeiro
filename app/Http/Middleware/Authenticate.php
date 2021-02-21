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
    $domains = explode(',', env('APP_DOMAIN_ACESSO'));
    $has_permission = false;
    foreach ($domains as $domain) {
      if ($request->header('host') == $domain) {
        $has_permission = true;
        break;
      }  
    }
  
    if (!$has_permission)
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
