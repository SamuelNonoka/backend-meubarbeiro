<?php

namespace App\Repository;

use App\Models\ModeratorModel;

class ModeratorRepository extends AbstractRepository 
{
  public function __construct () {
    parent::__construct((new ModeratorModel));
  }

  public function getByNameAndPassword ($name, $password) {
    return $this->model
            ->where('name', $name)
            ->where('password', $password)
            ->first();
	} // Fim do método getByBarbershopId
} // Fim da classe