<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use DB;

class ScheduleModel extends AbstractModel
{
	protected $tabela = "schedules";

	public const AGUARDANDO = 3;
	public const AGENDADO = 1;
	public const CANCELADO = 2;
	public const REPROVADO = 4;

	// Aprovar agendamento
	public function approve (Request $request, $id) 
	{
		$schedule_db = null;
		try {
			$schedule_db = $this->getById($id);
		} catch (DBException $e) {
			$schedule_db = null;
		}

		if ($schedule_db == null || $schedule_db['schedule_status_id'] != self::AGUARDANDO)
			return JsonHelper::getResponseErro('Não foi possível aprovar o agendamento!');

		$barber = TokenHelper::getUser($request);

		if ($schedule_db['barber_id'] != $barber->id)
			return JsonHelper::getResponseErro('Seu usuário não tem permissão para aprovar o agendamento!');
		
		if ($schedule_db['start_date'] < date('Y-m-d H:i:s'))
			return JsonHelper::getResponseErro('Este agendamento não pode ser aprovado!');

		$schedule = array ('schedule_status_id' => self::AGENDADO);
		$saved = $this->updateData($id, $schedule);

		if (!$saved)
			return JsonHelper::getResponseErro('Não foi possível aprovar o agendamento!');
		else
			return JsonHelper::getResponseSucesso('Agendamento aprovado com sucesso!');
	} // Fim do método approve

	// Reprovar agendamento
	public function repprove (Request $request, $id) 
	{
		$schedule_db = null;
		try {
			$schedule_db = $this->getById($id);
		} catch (DBException $e) {
			$schedule_db = null;
		}

		if ($schedule_db == null || $schedule_db['schedule_status_id'] != self::AGUARDANDO)
			return JsonHelper::getResponseErro('Não foi possível reprovar o agendamento!');

		$barber = TokenHelper::getUser($request);

		if ($schedule_db['barber_id'] != $barber->id)
			return JsonHelper::getResponseErro('Seu usuário não tem permissão para reprovar o agendamento!');
		
		if ($schedule_db['start_date'] < date('Y-m-d H:i:s'))
			return JsonHelper::getResponseErro('Este agendamento não pode ser reprovado!');

		$schedule = array ('schedule_status_id' => self::REPROVADO);
		$saved = $this->updateData($id, $schedule);

		if (!$saved)
			return JsonHelper::getResponseErro('Não foi possível reprovar o agendamento!');
		else
			return JsonHelper::getResponseSucesso('Agendamento reprovado com sucesso!');
	} // Fim do método repprove

	// Obtém os agendamentos pela data
	public function getByBarbershop ($barbershop_id, $date) 
	{
		$where = "schedules.barbershop_id = {$barbershop_id}";
		$where .= " and  DATE(schedules.start_date) = '{$date}'";
		$data = self::getAll($where);
		return JsonHelper::getResponseSucesso($data);
	} // Fim do método getByDate

	// Recupera os agendamentos que estão pendentes
	public function getByBarbershopPending ($barbershop_id, $barber_id) 
	{
		$date = date('Y-m-d');
		$where = "schedules.barbershop_id = {$barbershop_id}";
		$where .= " and  schedules.barber_id = {$barber_id}";
		$where .= " and schedules.schedule_status_id = " . self::AGUARDANDO;
		$where .= " and  DATE(schedules.start_date) >= '{$date}'";
		$data = self::getAll($where);
		
		if (count($data) > 0) {
			$data = $data[0];
			$data = $data['schedules'];
		}

		return JsonHelper::getResponseSucesso($data);
	} // Fim do método getByBarbershopPending
	
