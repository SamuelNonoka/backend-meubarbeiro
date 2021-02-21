<?php

namespace App\Repository;

use App\Models\BarbershopRequestBarberModel;

class BarbershopRequestBarberRepository extends AbstractRepository
{
  protected $table = 'barbershops_requests_barbers';
  
  public function __construct () {
    parent::__construct((new BarbershopRequestBarberModel));
  }

  public function getByBarberId ($barber_id) {
    $data = $this->model->where('barber_id', $barber_id)->get();

    foreach ($data as $key => $item) {
      $barber = $item->barber;
      unset($barber->password);
      
      $data[$key]['barber']     = $barber;
      $barbershop               = $item->barbershop;
      $data[$key]['barbershop'] = $barbershop;
    }
    
    return $data;
  }

  public function store ($barber_request) {
    return $this->model->insertGetId($barber_request);
  }

}  // Gim da classe