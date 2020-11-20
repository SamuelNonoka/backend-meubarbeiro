<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class BarbershopRequestBarberModel extends Model
{
  protected $table = "barbershops_requests_barbers";

	// Obtém as requisições do barbeiro
	public function getRequestByBarberId ($barber_id) 
	{
		try {
			return DB::table($this->table)
							->where('barber_id', $barber_id)
							->get();
		} catch (DBException $e) {
			return [];
		}
	} // Fim do método getRequestByBarberId
}
