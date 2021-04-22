<?php

namespace App\Repository;

use App\Models\ServiceModel;

class ServiceRepository extends AbstractRepository
{
  public function __construct () {
    parent::__construct((new ServiceModel));
  }

  public function getByBarbershopId($barbershop_id) {
    return $this->model->where('services.barbershop_id', $barbershop_id)->get();
	} // Fim d

} // Fim da classe