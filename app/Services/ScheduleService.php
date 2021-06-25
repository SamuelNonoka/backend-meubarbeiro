<?php

namespace App\Services;

use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Repository\ScheduleRepository;
use App\Repository\ScheduleServiceRepository;
use App\Repository\BarbershopRepository;
use App\Services\BarberService;

class ScheduleService
{
  private $schedule_repository;
  private $schedule_service_repository;
  private $barber_service;
  private $user_service;

  protected $rules = [
		'barbershop_id'	=> 'required',
		'user_id'				=> 'required',
		'barber_id'			=> 'required',
		'services'			=> 'required',
		'start_date'		=> 'required',
		'end_date'			=> 'required'
	];

  public function __construct () {
    $this->barbershop_repository        = new BarbershopRepository();
    $this->schedule_repository          = new ScheduleRepository();
    $this->schedule_service_repository  = new ScheduleServiceRepository();
    $this->barber_service               = new BarberService();
    $this->user_service                 = new UserService();
  }

  public function approve ($request, $id) 
  {
    $schedule_db = $this->schedule_repository->getById($id);

    if (!$schedule_db || $schedule_db['schedule_status_id'] != $this->schedule_repository::AGUARDANDO)
      return JsonHelper::getResponseErro('Não foi possível aprovar o agendamento!');
		
    $barber = TokenHelper::getUser($request);

    if ($schedule_db['barber_id'] != $barber->id)
			return JsonHelper::getResponseErro('Seu usuário não tem permissão para aprovar o agendamento!');
		
		if ($schedule_db['start_date'] < date('Y-m-d H:i:s'))
			return JsonHelper::getResponseErro('Este agendamento não pode ser aprovado!');
		
    $schedule = array ('schedule_status_id' => $this->schedule_repository::AGENDADO);
    $this->schedule_repository->update($schedule, $id);

		return JsonHelper::getResponseSucesso('Agendamento aprovado com sucesso!');
  } // Fim do método approve

  public function cancelByUser ($request, $id) 
	{
		if (!$request->user_id)
			return JsonHelper::getResponseErro('Por favor, informe o seu usuário!'); 
		
		$schedule_db = $this->schedule_repository->getById($id);

		if ($schedule_db->user_id != $request->user_id)
			return JsonHelper::getResponseErro('Seu usuário não tem permissão para editar este agendamento!');
			
		if ($schedule_db->schedule_status_id != $this->schedule_repository::AGUARDANDO)
			return JsonHelper::getResponseErro('Este agendamento não pode ser editado!');

		$schedule_date 	= $schedule_db->start_date;
		$schedule_date 	= date('Y-m-d H:i', strtotime('+1 hour',strtotime($schedule_date)));
		$date_now				= date('Y-m-d H:i');
			
		if ($schedule_date < $date_now)
			return JsonHelper::getResponseErro('Este agendamento não pode ser cancelado!');

		$schedule = array('schedule_status_id' => $this->schedule_repository::CANCELADO);
		$this->schedule_repository->update($schedule, $id);
		return JsonHelper::getResponseSucesso('Agendamento cancelado com sucesso!');
	} // Fim do método cancelByUser

  public function getByBarberId ($request, $barber_id, $barbershop_id) 
  {
    if (!$request->start_date || !$request->end_date)
      return JsonHelper::getResponseErro("Por favor, informe o período!");
      
    $schedules_db = $this->schedule_repository->getByBarber(
      $barber_id,
      $barbershop_id,
      $request->start_date ?? null,
      $request->end_date ?? null
    );
    
    foreach ($schedules_db as $key => $schedule) 
    {
      $schedules_db[$key]['barber'] = $this->barber_service->decrypt($schedule->barber);
      $schedules_db[$key]['user']   = $this->user_service->decrypt($schedule->user);
      unset( $schedules_db[$key]['barber']['password']);
      unset( $schedules_db[$key]['user']['password']);
    }

    return JsonHelper::getResponseSucesso($schedules_db);
  } // Fim do método getByBarberId

  public function getByBarbershopDate ($request, $barbershop_id) 
  {
    if (!$request->date)
			return JsonHelper::getResponseErro("Informe a data para filtrar os agendamentos!");

		$barber = TokenHelper::getUser($request);
		
		if ($barber->barbershop_id != $barbershop_id)
			return JsonHelper::getResponseErro("Seu usuário não tem permissão para recuperar esses dados!");

		$barbershop_db 	=$this->barbershop_repository->getById($barbershop_id);
    $barber_id 			= ($barber->id != $barbershop_db['admin_id']) ? $barber->id : null;

		$data = $this->schedule_repository->getByBarbershop($barbershop_id, $request->date, $barber_id);

    foreach ($data as $key => $item) {
      unset( $item['password']);
      $data[$key] = $this->barber_service->decrypt((object) $item);
      
      foreach ($item['schedules'] as $i => $schedule) {
        unset( $schedule['user']['password']);
        $user = $schedule['user'];
        $user = $this->user_service->decrypt($user);
        $data[$key]->schedules[$i]['user'] = $user;
      }
    }

    return JsonHelper::getResponseSucesso($data);
  } // Fim do método getByBarbershopDate