	private function getAll ($where)
	{
		try {
			$data = DB::table('barbers')
				->Join('schedules', 'barbers.id', '=', 'schedules.barber_id')
				->Join('users', 'users.id', '=', 'schedules.user_id')
				->Leftjoin('schedules_services', 'schedules_services.schedule_id', '=', 'schedules.id')
				->Leftjoin('services', 'services.id', '=', 'schedules_services.service_id')
				->select(
					'schedules.id',
					'schedules.barbershop_id',
					'schedules.barber_id',
					'schedules.user_id',
					'schedules.schedule_status_id',
					'schedules.start_date',
					'schedules.end_date',
					'schedules.price',
					'barbers.id as barber_id',
					'barbers.name as barber_name',
					'barbers.image_url as barber_image_url',
					'barbers.phone as barber_phone',
					'users.id as user_id',
					'users.name as user_name',
					'users.email as user_email',
					'users.phone_number as user_phone_number',
					'schedules_services.id as schedule_service_id',
					'schedules_services.schedule_id as schedule_service_schedule_id',
					'schedules_services.service_id as schedule_service_service_id',
					'services.id as service_id',
					'services.name as service_name'
				)
				->whereRaw($where)
				//->where('schedules.barbershop_id', $barbershop_id)
				//->whereDate('schedules.start_date', $date)
				->get();

			// Agrupamentos
			$barbers						= [];
			$users							= [];
			$schedules					= [];
			$schedules_services = [];
			$services						= [];

			$user_items								= $data->groupBy('user_id');
			$barbers_items						= $data->groupBy('barber_id');
			$schedules_items					= $data->groupBy('id');
			$schedules_services_items = $data->groupBy('schedule_service_id');
			$services_items						= $data->groupBy('service_id');
			
			// Popula os usuários
			foreach ($user_items as $user_item) 
			{
				$user = array(
					'id'						=> $user_item[0]->user_id,
					'name'					=> $user_item[0]->user_name,
					'email'					=> $user_item[0]->user_email,
					'phone_number'	=> $user_item[0]->user_phone_number
				);
				array_push($users, $user);
			}

			// Popula os usuários
			foreach ($barbers_items as $barber_item) 
			{
				$barber = array(
					'id'				=> $barber_item[0]->barber_id,
					'name'			=> $barber_item[0]->barber_name,
					'image_url'	=> $barber_item[0]->barber_image_url,
					'phone'			=> $barber_item[0]->barber_phone,
					'schedules'	=> [],
				);
				array_push($barbers, $barber);
			}

			// Popula os agendamentos
			foreach ($schedules_items as $schedule_item) 
			{
				$end_date = $schedule_item[0]->end_date;
				$end_date = date("Y-m-d H:i:s",strtotime(date($end_date)." -1 minutes"));

				$schedule = array(
					'id'									=> $schedule_item[0]->id,
					'barbershop_id'				=> $schedule_item[0]->barbershop_id,
					'barber_id'						=> $schedule_item[0]->barber_id,
					'user_id'							=> $schedule_item[0]->user_id,
					'schedule_status_id'	=> $schedule_item[0]->schedule_status_id,
					'start_date'					=> $schedule_item[0]->start_date,
					'end_date'						=> $end_date,
					'price'								=> $schedule_item[0]->price,
					'user'								=> null,
					'services'						=> []
				);
				array_push($schedules, $schedule);
			}

			// Popula os agendamentos
			foreach ($schedules_services_items as $schedule_service_item) 
			{
				$item = array(
					'id'					=> $schedule_service_item[0]->schedule_service_id,
					'service_id'	=> $schedule_service_item[0]->schedule_service_service_id,
					'schedule_id'	=> $schedule_service_item[0]->schedule_service_schedule_id,
					'service'			=> null
				);
				array_push($schedules_services, $item);
			}

			// Popula os agendamentos
			foreach ($services_items as $service_item) 
			{
				$item = array(
					'id'		=> $service_item[0]->service_id,
					'name'	=> $service_item[0]->service_name
				);
				array_push($services, $item);
			}

			// Pupula os servicos no agendamento
			foreach ($services as $service) 
			{
				foreach ($schedules_services as $key => $schedule_service) 
				{
					if ($schedule_service['service_id'] == $service['id'])
						$schedules_services[$key]['service'] = $service;
				}
			}

			// Popula os agendamentos no barbeiro
			foreach($schedules as $schedule) 
			{
				foreach ($users as $user) 
				{
					if ($user['id'] == $schedule['user_id']) {
						$schedule['user'] = $user;
						break;
					}
				}

				foreach ($schedules_services as $schedule_service) 
				{
					if ($schedule_service['schedule_id'] == $schedule['id']) {
						array_push($schedule['services'], $schedule_service['service']);
					}
				}

				foreach ($barbers as $key => $barber) 
				{
					if ($schedule['barber_id'] == $barber['id']) {
						array_push($barbers[$key]['schedules'], $schedule);
						break;
					}
				}
			}
					
			return $barbers;
		} catch (DBException $e) {
			return [];
		}
	} // Fim do método getByDate

