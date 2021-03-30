<?php

namespace App\Services;

use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Repository\ScheduleRepository;
use App\Services\BarberService;

class ScheduleService
{
  private $schedule_repository;
  private $barber_service;
  private $user_service;

  public function __construct () {
    $this->schedule_repository = new ScheduleRepository();
    $this->barber_service      = new BarberService();
    $this->user_service        = new UserService();
  }

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

} // Fim da classe