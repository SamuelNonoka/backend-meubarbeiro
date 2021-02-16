<?php

namespace App\Services;

use App\Helpers\JsonHelper;
use App\Repository\BarbershopRepository;

class BarbershopService 
{
  private $barbershop_repository;

  public function __construct () {
    $this->barbershop_repository = new BarbershopRepository();
  }

  public function getByName ($name) {
    return JsonHelper::getResponseSucesso($this->barbershop_repository->getByName($name)); 
  } // Fim do método getByName

} // Fim da classe