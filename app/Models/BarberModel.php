<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class BarberModel extends AbstractModel
{
	protected $table 				= "barbers";
	protected $tabela 			= "barbers";
	public const AGUARDANDO = 1;
	public const ATIVO 			= 2;
	public const BLOQUEADO 	= 3;
	
	// Busca todos os eventos
	public function getByEmailAndPassword($email, $password)
	{
		return DB::table($this->tabela)
						->where('email', $email)
						->where('password', $password)
						->get();
	} // Fim do método getByEmailAndPassword

	// Busca todos os barbeiros pelo uuid
	public function getByUuid ($uuid) 
	{
		$data = DB::table($this->tabela)->where('uuid', $uuid)->get();
		return self::removePassword($data);
	} // Fim do método getByUuid

	// Busca todos barbeiros pelo e-mail
	public function getByEmail ($email) {
		return DB::table($this->tabela)->where('email', $email)->get();
	} // Fim do método getByEmail

	// Obtem os barbeiros pelo id da barbearia
	public function getByBarbershopId ($barbershop_id, $barbers_ids = []) 
	{
		try {
			$query = DB::table($this->tabela)
						->select(
							'barbers.id',
							'barbers.uuid',
							'barbers.name',
							'barbers.email',
							'barbers.cpf',
							'barbers.enabled',
							'barbers.profile',
							'barbers.phone',
							'barbers.image_url',
							'barbers.barbershop_id',
							'barbers.barber_status_id',
							'barbers_status.id as barber_status_id',
							'barbers_status.name as barber_status_name'
						)
						->Leftjoin('barbers_status', 'barbers.barber_status_id', '=', 'barbers_status.id')
						->where('barbers.barbershop_id', $barbershop_id)
						->where('barbers.barber_status_id', self::ATIVO)
						->where('barbers.enabled', true);
			
			if (count($barbers_ids) > 0)
				$query->whereIn('barbers.id', $barbers_ids);
			
			$data = $query->get();
			return self::formatData($data);
		} catch (Exception $e) { 
			return []; 
		}
	} // Fim do método getByBarbershopId

	private function formatData ($data) 
	{
		if (count($data) == 0)
			return [];

		$barbers = [];
		
		foreach ($data as $item) 
		{
			$barber = array(
				'id'								=> $item->id,
				'uuid'							=> $item->uuid,
				'name'							=> $item->name,
				'email'							=> $item->email,
				'cpf'								=> $item->cpf,
				'enabled'						=> $item->enabled,
				'profile'						=> $item->profile,
				'phone'							=> $item->phone,
				'image_url'					=> $item->image_url,
				'barbershop_id'			=> $item->barbershop_id,
				'barber_status_id'	=> $item->barber_status_id,
				'status'						=> array (
					'id'		=> $item->barber_status_id,
					'name'	=> $item->barber_status_name
				)
			);
			array_push($barbers, $barber);
		}

		return $barbers;
	}

	private function removePassword ($data) 
	{
		$barbers = [];
		foreach ($data as $item) {
			$item  = (object) $item;
			unset($item->password);
			array_push($barbers, $item);
		}
		return $barbers;
	}

	public function getTotalBarbersByBarbershopId ($barbershop_id) 
	{
		try {
			return DB::table($this->tabela)
							->where('barbershop_id', $barbershop_id)
							->where('barber_status_id', BarberModel::ATIVO)
							->count();
		} catch (Exception $e) { 
			return 0; 
		}
	}

	// Confirma o registro
	public function confirmRegister($id) 
	{
		try {
			DB::table($this->tabela)
				->where('id', $id)
				->update(array('enabled' => true));

			return true;
		} catch (Exception $e) {
			return false;
		}
	} // Fim do método confirmRegister

	// Altera a senha
	public function updatePassword ($id, $password) {
		return self::updateData($id, array('password' => $password));
	} // Fim do método updateRange

}  // Fim da classe
