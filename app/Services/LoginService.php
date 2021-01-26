<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Repository\BarberRepository;
use App\Repository\UserRepository;
use App\Services\BarberService;
use App\Services\UserService;

class LoginService 
{
  private $barber_repository;

  public function __construct () {
    $this->barber_repository  = new BarberRepository();
    $this->user_repository    = new UserRepository();
  }

  public function loginBarber(Request $request) 
  {
    $rules = [
      'email' 		=> 'required',
      'password'	=> 'required|min:6'
    ];
		
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");

    $email      = CryptService::encrypt($request->email);
    $password   = CryptService::encrypt($request->password);
    $barber_db  = $this->barber_repository->getByEmail($email);

    if (count($barber_db) == 0)
      return JsonHelper::getResponseErro("E-mail e/ou senha incorreta.");
      
    $barber = null;

    foreach ($barber_db as $barber_item) 
		{
			if ($barber_item->password == $password) {
				$barber = $barber_item;
				break;
			}
		}

    if (!$barber)
			return JsonHelper::getResponseErro("E-mail e/ou senha incorreta.");

    unset($barber->password);
    $barber = (new BarberService)->decrypt($barber);
		$token  = TokenHelper::gerarTokenBarber($request, $object = $barber);
		
		if (!$token) 
			return JsonHelper::getResponseErroAutenticacao("Não foi possível gerar o token de acesso!");

		return JsonHelper::getResponseSucesso($token);
  } // Fim do método loginBarber

  public function loginUser (Request $request) 
  {
    $rules = [
      'email' 		=> 'required',
      'password'	=> 'required|min:6'
    ];
		
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");
		
		$user_db = $this->user_repository->getByEmail(CryptService::encrypt($request->email));
    
    if (count($user_db) == 0)
			return JsonHelper::getResponseErro("E-mail e/ou senha incorreta.");

    if ($user_db[0]->password != CryptService::encrypt($request->password))
      return JsonHelper::getResponseErro("E-mail e/ou senha incorreta.");

    $user_db = (new UserService)->decrypt($user_db[0]);
		unset($user_db->password);
		$token = TokenHelper::gerarTokenBarber($request, $user_db);
		
		if (!$token) 
			return JsonHelper::getResponseErroAutenticacao("Não foi possível gerar o token de acesso!");

		return JsonHelper::getResponseSucesso($token);
  } // Fim do método loginUser

  public function loginUserWithGoogle (Request $request) 
  {
    $rules = [
      'email' 		=> 'required',
      'google_id' => 'required'
    ];
		
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");
		
		$user_db = $this->user_repository->getByEmail(CryptService::encrypt($request->email));
    
    if (count($user_db) == 0)
			return JsonHelper::getResponseErro("E-mail e/ou senha incorreta.");

    $user_db = $user_db[0];
    if ($user_db->google_id && $user_db->google_id != $request->google_id)
      return JsonHelper::getResponseErroAutenticacao("Não foi possível fazer o login!");

    $user_db = (new UserService)->decrypt($user_db);
		unset($user_db->password);
		$token = TokenHelper::gerarTokenBarber($request, $user_db);
		
		if (!$token) 
			return JsonHelper::getResponseErroAutenticacao("Não foi possível gerar o token de acesso!");

		return JsonHelper::getResponseSucesso($token);
  } // Fim do método loginUserWithGoogle

  public function loginBarberWithGoogle (Request $request) 
  {
    $rules = [
      'email' 		=> 'required',
      'google_id' => 'required'
    ];
		
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");
		
		$barber_db = $this->user_repository->getByEmail(CryptService::encrypt($request->email));
    
    if (count($barber_db) == 0)
			return JsonHelper::getResponseErro("E-mail e/ou senha incorreta.");

    $barber_db = $barber_db[0];
    if ($barber_db->google_id && $barber_db->google_id != $request->google_id)
      return JsonHelper::getResponseErroAutenticacao("Não foi possível fazer o login!");

    $barber_db = (new BarberService)->decrypt($barber_db);
		unset($barber_db->password);
		$token = TokenHelper::gerarTokenBarber($request, $barber_db);
		
		if (!$token) 
			return JsonHelper::getResponseErroAutenticacao("Não foi possível gerar o token de acesso!");

		return JsonHelper::getResponseSucesso($token);
  } // Fim do método loginBarberWithGoogle

} // Fim da classe