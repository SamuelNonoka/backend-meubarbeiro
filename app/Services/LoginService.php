<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Repository\BarberRepository;
use App\Services\BarberService;

class LoginService 
{
  private $barber_repository;

  public function __construct () {
    $this->barber_repository = new BarberRepository();
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

} // Fim da classe