  public function getByBarbershopPending ($request, $barbershop_id) 
	{
		$barber = TokenHelper::getUser($request);
		
		if ($barber->barbershop_id != $barbershop_id)
			return JsonHelper::getResponseErro("Seu usuário não tem permissão para recuperar esses dados!");

    $filters = Array(
      'start_date'    => $request->start_date ?? null,
      'end_date'      => $request->end_date ?? null,
      'all_requests'  => $request->all_requests ?? null
    );
		$data = $this->schedule_repository->getByBarbershopPending($barbershop_id, $barber->id, $filters);

    foreach ($data as $key => $item) {
      $data[$key]->barber = $this->barber_service->decrypt($item->barber);
      $data[$key]->user   = $this->user_service->decrypt($item->user);
    }

    return JsonHelper::getResponseSucesso($data);
  } // Fim do método getByBarbershopPending

  public function getById ($id) 
  {
    $schedule         = $this->schedule_repository->getById($id);
    $schedule->barber = $this->barber_service->decrypt($schedule->barber);
    $schedule->user   = $this->user_service->decrypt($schedule->user);
		return JsonHelper::getResponseSucesso($schedule);
  }

  public function getByUserId ($user_id) 
  {
    $schedules = $this->schedule_repository->getByUserId($user_id);
		return JsonHelper::getResponseSucesso($schedules);
  } // Fim do método getByUserId

  public function getTotalPendingByBarbershop ($barbershop_id) {
		$data = $this->schedule_repository->getTotalPendingByBarbershop($barbershop_id);
    return JsonHelper::getResponseSucesso($data);
  } // Fim do método getTotalPendingByBarbershop

  public function getTotalByBarber ($request, $barber_id) {
    $data = $this->schedule_repository->getTotalByBarber(
      $barber_id,
      $request->start_date,
      $request->end_date
    );
    return JsonHelper::getResponseSucesso($data);
  } // Fim do método getTotalByBarber

  public function getTotalDoneByBarbershopId ($barbershop_id) 
  {
    $data = $this->schedule_repository->getTotalDoneByBarbershopId($barbershop_id);
    return JsonHelper::getResponseSucesso($data); 
  } // Fim do método getTotalDoneByBarbershopId

  public function getTotalOfDayByBarbershopId ($barbershop_id) 
  {
    $data = $this->schedule_repository->getTotalOfDayByBarbershopId($barbershop_id);
    return JsonHelper::getResponseSucesso($data); 
  } // Fim do método getTotalDoneByBarbershopId

  public function getTotalWaitingByBarbershopId ($barbershop_id) 
  {
    $data = $this->schedule_repository->getTotalWaitingByBarbershopId($barbershop_id);
    return JsonHelper::getResponseSucesso($data); 
  } // Fim do método getTotalWaitingByBarbershopId

  public function removePendingSchedules () {
    $this->schedule_repository->removePendingSchedulesClosed();
    return JsonHelper::getResponseSucesso('Agendamentos não atendidos removidos!'); 
  } // Fim do método removerSolicitacoesNaoAtendidas

  public function repprove ($request, $id) 
  {
    $schedule_db = $this->schedule_repository->getById($id);
		
		if (!$schedule_db || $schedule_db['schedule_status_id'] != $this->schedule_repository::AGUARDANDO)
			return JsonHelper::getResponseErro('Não foi possível reprovar o agendamento!');

		$barber = TokenHelper::getUser($request);

		if ($schedule_db['barber_id'] != $barber->id)
			return JsonHelper::getResponseErro('Seu usuário não tem permissão para reprovar o agendamento!');
		
		if ($schedule_db['start_date'] < date('Y-m-d H:i:s'))
			return JsonHelper::getResponseErro('Este agendamento não pode ser reprovado!');

		$schedule = array (
      'schedule_status_id'  => $this->schedule_repository::REPROVADO,
      'cancellation_reason' => $request->message ?? null
    );
    $this->schedule_repository->update($schedule, $id);
		return JsonHelper::getResponseSucesso('Agendamento reprovado com sucesso!');
	} // Fim do método repprove

  public function store ($request) 
	{
		$invalido = ValidacaoHelper::validar($request->all(), $this->rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		$price = 0;
		foreach ($request->services as $service) {
			$price += (float) $service['price'];
		}

		$schedule = array (
			'barbershop_id' 			=> $request->barbershop_id,
			'barber_id'						=> $request->barber_id,
			'user_id'							=> $request->user_id,
			'schedule_status_id'	=> $this->schedule_repository::AGUARDANDO,
			'start_date'					=> $request->start_date,
			'end_date'						=> $request->end_date,
			'price'								=> $price,
      'observation'         => $request->observation ?? null
		);

		$schedule_id    = $this->schedule_repository->store($schedule);
		$schedule['id'] = $schedule_id;

		foreach ($request->services as $service) 
    {
			$schedule_service = array (
				'schedule_id'	=> $schedule_id,
				'service_id'	=> $service['id']
			);
			$this->schedule_service_repository->store($schedule_service);
		}
		
		return JsonHelper::getResponseSucesso($schedule); 
	} // Fim da classe

} // Fim da classe