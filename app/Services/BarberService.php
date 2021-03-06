<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\JsonHelper;
use App\Helpers\MailHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Repository\BarberRepository;
use App\Repository\BarbershopRepository;
use App\Repository\ScheduleRepository;
use App\Services\CryptService;

class BarberService 
{
  private $barber_repository;
  private $barbershop_repository;
  private $schedule_repository;

  public function __construct () 
  {
    $this->barber_repository      = new BarberRepository();
    $this->barbershop_repository  = new BarbershopRepository();
    $this->schedule_repository    = new ScheduleRepository();
  } // Fim do Construtor

  public function blockBarber ($request, $id) 
  {
    $barber_db      = $this->barber_repository->getById($id);
		$barber 	      = TokenHelper::getUser($request);
		$barbershop_db	= $this->barbershop_repository->getById($barber_db->barbershop_id);
		
		if ($barbershop_db->id != $barber->barbershop_id || $barbershop_db->admin_id != $barber->id)
			return JsonHelper::getResponseErro('Você não tem permissão para realizar essa ação!');

		$schedules = $this->schedule_repository->getFutureAprovedByBarberId($barber_db->id);
		
		if (count($schedules) > 0)
			return JsonHelper::getResponseErro('Não é possível bloquear o barbeiro pois ele tem agendamentos pendentes!');

		$barber = array('barber_status_id' => $this->barber_repository::BLOQUEADO);
		$this->barber_repository->update($barber, $id); 
		return JsonHelper::getResponseSucesso('Barbeiro bloqueado!');
  } // Fim do método blockBarber

  public function blockBarberByModerator ($request, $id) 
  {
    $barber = array('enabled' => false);
    $this->barber_repository->update($barber, $id); 
		return JsonHelper::getResponseSucesso('Barbeiro bloqueado!');
  } // Fim do método blockBarberByModerator

  public function changePassword (Request $request) 
  {
    $rules = [ 
			'token'			=> 'required',
			'code' 			=> 'required|size:4',
			'password'	=> 'required|min:6' 
		];

		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		$barber_db = $this->barber_repository->getByUuid($request->token);
		
		if (count($barber_db) == 0)
			return JsonHelper::getResponseErro('Não foi possível recuperar o token!');

    $barber_db = $barber_db[0];
    
		if ($barber_db->code != $request->code)
			return JsonHelper::getResponseErro('O código não está correto!');

    $password	= CryptService::encrypt($request->password);
    $this->barber_repository->update(array('password' => $password), $barber_db->id);
		return JsonHelper::getResponseSucesso('Senha alterda com sucesso!');
  } // Fim do método changePassword

  public function confirmRegister ($request) 
  {
    if (!$request->token)
			return JsonHelper::getResponseErro('Não foi possível confirmar o seu cadastro :( !');

    $barber_db = $this->barber_repository->getByUuid($request->token);		
		$this->barber_repository->confirmRegister($barber_db[0]->id);
		return JsonHelper::getResponseSucesso('Cadastro confirmado :) !');
  } // Fim do método confirmRegister

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

  public function decrypt ($barber_db) 
  {
    $barber_db->email = CryptService::decrypt($barber_db->email);
    $barber_db->name  = CryptService::decrypt($barber_db->name);
    $barber_db->phone = CryptService::decrypt($barber_db->phone);
    return $barber_db;
  } // Fim do método decrypt

  public function getRevenuesByBarber ($request, $barber_id, $barbershop_id) 
  {
    $filter = array(
      'end_date'    => $request->end_date ?? null,
      'start_date'  => $request->start_date ?? null
    );
    $data = $this->schedule_repository->getAmmountByBarber($barber_id, $barbershop_id, $filter);
    return JsonHelper::getResponseSucesso($data);
	}

  public function getAll ($request) 
  {
    $search = $request->search ? CryptService::encrypt($request->search) : null;
    $order  = $request->order ?? null; 
    $status = $request->status ?? null;
    $barbers_db = $this->barber_repository->getAll($search, $status, $order);
    foreach ($barbers_db as $key => $barber_db) {
      $barbers_db[$key] = $this->decrypt($barber_db);
      unset($barbers_db[$key]['password']);
    }
    return JsonHelper::getResponseSucesso($barbers_db);
  } // Fim do método getAll

  public function getByBarbershopId ($request, $barbershop_id) 
  {
    $status = null; 
    if ($request->status) {
      if ($request->status == 'ativo')
        $status = $this->barber_repository::ATIVO;
      else if ($request->status == 'bloqueado')
        $status = $this->barber_repository::BLOQUEADO;
    }
    $filters = array ('status' => $status);
    $barbers_db = $this->barber_repository->getByBarbershopId($barbershop_id, $filters);
    foreach ($barbers_db as $key => $barber_db) {
      $barbers_db[$key] = $this->decrypt($barber_db);
      unset($barbers_db[$key]['password']);
    }
    return JsonHelper::getResponseSucesso($barbers_db);
  } // Fim do método getByBarbsershopId

