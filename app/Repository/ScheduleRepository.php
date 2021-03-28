<?php

namespace App\Repository;

use App\Models\ScheduleModel;

class ScheduleRepository extends AbstractRepository
{
  private $schedule_model;

  public function __construct () {
    parent::__construct((new ScheduleModel));
  }

  public function getByBarbershopDate ($barbershop_id, $date) 
	{
    return $this->model->where('barbershop_id', $barbershop_id)
            ->whereRaw("date(start_date) = '$date'")
            ->get();
	} // Fim do método getByBarbershopDate

  public function getById ($id) 
  {
		$data = $this->model->find($id)->get();
    dd($data);
	} // Fim do método getByUserId

  public function getByUserId ($user_id) {
		return $this->model->where('user_id', $user_id)->get();
	} // Fim do método getByUserId

} // Fim da classe