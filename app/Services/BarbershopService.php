<?php

namespace App\Services;

use App\Services\BarberService;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Repository\AddressRepository;
use App\Repository\BarberRepository;
use App\Repository\BarbershopRepository;
use App\Repository\BarbershopScheduleDayRepository;
use App\Repository\ScheduleRepository;
use DB;

class BarbershopService 
{
  private $address_repository;
  private $barber_repository;
  private $barbershop_repository;
  private $barbershop_schedule_day_repository;
  private $barber_service;

  public function __construct () {
    $this->address_repository     = new AddressRepository();
    $this->barber_repository      = new BarberRepository();
    $this->barbershop_repository  = new BarbershopRepository();
    $this->barbershop_schedule_day_repository = new BarbershopScheduleDayRepository();
    $this->barber_service         = new BarberService();
    $this->schedule_repository    = new ScheduleRepository();
  }

  public function blockBarbershopByModerator ($request, $id) 
  {
    $barbershop = array('barbershop_status_id' => $this->barbershop_repository::BLOQUEADO);
    $this->barbershop_repository->update($barbershop, $id); 
		return JsonHelper::getResponseSucesso('Barbearia bloqueada!');
  } // Fim do método blockBarbershopByModerator

  public function getAllEnabled () {
    return JsonHelper::getResponseSucesso($this->barbershop_repository->getAllEnabled());
  } // Fim do método getAll

  public function getAllPaginated ($request) 
  {
    $order  = $request->order ?? null; 
    $status = $request->status ?? null;
    $data   = $this->barbershop_repository->getAllPaginated($request->search, $status, $order);
    return JsonHelper::getResponseSucesso($data);
  }

  public function getAll () {
    return JsonHelper::getResponseSucesso($this->barbershop_repository->getAll());
  } // Fim do método getAll

  public function getById ($id) 
  {
    $data = $this->barbershop_repository->getById($id);
    return JsonHelper::getResponseSucesso($data);
  } // Fim da classe getById

  public function getByName ($name) {
    return JsonHelper::getResponseSucesso($this->barbershop_repository->getByName($name)); 
  } // Fim do método getByName

  public function getTotal ($request, $barbershop_id) 
  {
    $total = array(
      'barbers'   => $this->barber_repository->getTotalByBarbershopId($barbershop_id),
      'schedules' => $this->schedule_repository->getTotalByBarbershopId($request, $barbershop_id),
      'revenues'  => $this->schedule_repository->getTotalRevenuesByBarbershopId($request, $barbershop_id)
    );
    return JsonHelper::getResponseSucesso($total);
  }

  public function store ($request) {
    $rules = [
			'name'			=> 'required|max:50',
      'barber_id' => 'required'
    ];

    $messages = [
      'name.required' => 'O nome da barbearia deve ser informado',
      'name.max'      => 'O nome da barbearia deve ter no máximo 50 caracteres',
      'barber_id'     => 'Informe o barbeiro'
    ];
		
		$invalido = ValidacaoHelper::validar($request->all(), $rules, $messages);
    
    if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

    $barber_db = $this->barber_repository->getById($request->barber_id);
    
    if (!$barber_db)
      return JsonHelper::getResponseErro('O barbeiro informado não existe na plataforma.');

    if (!$barber_db->enabled)
      return JsonHelper::getResponseErro('Seu acesso está bloqueado na plataforma.');

    if ($barber_db->barbershop_id)
      return JsonHelper::getResponseErro('Você já possui uma barbearia cadastrada na plataforma.');

    $barbershop_arr = array (
      'admin_id'  => $barber_db->id,
      'name'      => $request->name
    );

    DB::beginTransaction();
    $id = $this->barbershop_repository->store($barbershop_arr);

    if ($id == 0)
      return JsonHelper::getResponseErro('Não foi possível salvar a barbearia.');

    $barber_arr = array('barbershop_id' => $id);
    $this->barber_repository->update($barber_arr, $barber_db->id); 
      
    DB::commit();
    $barber_db->barbershop_id = $id;
      
    $barber_db  = $this->barber_service->decrypt($barber_db);
    $token   	  = TokenHelper::atualizarToken($request, $barber_db);
    $payload	  = array("token" => $token);
  
    return JsonHelper::getResponseSucesso($payload);
  } // Fim do método store

  public function unblockBarbershopByModerator ($request, $id) 
  {
    $barbershop = array('barbershop_status_id' => $this->barbershop_repository::ATIVO);
    $this->barbershop_repository->update($barbershop, $id); 
		return JsonHelper::getResponseSucesso('Barbearia desbloqueada!');
  } // Fim do método blockBarbershopByModerator