	// Obtém os agendamentos pelo ID
	public function getById ($id) 
	{
		try {
			$data = DB::table($this->tabela)
							->join('schedules_status', 'schedules_status.id', '=', 'schedules.schedule_status_id')
							->join('barbers', 'barbers.id', '=', 'schedules.user_id')
							->join('barbershops', 'barbershops.id', '=', 'schedules.barbershop_id')
							->join('addresses', 'addresses.id', '=', 'barbershops.address_id')
							->Leftjoin('schedules_services', 'schedules_services.schedule_id', '=', 'schedules.id')
							->Leftjoin('services', 'services.id', '=', 'schedules_services.service_id')
							->select(
								'schedules.id',
								'schedules.barbershop_id',
								'schedules.barber_id',
								'schedules.user_id',
								'schedules.schedule_status_id',
								'schedules.start_date',
								'schedules.end_date',
								'schedules.price',
								'schedules_status.id as schedules_status_id',
								'schedules_status.name as schedules_status_name',
								'barbers.id as barbers_id',
								'barbers.name as barbers_name',
								'barbers.image_url as barbers_image_url',
								'barbers.phone as barbers_phone',
								'barbershops.id as barbershops_id',
								'barbershops.name as barbershops_name',
								'barbershops.image_url as barbershops_image_url',
								'barbershops.phone_number as barbershops_phone_number',
								'addresses.id as addresses_id',
								'addresses.cep as addresses_cep',
								'addresses.public_place as addresses_public_place',
								'addresses.number as addresses_number',
								'addresses.latitude as addresses_latitude',
								'addresses.longitude as addresses_longitude',
								'addresses.neighborhood as addresses_neighborhood',
								'addresses.city as addresses_city',
								'addresses.uf as addresses_uf',
								'addresses.map_url as addresses_map_url',
								'addresses.complement as addresses_complement',
								'schedules_services.id as schedules_services_id',
								'schedules_services.schedule_id as schedules_services_schedule_id',
								'schedules_services.service_id as schedules_services_service_id',
								'services.id as service_id',
								'services.name as service_name'
							)
							->where('schedules.id', $id)
							->get();

			// Agrupamentos
			$services = [];
			foreach ($data as $item) 
			{
				$service = array(
					'id' 		=>	$item->service_id,
					'name'	=> 	$item->service_name
				);

				array_push($services, $service);
			}					

			$schedule = array(
				'id'									=> $data[0]->id,
				'barbershop_id'				=> $data[0]->barbershop_id,
				'barber_id'						=> $data[0]->barber_id,
				'user_id'							=> $data[0]->user_id,
				'schedule_status_id'	=> $data[0]->schedule_status_id,
				'start_date'					=> $data[0]->start_date,
				'end_date'						=> $data[0]->end_date,
				'price'								=> $data[0]->price,
				'status'							=> array(
					'id'		=> $data[0]->schedules_status_id,
					'name'	=> $data[0]->schedules_status_name
				),
				'barber' => array(
					'id'				=> $data[0]->barbers_id,
					'name'			=> $data[0]->barbers_name,
					'image_url'	=> $data[0]->barbers_image_url,
					'phone'			=> $data[0]->barbers_phone
				),
				'barbershop' => array(
					'id'						=> $data[0]->barbershops_id,
					'name'					=> $data[0]->barbershops_name,
					'image_url'			=> $data[0]->barbershops_image_url,
					'phone_number'	=> $data[0]->barbershops_phone_number,
					'address'				=> array(
						'id'						=> $data[0]->addresses_id,
						'cep'						=> $data[0]->addresses_cep,
						'public_place'	=> $data[0]->addresses_public_place,
						'number'				=> $data[0]->addresses_number,
						'latitude'			=> $data[0]->addresses_latitude,
						'longitude'			=> $data[0]->addresses_longitude,
						'neighborhood'	=> $data[0]->addresses_neighborhood,
						'city'					=> $data[0]->addresses_city,
						'uf'						=> $data[0]->addresses_uf,
						'map_url'				=> $data[0]->addresses_map_url,
						'complement'		=> $data[0]->addresses_complement
					)
				),
				'services'	=> $services
			);

			return $schedule;
		} catch (DBException $e) {
			return [];
		}
	} // Fim do método getById
	
	// Obtem os aendamentos do usuario 
	public function getByUserId ($user_id) 
	{
		try {
			return DB::table($this->tabela)
								->where('user_id', $user_id)
								->get();
		} catch (DBException $e) {
			return [];
		}
	} // Fim do método getById

	public function getFutureAprovedByBarberId ($barber_id) 
	{
		try {
			$date = date('Y-m-d H:i:s');
			return DB::table($this->tabela)
								->where('barber_id', $barber_id)
								->where('schedule_status_id', '=', self::AGENDADO)
								->whereRaw("date(start_date) >= '$date'")
								->get();
		} catch (DBException $e) {
			return [];
		}
	} // Obtem os agendamentos aprovados do barbeiro

	// Obtem os agendamentos pela data da barbearia
	public function getByBarbershopDate ($barbershop_id, $date) 
	{
		try {
			return DB::table($this->tabela)
								->where('barbershop_id', $barbershop_id)
								->whereRaw("date(start_date) = '$date'")
								->get();
		} catch (DBException $e) {
			return [];
		}
	} // Fim do método getByBarbershopDate

	public function getTotalDoneByBarbershopId ($barbershop_id) 
	{
		$date = date('y-m-d H:i:s');
		try {
			return DB::table($this->tabela)
				->where('barbershop_id', $barbershop_id)
				->where('schedule_status_id', self::AGENDADO)
				->whereRaw("date(end_date) <= '$date'")
				->count();
		} catch (Exception $e) { return 0; }
	}

	public function getTotalWaitingByBarbershopId ($barbershop_id) 
	{
		$date = date('y-m-d H:i:s');
		try {
			return DB::table($this->tabela)
				->where('barbershop_id', $barbershop_id)
				->where('schedule_status_id', self::AGUARDANDO)
				->whereRaw("date(end_date) > '$date'")
				->count();
		} catch (Exception $e) { return 0; }
	}

	public function getTotalOfDayByBarbershopId ($barbershop_id) 
	{
		$date = date('y-m-d');
		try {
			return DB::table($this->tabela)
				->where('barbershop_id', $barbershop_id)
				->where('schedule_status_id', self::AGENDADO)
				->whereRaw("date(end_date) = '$date'")
				->count();
		} catch (Exception $e) { return 0; }
	}

} // Fim da classe
