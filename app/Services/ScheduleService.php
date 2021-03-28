<?php

namespace App\Services;

use App\Helpers\JsonHelper;
use App\Repository\ScheduleRepository;

class ScheduleService
{
  private $schedule_repository;

  public function __construct () {
    $this->schedule_repository = new ScheduleRepository();
  }

  public function getById ($id) 
  {
    $schedules = $this->schedule_repository->getById($id);
		return JsonHelper::getResponseSucesso($schedules);
  }

  public function getByUserId ($user_id) 
  {
    $schedules = $this->schedule_repository->getByUserId($user_id);
		return JsonHelper::getResponseSucesso($schedules);
  } // Fim do método getByUserId

} // Fim da classe