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

	// Obtém as requisições do barbeiro
	public function getRequestByBarberId ($barber_id) 
	{
		try {
			$data = DB::table($this->table)
							->select(
								'barbershops_requests_barbers.*',
								'barbershops.id as barbershop_id',
								'barbershops.name as barbershop_name',
								'barbers.id as barber_id',
								'barbers.name as barber_name'
							)
							->Join('barbers', 'barbers.id', '=', 'barbershops_requests_barbers.barber_id')
							->Join('barbershops', 'barbershops.id', '=', 'barbershops_requests_barbers.barbershop_id')
							->where('barber_id', $barber_id)
							->get();
			
			return self::formatData($data); 
		} catch (DBException $e) {
			return [];
		}
	} // Fim do método getRequestByBarberId

	// Obtém as requisições do barbeiro
	public function getRequestByBarbershopId ($barbershop_id) 
	{
		try {
			$data = DB::table($this->table)
							->select(
								'barbershops_requests_barbers.*',
								'barbershops.id as barbershop_id',
								'barbershops.name as barbershop_name',
								'barbers.id as barber_id',
								'barbers.name as barber_name',
								'barbers.email as barber_email',
								'barbers.image_url as barber_image_url'
							)
							->Join('barbers', 'barbers.id', '=', 'barbershops_requests_barbers.barber_id')
							->Join('barbershops', 'barbershops.id', '=', 'barbershops_requests_barbers.barbershop_id')
							->where('barbershops_requests_barbers.barbershop_id', $barbershop_id)
							->get();
			
			return self::formatData($data); 
		} catch (DBException $e) {
			return [];
		}
	} // Fim do método getRequestByBarberId

	public function formatData ($data) {
		$requests = [];
		foreach ($data as $item) {
			array_push($requests, array(
				'id'						=> $item->id,
				'barber_id'			=> $item->barber_id,
				'barbershop_id'	=> $item->barbershop_id,
				'barber' 				=> array(
					'id'				=> $item->barber_id,
					'name'  		=> $item->barber_name,
					'email'			=> $item->barber_email,
					'image_url' => $item->barber_image_url
				),
				'barbershop' 		=> array(
					'id'		=> $item->barbershop_id,
					'name'	=> $item->barbershop_name
				)
			));
		}

		return $requests;
	}

	public function getById ($id) 
	{
		try {
			return DB::table($this->table)
							->where('id', $id)
							->get(); 
		} catch (DBException $e) {
			return [];
		}
	}

	// Remove uma solicitação
	public function deleteById ($requestId) {
		try {
			DB::table($this->table)->where('id', $requestId)->delete();
			return true;
		} catch (DBException $e) {
			return false;
		}
	} // Fin di método deleteById
}
