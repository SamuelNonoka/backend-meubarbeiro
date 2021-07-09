<?php

namespace App\Repository;

use App\Models\BarberDeviceTokenModel;

class BarberDeviceTokenRepository extends AbstractRepository 
{
  public function __construct () {
    parent::__construct((new BarberDeviceTokenModel));
  } // fim do Construtor

  public function getByBarberIdAndDeviceToken ($barber_id, $device_token) {
    try {
      return $this->model
                ->where('barber_id', '=', $barber_id)
                ->where('device_token', '=', $device_token)
                ->get();
    } catch (\Illuminate\Database\QueryException $e) {
      return [];
    }
  }

  public function getDeviceTokensByBarber ($barberId) {
    try {
      return $this->model
                ->where('barber_id', '=', $barberId)
                ->get();
    } catch (\Illuminate\Database\QueryException $e) {
      return [];
    }
  }

} // Fim da classe