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

  public function crypt () 
  {
    $barber_db = $this->barber_repository->getNotEncrypted();
    
    if ($barber_db == null)
      return JsonHelper::getResponseErro('Todos os barbeiros já foram encriptados!');
    
    $barber = array(
      'email'     => CryptService::encrypt($barber_db->email),
      'name'      => CryptService::encrypt($barber_db->name),
      'phone'     => CryptService::encrypt($barber_db->phone),
      'encrypted' => true
    );
  
    $this->barber_repository->update($barber, $barber_db->id);
    return JsonHelper::getResponseSucesso('Barberiro encriptado com sucesso!');
  } // Fim do método crypt

  private function decrypt ($barber_db) 
  {
    $barber_db->email = CryptService::decrypt($barber_db->email);
    $barber_db->name  = CryptService::decrypt($barber_db->name);
    $barber_db->phone = CryptService::decrypt($barber_db->phone);
    return $barber_db;
  } // Fim do método decrypt

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
      'encrypted'         => true,
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

    $token  = TokenHelper::getUser($request);
    $barber = array(
      'email'     => CryptService::encrypt($token->email),
      'name'      => CryptService::encrypt($request->name),
      'phone'     => CryptService::encrypt($request->phone),
      'encrypted' => true
    );

    $this->barber_repository->update($barber, $id);
    $barber_db		= $this->barber_repository->getById($id);
    $barber_db    = $this->decrypt($barber_db);
    unset($barber_db->password);
    $token   	    = TokenHelper::atualizarToken($request, $barber_db);
		$payload	    = array("token" => $token);
		return JsonHelper::getResponseSucesso($payload);
  } // Fim do método update

} // Fim da classe