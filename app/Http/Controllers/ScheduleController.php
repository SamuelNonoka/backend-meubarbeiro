<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Models\BarbershopModel;
use App\Models\ScheduleModel;
use App\Models\ScheduleServiceModel;
use App\Services\ScheduleService;

class ScheduleController extends Controller
{
	private $schedule_service;

	protected $rules = [
		'barbershop_id'	=> 'required',
		'user_id'				=> 'required',
		'barber_id'			=> 'required',
		'services'			=> 'required',
		'start_date'		=> 'required',
		'end_date'			=> 'required'
	];

	public function __construct () {
		$this->schedule_service = new ScheduleService();
	}

	public function approve (Request $request, $id) {
		return $this->schedule_service->approve($request, $id);
	} // Fim do método approve

	public function getByBarberId (Request $request, $barber_id) {
		return $this->schedule_service->getByBarberId($request, $barber_id);
	} // Fim do método getByBarberId

	public function getByBarbershopDate (Request $request, $barbershop_id) {
		return $this->schedule_service->getByBarbershopDate($request, $barbershop_id);
	} // Fim do método index

	public function getByBarbershopPending (Request $request, $barbershop_id) {
		return $this->schedule_service->getByBarbershopPending($request, $barbershop_id);
	} // Fim do método getByBarbershopPending

	public function getByUserId ($user_id) {
		return $this->schedule_service->getByUserId($user_id);
	} // Fim do método getByUserId

	public function repprove (Request $request, $id) {
		return $this->schedule_service->repprove($request, $id);
	} // Fim do método repprove

	public function show ($id) {
		return $this->schedule_service->getById($id);
	} // Fim do método show

	// Faz um agendamento
	public function store (Request $request) 
	{
		// Valida a request
		$invalido = ValidacaoHelper::validar($request->all(), $this->rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		// Recupera o preco
		$price = 0;
		foreach ($request->services as $service) {
			$price += (float) $service['price'];
		}

		$schedule = array (
			'barbershop_id' 			=> $request->barbershop_id,
			'barber_id'						=> $request->barber_id,
			'user_id'							=> $request->user_id,
			'schedule_status_id'	=> ScheduleModel::AGUARDANDO,
			'start_date'					=> $request->start_date,
			'end_date'						=> $request->end_date,
			'price'								=> $price
		);

		// Salva a agenda
		$schedule_id = (new ScheduleModel)->storeObjeto($schedule);

		if ($schedule_id == 0)
			return JsonHelper::getResponseErro('Não foi possível salvar o agendamento!');

		$schedule['id'] = $schedule_id;

		// Salva os relacionamentos
		$schedule_service_model = new ScheduleServiceModel();

		foreach ($request->services as $service) {
			$schedule_service = array (
				'schedule_id'	=> $schedule_id,
				'service_id'	=> $service['id']
			);

			$schedule_service_model->storeObjeto($schedule_service);
		}
		
		return JsonHelper::getResponseSucesso($schedule); 
	} // Fim da classe

	// Usuário cancela o agendamento
	public function cancelByUser (Request $request, $id) 
	{
		if (!$request->user_id)
			return JsonHelper::getResponseErro('Por favor, informe o seu usuário!'); 
		
		$schedule_model = new ScheduleModel(); 
		$schedule_db = $schedule_model->getById($id);

		if ($schedule_db['user_id'] != $request->user_id)
			return JsonHelper::getResponseErro('Seu usuário não tem permissão para editar este agendamento!');
			
		if ($schedule_db['schedule_status_id'] != ScheduleModel::AGUARDANDO)
			return JsonHelper::getResponseErro('Este agendamento não pode ser editado!');

		$schedule_date 	= $schedule_db['start_date'];
		$schedule_date 	= date('Y-m-d H:i', strtotime('+1 hour',strtotime($schedule_date)));
		$date_now				= date('Y-m-d H:i');
			
		if ($schedule_date < $date_now)
			return JsonHelper::getResponseErro('Este agendamento não pode ser cancelado!');

		$schedule = array('schedule_status_id' => ScheduleModel::CANCELADO);
		$saved 		= $schedule_model->updateData($id, $schedule);
		
		if (!$saved)
			return JsonHelper::getResponseErro('Não foi possível cancelar este agendamento!');

		return JsonHelper::getResponseSucesso('Agendamento cancelado com sucesso!');
	} // Fim do método cancelByUser

	public function getTotalDoneByBarbershopId ($barbershop_id) 
	{
		$data = (new ScheduleModel)->getTotalDoneByBarbershopId($barbershop_id);
		return JsonHelper::getResponseSucesso($data);
	}

	public function getTotalWaitingByBarbershopId ($barbershop_id) 
	{
		$data = (new ScheduleModel)->getTotalWaitingByBarbershopId($barbershop_id);
		return JsonHelper::getResponseSucesso($data);
	}

	public function getTotalOfDayByBarbershopId ($barbershop_id) 
	{
		$data = (new ScheduleModel)->getTotalOfDayByBarbershopId($barbershop_id);
		return JsonHelper::getResponseSucesso($data);
	}

} // Fim da classe
