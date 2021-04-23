<?php

namespace App\Repository;

use App\Models\ScheduleServiceModel;

class ScheduleServiceRepository extends AbstractRepository
{
  public function __construct () {
    parent::__construct((new ScheduleServiceModel));
  }
  
} // Fim da Classe