<?php

namespace App\Services;

use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Repository\ScheduleRepository;
use App\Repository\BarbershopRepository;
use App\Services\BarberService;

class ScheduleService
{
  private $schedule_repository;
  private $barber_service;
  private $user_service;

  public function __construct () {
    $this->barbershop_repository  = new BarbershopRepository();
    $this->schedule_repository    = new ScheduleRepository();
    $this->barber_service         = new BarberService();
    $this->user_service           = new UserService();
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

  public function getByBarberId ($request, $barber_id) 
  {
    if (!$request->start_date || !$request->end_date)
      return JsonHelper::getResponseErro("Por favor, informe o período!");

    $start_date = date('z', strtotime($request->start_date));
    $end_date   = date('z', strtotime($request->end_date));
    
    if ($end_date - $start_date >= 7)
      return JsonHelper::getResponseErro("O período máximo de filtro é de uma semana!");
      
    $schedules_db = $this->schedule_repository->getByBarber(
      $barber_id,
      $request->start_date,
      $request->end_date
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

		$data = $this->schedule_repository->getByBarbershopPending($barbershop_id, $barber->id);

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

		$schedule = array ('schedule_status_id' => $this->schedule_repository::REPROVADO);
		$this->update($schedule, $id);
		return JsonHelper::getResponseSucesso('Agendamento reprovado com sucesso!');
	} // Fim do método repprove

} // Fim da classe