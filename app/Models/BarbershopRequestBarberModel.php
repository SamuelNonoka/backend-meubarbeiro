<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BarberModel;
use App\Models\BarbershopModel;
use DB;

class BarbershopRequestBarberModel extends AbstractModel
{
  protected $table = "barbershops_requests_barbers";

	public function barber () {
		return $this->belongsTo('App\Models\BarberModel');
	}

	public function barbershop () {
		return $this->belongsTo('App\Models\BarbershopModel');
	}

} // Fim da classe
