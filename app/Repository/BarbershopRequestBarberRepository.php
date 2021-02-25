<?php

namespace App\Repository;

use App\Models\BarbershopRequestBarberModel;

class BarbershopRequestBarberRepository extends AbstractRepository
{
  protected $table = 'barbershops_requests_barbers';
  
  public function __construct () {
    parent::__construct((new BarbershopRequestBarberModel));
  }

  public function deleteById ($id) {
    $this->model->destroy($id);
  }

  private function formatData ($data) {
    foreach ($data as $key => $item) {
      $barber = $item->barber;
      unset($barber->password);
      
      $data[$key]['barber']     = $barber;
      $barbershop               = $item->barbershop;
      $data[$key]['barbershop'] = $barbershop;
    }

    return $data;
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

  public function getByBarbershopId ($barbershop_id) {
    $data = $this->model->where('barbershop_id', $barbershop_id)->get();
    $data = $this->formatData($data);
    return $data;
  }

  public function getById ($id) {
    $data = $this->model->find($id);

    if (!$data)
      return $data;
    
    $barber = $data->barber;
    unset($barber->password);
    
    $data['barber']     = $barber;
    $barbershop         = $data->barbershop;
    $data['barbershop'] = $barbershop;
    
    return $data;
  }

  public function store ($barber_request) {
    return $this->model->insertGetId($barber_request);
  }

}  // Gim da classe