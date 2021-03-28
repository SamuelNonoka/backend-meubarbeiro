<?php

namespace App\Repository;

use Illuminate\Database\QueryException as DBException;
use App\Models\BarbershopScheduleDayModel;

class BarbershopScheduleDayRepository extends AbstractRepository 
{
  public function __construct () {
    parent::__construct((new BarbershopScheduleDayModel));
  }

  public function getByBarbershopId ($barbershop_id) {
    return $this->model->where('barbershop_id', $barbershop_id)->get();
	} // Fim do método getByBarbershopId
} // Fim da classe