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

  public function blockUserByModerator (Request $request, $id)
  {
    $user = array('enabled' => false);
    $this->user_repository->update($user, $id); 
		return JsonHelper::getResponseSucesso('Usuário bloqueado!');
	} // Fim do método blockUserByModerator

  public function changePassword ($request)
  {
    if (!$request->password)
			return JsonHelper::getResponseErro('Por favor, informe a senha!');

		$token 		= $request->header('token');
		$payload 	= explode(".",$token);
		$payload 	= $payload[1];
		$payload	= base64_decode($payload);
		$payload	= json_decode($payload);
		$user 		= array('password' => CryptService::encrypt($request->password));
    $this->user_repository->update($user, $payload->usuario->id);
		return JsonHelper::getResponseSucesso('Senha alterada com sucesso!');
  }

  public function changePasswordByCode ($request) 
	{
    $rules = [ 
			'token'			=> 'required',
			'code' 			=> 'required|size:4',
			'password'	=> 'required|min:6' 
		];

		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		$user_db = $this->user_repository->getByUuid($request->token);
		
		if ($user_db->code != $request->code)
			return JsonHelper::getResponseErro('O código não está correto!');

		$password	= CryptService::encrypt($request->password);
		$this->user_repository->update(array('password' => $password), $user_db->id);
		return JsonHelper::getResponseSucesso('Senha alterda com sucesso!');
  } // Fim do método changePasswordByCode

  public function confirmRegister ($request) 
	{
		if (!$request->token)
			return JsonHelper::getResponseErro('Não foi possível confirmar o seu cadastro :(');

		$user_db = $this->user_repository->getByUuid($request->token);

		if (count($user_db) == 0)
			return JsonHelper::getResponseErro('Não foi possível confirmar o seu cadastro :(');
		
		$this->user_repository->confirmRegister($user_db[0]->id);
		return JsonHelper::getResponseSucesso('Cadastro confirmado :)');
	} // Fim do método confirm

  public function decrypt ($user) 
  {
    if (isset($user['name']))
      $user['name'] = CryptService::decrypt($user['name']);
    
    if (isset($user['email']))
      $user['email'] = CryptService::decrypt($user['email']);
    
    if (isset($user['phone_number']))
      $user['phone_number'] = CryptService::decrypt($user['phone_number']);
    
    if (isset($user['password']))
      $user['password'] = CryptService::decrypt($user['password']);
    
    return $user;
  } // Fim do método decrypt

  public function getAll ($request) 
  {
    $search = $request->search ? CryptService::encrypt($request->search) : null;
    $order  = $request->order ?? null; 
    $status = $request->status ?? null;
    $users_db = $this->user_repository->getAll($search, $status, $order);
    foreach ($users_db as $key => $user_db) {
      $users_db[$key] = $this->decrypt($user_db);
      unset($users_db[$key]['password']);
    }
    return JsonHelper::getResponseSucesso($users_db);
  } // Fim do método getAll

  public function store (Request $request) 
  {
    $rules = [
			'name'			=> 'required|max:50',
      'email' 		=> 'required|max:50',
      'password'  => 'required|min:6',
      'born_date' => 'required'
    ];
		
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
      return JsonHelper::getResponseErro($invalido);

    $uuid = (string) Str::uuid();
    $user = array (
			'uuid'			    => $uuid,
      'born_date'     => $request->born_date,
      'acepted_term'  => date('Y-m-d H:i:s'),
			'name'			    => CryptService::encrypt($request->name),
			'email'			    => CryptService::encrypt($request->email),
			'password'      => CryptService::encrypt($request->password)	
    );

    if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");

    $date       = date('Y-m-d', strtotime('-18 years'));
    $born_date  = date('Y-m-d', strtotime($request->born_date));

    if(strtotime($date) < strtotime($born_date))
      return JsonHelper::getResponseErro("O Meu Barbeiro só é permitido para maiores de idade!");

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

  public function storeWithGoogle (Request $request) 
  {
    $rules = [
			'name'			=> 'required|max:50',
      'email' 		=> 'required|max:50',
      'google_id'  => 'required|min:6',
      'born_date' => 'required'
    ];
		
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
      return JsonHelper::getResponseErro($invalido);

    $uuid = (string) Str::uuid();
    $user = array (
      'uuid'			=> $uuid,
      'name'			=> CryptService::encrypt($request->name),
      'email'			=> CryptService::encrypt($request->email),
      'google_id' => $request->google_id,
      'enabled'   => true,
      'acepted_term'  => date('Y-m-d H:i:s'),
      'born_date'     => $request->born_date
    );

    $date       = date('Y-m-d', strtotime('-18 years'));
    $born_date  = date('Y-m-d', strtotime($request->born_date));

    if(strtotime($date) < strtotime($born_date))
      return JsonHelper::getResponseErro("O Meu Barbeiro só é permitido para maiores de idade!");

    if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");
    
    $email_db = $this->user_repository->getByEmail($user['email']);
    
    if (count($email_db) > 0)
      return JsonHelper::getResponseErro('O e-mail informado já está sendo utilizado!');

    $id = $this->user_repository->store($user);
      
    if (!$id)
      return JsonHelper::getResponseErro('Não foi possível finalizar o seu cadastro!');
  
    $user_db = $this->user_repository->getById($id);
    unset($user_db->password);
    $user_db  = $this->decrypt($user_db);
    $token    = TokenHelper::atualizarToken($request, $user_db);
    
    MailHelper::sendRegisterWithGoogle($request->name, $request->email, false);			
    return JsonHelper::getResponseSucesso($token);
  } // Fim do método storeWithGoogle

  
  public function unblockUserByModerator ($request, $id) 
  {
    $user = array('enabled' => true);
    $this->user_repository->update($user, $id); 
		return JsonHelper::getResponseSucesso('Usuário desbloqueado!');
  } // Fim do método blockUserByModerator

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

    $rules = [
      'name'			    => 'required|max:50',
      'phone_number'  => 'required',
      'born_date'     => 'required'
    ];
    
    $invalido = ValidacaoHelper::validar($request->all(), $rules);

    if ($invalido) 
      return JsonHelper::getResponseErro($invalido);

    $date       = date('Y-m-d', strtotime('-18 years'));
    $born_date  = date('Y-m-d', strtotime($request->born_date));

    if(strtotime($date) < strtotime($born_date))
      return JsonHelper::getResponseErro("O Meu Barbeiro só é permitido para maiores de idade!");

		$user = array(
			'name'					=> CryptService::encrypt($request->name),
			'phone_number'  => CryptService::encrypt($request->phone_number),
      'born_date'     => $request->born_date
		);
		
		$this->user_repository->update($user, $id);

		$user_db = $this->user_repository->getById($id);
    $token 	 = TokenHelper::gerarTokenBarber($request, $this->decrypt($user_db));

		return JsonHelper::getResponseSucesso($token);
  }

  public function uploadImage ($request) 
  {
    $user 	= TokenHelper::getUser($request);
		$rules 		= ['img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'];
		$invalido	= ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		if (!$request->hasFile('img'))
			return JsonHelper::getResponseErro('Por favor, envie uma imagem');

		$image 						= $request->file('img');
		$name							= $user->uuid . rand(10, 99);
		$name 						=	$name .'.'.$image->getClientOriginalExtension();
		$path							= '/storage/uploads/users/profile/' . $user->uuid;
    $destinationPath 	= public_path($path);
    $imagePath	 			= $destinationPath. "/".  $name;
		$image->move($destinationPath, $name);
		
		$path 				= $path . '/' . $name;
		$user_arr 	  = array('image_url' => $path);
		$this->user_repository->update($user_arr, $user->id);
    $user_db		= $this->user_repository->getById($user->id);
    $user_db    = $this->decrypt($user_db);
    unset($user_db->password);
		$token			= TokenHelper::gerarTokenBarber($request, $user_db);
		return JsonHelper::getResponseSucesso($token);
  } // Fim do método uploadImage

  public function recoveryPassword (Request $request) 
  {
    $rules    = [ 'email' => 'required|max:50' ];
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");
    
    $email      = CryptService::encrypt($request->email);
    $user_db  = $this->user_repository->getByEmail($email);
		
		if (count($user_db) == 0)
      return JsonHelper::getResponseErro('Esse mail não está cadastrado na plataforma!');
      
    $user_db	= $this->decrypt($user_db[0]);
    $code 			= mt_rand(1000, 9999);
    $this->user_repository->update(array('code' => $code), $user_db->id);

    $sended = MailHelper::sendRecoveryPassword($user_db->email, $user_db->name, $code, $user_db->uuid, false);

    if (!$sended)
      return JsonHelper::getResponseErro('Não foi possível enviar o e-mail!');

    return JsonHelper::getResponseSucesso($user_db->uuid);
  } // Fim do método recoveryPassword

} // Fim da classe