  public function getTotalBarbersByBarbershopId ($barbershop_id) 
  {
    $data = $this->barber_repository->getTotalBarbersByBarbershopId($barbershop_id);
		return JsonHelper::getResponseSucesso($data);
  } // Fim do método getTotalBarbershopByBarbershopId

  public function ranking ($request, $barbershop_id) 
  {
    $data = $this->barber_repository->ranking($request, $barbershop_id);

    foreach ($data as $key => $barber) {
      $data[$key] = self::decrypt($barber);
    }
    
		return JsonHelper::getResponseSucesso($data);
  } // Fim do métofo ranking

  public function resendRegisterMail (Request $request) 
  {
    if (!$request->email)
      return JsonHelper::getResponseErro('Por favor, informe o e-mail');

    if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro('Por favor, informe um e-mail válido.');

    $email     = CryptService::encrypt($request->email);
    $barber_db = $this->barber_repository->getByEmail($email);
  
    if (count($barber_db) == 0)
      return JsonHelper::getResponseErro('O e-mail informado não está sendo utilizado!');

    $barber_db = $this->decrypt($barber_db[0]);

    MailHelper::sendRegister($barber_db->name, $barber_db->email, $barber_db->password, $barber_db->uuid, $is_barber = true);

		return JsonHelper::getResponseSucesso('E-mail enviado com sucesso!');
	} // Fim do método resendRegisterMail

