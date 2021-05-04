<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;

class Moderator
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
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

    if ($request->header('moderator_token') == null)
      return JsonHelper::getResponseErroAutenticacao("Token de acesso à aplicação não informado! 2" . $request->header('moderator_token'));

    if(!TokenHelper::eValido($request->header('moderator_token')))
      return JsonHelper::getResponseErroAutenticacao("Token inválido!");

    if(TokenHelper::dataExpirada($request->header('moderator_token')))
      return JsonHelper::getResponseErroAutenticacao("Token expirado!");
      
    $user = TokenHelper::getUser($request);

    if (!$user->is_moderator)
      return JsonHelper::getResponseErroAutenticacao("Você não tem permissão para acessar essa API!");
  
    return $next($request);
	}
}
