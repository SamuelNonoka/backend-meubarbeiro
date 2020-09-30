<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EncriptacaoHelper;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Models\BarberModel;
use App\Models\UserModel;

class LoginController extends Controller
{
	// Tenta fazer login na aplicacao
	public function loginBarber (Request $request) 
	{
		// Valida a request
		$rules = [
      'email' 		=> 'required',
      'password'	=> 'required|min:6'
    ];
		
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		// Verifica se o email é válido
    if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");
		
		// Verifica se existe um barbeiro com aquele e-mail cadastrado
		$barber_model = new BarberModel();
		$barber_db 		= $barber_model->getByEmail($request->email);

		if (count($barber_db) == 0)
			return JsonHelper::getResponseErro("E-mail e/ou senha incorreta.");

		$barber = null;

		foreach ($barber_db as $barber_item) 
		{
			$has_barber = EncriptacaoHelper::validarSenha($request->password, $barber_item->password);
			
			if ($has_barber) 
			{
				$barber = $barber_item;
				break;
			}
		}
		
		if (!$barber)
			return JsonHelper::getResponseErro("E-mail e/ou senha incorreta.");

		unset($barber->password);
		$token = TokenHelper::gerarTokenBarber($request, $object = $barber);
		
		if (!$token) 
			return JsonHelper::getResponseErroAutenticacao("Não foi possível gerar o token de acesso!");

		return JsonHelper::getResponseSucesso($token);
	} // Fim do método logar

	// Faz login do usuário
	public function loginUser (Request $request) 
	{
		// Valida a request
		$rules = [
      'email' 		=> 'required',
      'password'	=> 'required|min:6'
    ];
		
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		// Verifica se o email é válido
    if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");
		
		// Verifica se existe um barbeiro com aquele e-mail cadastrado
		$user_db = (new UserModel())->getByEmail($request->email);

		if (count($user_db) == 0)
			return JsonHelper::getResponseErro("E-mail e/ou senha incorreta.");

		$user = null;

		foreach ($user_db as $user_item) 
		{
			$has_user = EncriptacaoHelper::validarSenha($request->password, $user_item->password);
			if ($has_user) {
				$user = $user_item;
				break;
			}
		}
		
		if (!$user)
			return JsonHelper::getResponseErro("E-mail e/ou senha incorreta.");

		unset($user->password);
		$token = TokenHelper::gerarTokenBarber($request, $object = $user);
		
		if (!$token) 
			return JsonHelper::getResponseErroAutenticacao("Não foi possível gerar o token de acesso!");

		return JsonHelper::getResponseSucesso($token);
	} // Fim do método logar

} // Fim da classe