  public function update ($request, $id) 
  {
    $barber_arr = $request->only(['name', 'description', 'phone_number', 'instagram_url', 'facebook_url']);

    if ($request->address) {
      $address_request = (object) $request->address;
      $address_arr     = [];
    
      $address_arr['cep']           = $address_request->cep ?? null;
      $address_arr['public_place']  = $address_request->public_place ?? null;
      $address_arr['number']        = $address_request->number ?? null;
      $address_arr['neighborhood']  = $address_request->neighborhood ?? null;
      $address_arr['city']          = $address_request->city ?? null;
      $address_arr['uf']            = $address_request->uf ?? null;
      $address_arr['map_url']       = $address_request->map_url ?? null;
      $address_arr['complement']    = $address_request->complement ?? null;

      if ($request->address_id) {
        $this->address_repository->update($address_arr, $request->address_id);
      } else {
        $address_id = $this->address_repository->store($address_arr);
        if ($address_id != 0)
          $barber_arr['address_id'] = $address_id;
      }
    }

    // Schedules days
    if ($request->schedules_days) 
    {
      $schedules    = $request->schedules_days;
      $schedules_db = $this->barbershop_schedule_day_repository->getByBarbershopId($id);

      // Faz o loop com os horários enviados
      foreach ($schedules as $schedule) 
      {
        $has_schedule   = false;
        $schedule_db_id = null;

        foreach ($schedules_db as $schedule_db) 
        {
          if ($schedule['schedule_day_id'] == $schedule_db->schedule_day_id) {
            $schedule_db_id = $schedule_db->id;
            $has_schedule   = true;
            break;
          }
        } // Fim do loop dos horários do db

        $schedule_arr = array (
          'schedule_day_id' => $schedule['schedule_day_id'],
          'barbershop_id'   => $id,
          'open'            => true,
          'start'           => $schedule['start'],
          'end'             => $schedule['end']
        );

        if ($has_schedule) {
          if ($schedule['open']) {
            $this->barbershop_schedule_day_repository->update($schedule_arr, $schedule_db_id);
          } else {
            $this->barbershop_scheduleday_model->remove($schedule_db_id);
          }
        } else if ($schedule['open']) {
          $this->barbershop_schedule_day_repository->store($schedule_arr);
        }
      } // Fim do loop
    
    } // Fim do schedules days

    $this->barbershop_repository->update($barber_arr, $id); 
    
    $barbershop_db  = $this->barbershop_repository->getById($id);
    $token   	      = TokenHelper::atualizarToken($request, $barbershop_db);
		$payload	      = array(
      'token'       => $token, 
      'barbershop'  => $barbershop_db
    );

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

    // Salva a imagem
    $barbershop_id    = $barber->barbershop_id;                
		$image 						= $request->file('img');
		$name							= $barber->uuid . rand(10, 99);
		$name 						=	$name .'.'.$image->getClientOriginalExtension();
		$path							= '/storage/uploads/barbershop/profile/' . $barbershop_id;
    $destinationPath 	= public_path($path);
    $imagePath	 			= $destinationPath. "/".  $name;
		$image->move($destinationPath, $name);
		
		// Atualiza o barbeiro
		$path 				    = $path . '/' . $name;
		$barbershop_arr   = array('image_url' => $path);
		$this->barbershop_repository->update($barbershop_arr, $barbershop_id);
    return JsonHelper::getResponseSucesso($path);
  } // Fim do método uploadImage

  public function uploadBackgroundImage ($request) 
	{
		$barber 	= TokenHelper::getUser($request);
		$rules 		= ['img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'];
		$invalido	= ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		if (!$request->hasFile('img'))
			return JsonHelper::getResponseErro('Por favor, envie uma imagem');

    // Salva a imagem
    $barbershop_id    = $barber->barbershop_id;                
		$image 						= $request->file('img');
		$name							= $barber->uuid . rand(10, 99);
		$name 						=	$name .'.'.$image->getClientOriginalExtension();
		$path							= '/storage/uploads/barbershop/background/' . $barbershop_id;
    $destinationPath 	= public_path($path);
    $imagePath	 			= $destinationPath. "/".  $name;
		$image->move($destinationPath, $name);
		
		// Atualiza o barbeiro
		$path 				    = $path . '/' . $name;
		$barbershop_arr   = array('background_url' => $path);
		$this->barbershop_repository->update($barbershop_arr, $barbershop_id);
		return JsonHelper::getResponseSucesso($path);
  } // Fim do método uploadImage

} // Fim da classe