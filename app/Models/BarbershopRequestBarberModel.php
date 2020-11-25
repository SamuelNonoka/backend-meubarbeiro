<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class BarbershopRequestBarberModel extends Model
{
  protected $table = "barbershops_requests_barbers";

	// Salva a requisição
	public function store ($barber_request) 
	{
		try {
			return DB::table($this->table)->insertGetId($barber_request);
		} catch (DBException $e) {
			return 0;
		}
	} // Fim do método store

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
			
			$requests = [];
			foreach ($data as $item) {
				array_push($requests, array(
					'id'						=> $item->id,
					'barber_id'			=> $item->barber_id,
					'barbershop_id'	=> $item->barbershop_id,
					'barber' 				=> array(
						'id'		=> $item->barber_id,
						'name'  => $item->barber_name
					),
					'barbershop' 		=> array(
						'id'		=> $item->barbershop_id,
						'name'	=> $item->barbershop_name
					)
				));
			}

			return $requests; 
		} catch (DBException $e) {
			return [];
		}
	} // Fim do método getRequestByBarberId

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
