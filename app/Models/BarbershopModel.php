<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use  App\Models\AddressModel;
use DB;

class BarbershopModel extends AbstractModel
{
	protected $table = "barbershops";
	protected $tabela = "barbershops";
	public const AGUARDANDO = 1;
	public const ATIVA = 2;
	public const BLOQUEADA = 3;

	public function address() {
		return $this->belongsTo('App\Models\AddressModel');
	}
	
	// Busca todas as Barbearias
	public function getAll ($name = null) 
	{
		$query = DB::table($this->tabela)
			->select(
				'barbershops.*',
				'addresses.id as address_id',
				'addresses.cep as address_cep',
				'addresses.public_place as address_public_place',
				'addresses.number as address_number',
				'addresses.neighborhood as address_neighborhood',
				'addresses.city as address_city',
				'addresses.uf as address_uf',
				'addresses.map_url as address_map_url',
				'addresses.complement as address_complement',
				'bsd.id as bsd_id',
				'bsd.barbershop_id as bsd_barbershop_id',
				'bsd.schedule_day_id as bsd_schedule_day_id',
				'bsd.start as bsd_start',
				'bsd.end as bsd_end',
				'bsd.open as bsd_open'
			)
			->Leftjoin('addresses', 'barbershops.address_id', '=', 'addresses.id')
			->Leftjoin('barbershops_schedules_days as bsd', 'bsd.barbershop_id', '=', 'barbershops.id')
			->Leftjoin('services', 'services.barbershop_id', '=', 'barbershops.id')
			->where('barbershops.barbershop_status_id', self::ATIVA);

		if ($name) {
			$name = '%' . $name . '%';
			$query->where('barbershops.name', 'like', $name);
		}
			
		$data = $query->get();

		return self::formatData($data); 
	} // Fim do método getAll

  // Busca todos os barbeiros pelo Id
	public function getById ($id) 
	{
		try {
			$data = DB::table($this->tabela)
				->select(
					'barbershops.*',
					'addresses.id as address_id',
					'addresses.cep as address_cep',
					'addresses.public_place as address_public_place',
					'addresses.number as address_number',
					'addresses.neighborhood as address_neighborhood',
					'addresses.city as address_city',
					'addresses.uf as address_uf',
					'addresses.map_url as address_map_url',
					'addresses.complement as address_complement',
					'bsd.id as bsd_id',
					'bsd.barbershop_id as bsd_barbershop_id',
					'bsd.schedule_day_id as bsd_schedule_day_id',
					'bsd.start as bsd_start',
					'bsd.end as bsd_end',
					'bsd.open as bsd_open'
				)
				->Leftjoin('addresses', 'barbershops.address_id', '=', 'addresses.id')
				->Leftjoin('barbershops_schedules_days as bsd', 'bsd.barbershop_id', '=', 'barbershops.id')
				->where('barbershops.id', $id)
				->get();
			
			if (count($data) > 0) 
			{
				$data = self::formatData($data);
				$data = $data[0];
			}

			//dd($data);

			return $data;
		} 
		catch (DBException $e) {
			return [];
		}
	} // Fim do método getById

	// Formata os dados para listagem
	private function formatData ($data) 
	{
		if (count($data) > 0) 
		{
			$barbershops_db = $data->groupBy('id');
			$schedules 			= $data->groupBy('bsd_id')->toArray();
			$schedule_days 	= [];
			$barbershops		= [];
			$services				= [];

			// Loop nas barbearias
			foreach ($barbershops_db as $item) 
			{
				$barbershop = array (
					'id'              => $item[0]->id,
					'address_id'      => $item[0]->address_id,
					'admin_id'        => $item[0]->admin_id,
					'name'            => $item[0]->name,
					'image_url'       => $item[0]->image_url,
					'background_url'  => $item[0]->background_url,
					'description'     => $item[0]->description,
					'phone_number'    => $item[0]->phone_number,
					'instagram_url'   => $item[0]->instagram_url,  
					'facebook_url'    => $item[0]->facebook_url,
					'address'        	=> array(
						'id' 						=> $item[0]->address_id,
						'cep'						=> $item[0]->address_cep,
						'public_place'	=> $item[0]->address_public_place,
						'number'				=> $item[0]->address_number,
						'neighborhood'	=> $item[0]->address_neighborhood,
						'city'					=> $item[0]->address_city,
						'uf'						=> $item[0]->address_uf,
						'map_url'				=> $item[0]->address_map_url,
						'complement'		=> $item[0]->address_complement
					),
					'schedule_days'		=> [],
					'services'				=> []
				);
				
				array_push($barbershops, $barbershop);
			} // Fim do loop nas barbearias

			// Horários da barbearia
			foreach ($schedules as $item) 
			{
				$schedule = array(
					'id'							=> $item[0]->bsd_id,
					'barbershop_id'		=> $item[0]->bsd_barbershop_id,
					'schedule_day_id'	=> $item[0]->bsd_schedule_day_id,
					'start'						=> $item[0]->bsd_start,
					'end'							=> $item[0]->bsd_end,
					'open'						=> $item[0]->bsd_open
				);
				array_push($schedule_days, $schedule);
			} // Fim dos horários da barberaria

			// Popula os horários na barbearia
			foreach ($barbershops as $k => $barbershop) 
			{
				foreach ($schedule_days as $schedule_db) 
				{
					if ($schedule_db['barbershop_id'] == $barbershop['id']) {
						array_push($barbershops[$k]['schedule_days'], $schedule_db);
					}
				}
			}
			
			return $barbershops;
		} 
		else 
			return [];
	} // Fim do método formatData

} // Fim da classe
