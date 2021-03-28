<?php

namespace App\Repository;

use App\Models\ScheduleModel;

class ScheduleRepository 
{
  private $schedule_model;

  public function __construct () {
    $this->schedule_model = new ScheduleModel();
  }

  public function getByBarbershopDate ($barbershop_id, $date) 
	{
    return $this->schedule_model->where('barbershop_id', $barbershop_id)
            ->whereRaw("date(start_date) = '$date'")
            ->get();
	} // Fim do método getByBarbershopDate
} // Fim da classe