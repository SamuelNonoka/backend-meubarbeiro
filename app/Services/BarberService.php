<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\JsonHelper;
use App\Helpers\MailHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Repository\BarberRepository;
use App\Services\CryptService;

class BarberService 
{
  private $barber_repository;

  public function __construct () {
    $this->barber_repository = new BarberRepository();
  }

  public function store (Request $request) 
  {
    $rules = [
			'name'			=>	'required|max:50',
      'email' 		=> 'required|max:50',
      'password'	=> 'required|min:6'
    ];

    $invalido = ValidacaoHelper::validar($request->all(), $rules);

    if ($invalido) 
      return JsonHelper::getResponseErro($invalido);
      
    $barbershop_id 		= null;
    $barber_token			= $request->token ?? null;
    $barber_status_id	= $this->barber_repository::AGUARDANDO;

    if ($barber_token) {
			if (!TokenHelper::eValido($barber_token))
				return JsonHelper::getResponseErro('Token Invádido');

			$token 						= TokenHelper::getUser($request);
			$barbershop_id 		= $token->barbershop_id;
			$barber_status_id	= $this->barber_repository::ATIVO;
    }
    
    if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro('Por favor, informe um e-mail válido.');
      
    $name 		= CryptService::encrypt($request->name);
    $email		= CryptService::encrypt($request->email);
    $password = CryptService::encrypt($request->password);
    $uuid			= (string) Str::uuid();
    
    $has_email = $this->barber_repository->getByEmail($email);
  
    if (count($has_email) > 0)
      return JsonHelper::getResponseErro('O e-mail informado já está sendo utilizado!');

    $barber = array (
			'uuid'							=> $uuid,
			'name'							=> $name,
			'email'							=> $email,
			'password'					=> $password,
			'barbershop_id'			=> $barbershop_id,
      'barber_status_id'  => $barber_status_id
		);
    
    $id = $this->barber_repository->store($barber);
    if ($id == 0)
			return JsonHelper::getResponseErro('Não foi possível finalizar o seu cadastro!');
	
		$token   	= TokenHelper::atualizarToken($request, array('uuid' => $uuid));
		$payload	= array("token" => $token);

		MailHelper::sendRegister($request->name, $request->email, $request->password, $uuid, $is_barber = true);	
		return JsonHelper::getResponseSucesso($payload);
  } // Fim do método store

  public function update ($request, $id) 
  {
    $rules = [
			'name' 	=> 'required|max:50',
			'phone'	=> 'required|max:11|min:8'
		];

    $invalido = ValidacaoHelper::validar($request->all(), $rules);
    
    if ($invalido) 
      return JsonHelper::getResponseErro($invalido);
      
    $barber = array(
      'name'  => CryptService::encrypt($request->name),
      'phone' => CryptService::encrypt($request->phone)
    );

    $this->barber_repository->update($barber, $id);
    $barber_db		= $this->barber_repository->getById($id);
    $token   	    = TokenHelper::atualizarToken($request, $barber_db);
		$payload	    = array("token" => $token);
		return JsonHelper::getResponseSucesso($payload);
  } // Fim do método update

} // Fim da classe