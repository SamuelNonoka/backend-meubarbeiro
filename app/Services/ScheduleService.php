<?php

namespace App\Services;

use App\Helpers\JsonHelper;
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