  public function store (Request $request) 
  {
    $rules = [
			'name'			=>	'required|max:50',
      'email' 		=> 'required|max:50',
      'password'	=> 'required|min:6',
      'born_date' => 'required'
    ];

    $invalido = ValidacaoHelper::validar($request->all(), $rules);

    if ($invalido) 
      return JsonHelper::getResponseErro($invalido);
      
    $barbershop_id 		= null;
    $barber_token			= $request->barber_token ?? null;
    $barber_status_id	= $this->barber_repository::AGUARDANDO;
    
    if ($barber_token) {
			if (!TokenHelper::eValido($barber_token))
				return JsonHelper::getResponseErro('Token Invádido');

      $part 	          = explode(".",$barber_token);
      $payload          = $part[1];
      $payload          = json_decode(base64_decode($payload));
			$barbershop_id    = $payload->usuario->barbershop_id;
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

    $date       = date('Y-m-d', strtotime('-18 years'));
    $born_date  = date('Y-m-d', strtotime($request->born_date));

    if(strtotime($date) < strtotime($born_date))
      return JsonHelper::getResponseErro("O Meu Barbeiro só é permitido para maiores de idade!");

    $barber = array (
			'uuid'							=> $uuid,
			'name'							=> $name,
			'email'							=> $email,
			'password'					=> $password,
      'encrypted'         => true,
      'barbershop_id'			=> $barbershop_id,
      'barber_status_id'  => $barber_status_id,
      'born_date'         => $request->born_date,
      'acepted_term'      => date('Y-m-d H:i:s'),
		);
    
    $id = $this->barber_repository->store($barber);
    if ($id == 0)
			return JsonHelper::getResponseErro('Não foi possível finalizar o seu cadastro!');
	
		$token   	= TokenHelper::atualizarToken($request, array('uuid' => $uuid));
		$payload	= array("token" => $token);

		MailHelper::sendRegister($request->name, $request->email, $request->password, $uuid, $is_barber = true);	
		return JsonHelper::getResponseSucesso($payload);
  } // Fim do método store

  public function storeWithGoogle (Request $request) 
  {
    $rules = [
			'name'			=>	'required|max:50',
      'email' 		=> 'required|max:50',
      'google_id' => 'required',
      'born_date' => 'required'
    ];

    $invalido = ValidacaoHelper::validar($request->all(), $rules);

    if ($invalido) 
      return JsonHelper::getResponseErro($invalido);

    $date       = date('Y-m-d', strtotime('-18 years'));
    $born_date  = date('Y-m-d', strtotime($request->born_date));

    if(strtotime($date) < strtotime($born_date))
      return JsonHelper::getResponseErro("O Meu Barbeiro só é permitido para maiores de idade!");
      
    if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro('Por favor, informe um e-mail válido.');
      
    $name 		        = CryptService::encrypt($request->name);
    $email		        = CryptService::encrypt($request->email);
    $uuid			        = (string) Str::uuid();
    $barber_status_id	= $this->barber_repository::ATIVO;

    $has_email = $this->barber_repository->getByEmail($email);
  
    if (count($has_email) > 0)
      return JsonHelper::getResponseErro('O e-mail informado já está sendo utilizado!');

    $barber = array (
			'uuid'							=> $uuid,
			'name'							=> $name,
			'email'							=> $email,
			'google_id'					=> $request->google_id,
      'encrypted'         => true,
      'enabled'           => true,
      'barbershop_id'			=> null,
      'barber_status_id'  => $barber_status_id,
      'acepted_term'  => date('Y-m-d H:i:s'),
      'born_date'     => $request->born_date
		);
    
    $id = $this->barber_repository->store($barber);
    if ($id == 0)
			return JsonHelper::getResponseErro('Não foi possível finalizar o seu cadastro!');
	
    $barber_db = $this->barber_repository->getById($id);
    unset($barber_db->password);
    $barber_db = $this->decrypt($barber_db);
		$token = TokenHelper::atualizarToken($request, $barber_db);
		
		MailHelper::sendRegisterWithGoogle($request->name, $request->email, true);	
		return JsonHelper::getResponseSucesso($token);
  } // Fim do método storeWithGoogle

  public function recoveryPassword (Request $request) 
  {
    $rules    = [ 'email' => 'required|max:50' ];
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");
    
    $email      = CryptService::encrypt($request->email);
    $barber_db  = $this->barber_repository->getByEmail($email);
		
		if (count($barber_db) == 0)
      return JsonHelper::getResponseErro('Esse mail não está cadastrado na plataforma!');
      
    $barber_db	= $this->decrypt($barber_db[0]);
    $code 			= mt_rand(1000, 9999);
    $this->barber_repository->update(array('code' => $code), $barber_db->id);

    $sended = MailHelper::sendRecoveryPassword($barber_db->email, $barber_db->name, $code, $barber_db->uuid, true);

    if (!$sended)
      return JsonHelper::getResponseErro('Não foi possível enviar o e-mail!');

    return JsonHelper::getResponseSucesso($barber_db->uuid);
  } // Fim do método recoveryPassword

  public function sendInvitation ($request) 
  {
    $rules    = [ 'email' => 'required|max:50' ];
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
			return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");

    $email      = CryptService::encrypt($request->email);
    $barber_db  = $this->barber_repository->getByEmail($email);
    
    if (count($barber_db) > 0)
      return JsonHelper::getResponseErro('Este barbeiro já está cadastrado!'); 

		$barber					= TokenHelper::getUser($request);
		$barbershop_db 	= $this->barbershop_repository->getById($barber->barbershop_id);
		$token					= array(
			'barbershop_id'	=> $barber->barbershop_id,
			'barber_mail'		=> $request->email					
		);
		$expiration			= date('Y-m-d H:i', strtotime('+1 day'));
		$token					= TokenHelper::setToken($request, $token, $expiration);
		MailHelper::sendBarberInvitation($request->email, $barbershop_db->name, $token);
		return JsonHelper::getResponseSucesso('Convite enviado para o barbeiro!');
  } // Fim do método sendInvitation

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
      'profile'   => $request->profile,
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

  public function uploadImage ($request) 
  {
    $barber 	= TokenHelper::getUser($request);
		$rules 		= ['img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'];
		$invalido	= ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		if (!$request->hasFile('img'))
			return JsonHelper::getResponseErro('Por favor, envie uma imagem');

		$image 						= $request->file('img');
		$name							= $barber->uuid . rand(10, 99);
		$name 						=	$name .'.'.$image->getClientOriginalExtension();
		$path							= '/storage/uploads/barbers/profile/' . $barber->uuid;
    $destinationPath 	= public_path($path);
    $imagePath	 			= $destinationPath. "/".  $name;
		$image->move($destinationPath, $name);
		
		$path 				= $path . '/' . $name;
		$barber_arr 	= array('image_url' => $path);
		$this->barber_repository->update($barber_arr, $barber->id);
    $barber_db		= $this->barber_repository->getById($barber->id);
    $barber_db    = $this->decrypt($barber_db);
    unset($barber_db->password);
		$token			= TokenHelper::gerarTokenBarber ($request, $barber_db);
		return JsonHelper::getResponseSucesso($token);
  } // Fim do método uploadImage

  public function unlockBarber ($request, $id) 
	{
		$barber_db      = $this->barber_repository->getById($id);
		$barber 		    = TokenHelper::getUser($request);
		$barbershop_db  = $this->barbershop_repository->getById($barber_db->barbershop_id);
		
    if ($barbershop_db->id != $barber->barbershop_id || $barbershop_db->admin_id != $barber->id)
			return JsonHelper::getResponseErro('Você não tem permissão para realizar essa ação!');

		$barber = array('barber_status_id' => $this->barber_repository::ATIVO);
		$this->barber_repository->update($barber, $id); 
		return JsonHelper::getResponseSucesso('Barbeiro desbloqueado!');
	} // Fim do método unlockBarber

  public function unblockBarberByModerator ($request, $id) 
  {
    $barber = array('enabled' => true);
    $this->barber_repository->update($barber, $id); 
		return JsonHelper::getResponseSucesso('Barbeiro desbloqueado!');
  } // Fim do método blockBarberByModerator

} // Fim da classe