<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Repository\ModeratorRepository;

class ModeratorService 
{
  private $moderator_repository;

  public function __construct () {
    $this->moderator_repository = new ModeratorRepository();
  }

  public function login(Request $request) 
  {
    $rules = [
      'name' 		  => 'required',
      'password'  => 'required'
    ];
		
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

    $password     = CryptService::encrypt($request->password);
    $moderator_db = $this->moderator_repository->getByNameAndPassword(
      $request->name,
      $password
    );

    if (!$moderator_db)
      return JsonHelper::getResponseErro("Nome e/ou senha incorreta.");
      
    unset($moderator_db->password);
    $moderator_db['is_moderator'] = true;
    $token = TokenHelper::gerarTokenBarber($request, $moderator_db);
		
		if (!$token) 
			return JsonHelper::getResponseErroAutenticacao("Não foi possível gerar o token de acesso!");

		return JsonHelper::getResponseSucesso($token);
  } // Fim do método loginBarber

} // Fim da classe