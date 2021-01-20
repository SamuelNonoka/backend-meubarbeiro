<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\JsonHelper;
use App\Helpers\MailHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Services\CryptService;
use App\Repository\UserRepository;

class UserService 
{
  private $user_repository;

  public function __construct () {
    $this->user_repository = new UserRepository();
  }

  public function decrypt ($user) 
  {
    if (isset($user->name))
      $user['name'] = CryptService::encrypt($user->name);
    
    if (isset($user->email))
      $user['email'] = CryptService::encrypt($user->email);
    
    if (isset($user->phone_number))
      $user['phone_number'] = CryptService::encrypt($user->phone_number);
    
    if (isset($user->password))
      $user['password'] = CryptService::encrypt($user->password);
    
    if (isset($user->name))
      $user['image_url'] = CryptService::encrypt($user->image_url);
    
      return $user;
  } // Fim do método decrypt

  public function store (Request $request) 
  {
    $rules = [
			'name'			=> 'required|max:50',
      'email' 		=> 'required|max:50',
      'password'  => 'required|min:6'
    ];
		
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
      return JsonHelper::getResponseErro($invalido);

    $uuid = (string) Str::uuid();
    $user = array (
			'uuid'			=> $uuid,
			'name'			=> CryptService::encrypt($request->name),
			'email'			=> CryptService::encrypt($request->email),
			'password'  => CryptService::encrypt($request->password)	
    );

    if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");

    $email_db = $this->user_repository->getByEmail($user['email']);
    
		if (count($email_db) > 0)
			return JsonHelper::getResponseErro('O e-mail informado já está sendo utilizado!');

		$id = $this->user_repository->store($user);
		
		if (!$id)
			return JsonHelper::getResponseErro('Não foi possível finalizar o seu cadastro!');
	
		$token   	= TokenHelper::atualizarToken($request, array('uuid' => $uuid));
		$payload	= array("token" => $token);

		MailHelper::sendRegister($request->name, $request->email, $request->password, $uuid, $is_barber = false);			
		return JsonHelper::getResponseSucesso($payload);
  } // Fim do método store

  public function update (Request $request, $id) 
  {
    $token 		= $request->header('token');
		$payload 	= explode(".",$token);
		$payload 	= $payload[1];
		$payload	= base64_decode($payload);
		$payload	= json_decode($payload);
		$usuario  = $payload->usuario;
    
		if ($id != $usuario->id)
			return JsonHelper::getResponseErro('Seu usuário não tem permissão para alterar os dados!');

		$user = array(
			'name'					=> CryptService::encrypt($request->name),
			'phone_number'  => CryptService::encrypt($request->phone_number)
		);
		
		$this->user_repository->update($user, $id);

		$user_db = $this->user_repository->getById($id);
    $token 	 = TokenHelper::gerarTokenBarber($request, $this->decrypt($user_db[0]));

		return JsonHelper::getResponseSucesso($token);
  }

} // Fim da classe