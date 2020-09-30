<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Models\ScheduleModel;
use App\Models\ScheduleServiceModel;

class ScheduleController extends Controller
{
	protected $rules = [
		'barbershop_id'	=> 'required',
		'user_id'				=> 'required',
		'barber_id'			=> 'required',
		'services'			=> 'required',
		'start_date'		=> 'required',
		'end_date'			=> 'required'
	];

	// Busca todos os agendamentos
	public function getByBarbershopDate (Request $request, $barbershop_id) 
	{
		if (!$request->date)
			return JsonHelper::getResponseErro("Informe a data para filtrar os agendamentos!");

		$barber = TokenHelper::getUser($request);
		
		if ($barber->barbershop_id != $barbershop_id)
			return JsonHelper::getResponseErro("Seu usuário não tem permissão para recuperar esses dados!");

		return (new ScheduleModel)->getByBarbershop($barbershop_id, $request->date);
	} // Fim do método index

	// Obtem os agendamentos pendentes de aprovação da barbearia
	public function getByBarbershopPending (Request $request, $barbershop_id) 
	{
		$barber = TokenHelper::getUser($request);
		
		if ($barber->barbershop_id != $barbershop_id)
			return JsonHelper::getResponseErro("Seu usuário não tem permissão para recuperar esses dados!");

		return (new ScheduleModel)->getByBarbershopPending($barbershop_id, $barber->id);
	} // Fim do método getByBarbershopPending

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

	// Aprovar agendamento
	public function approve (Request $request, $id) {
		return (new ScheduleModel)->approve($request, $id);
	} // Fim do método approve

	// Reprovar agendamento
	public function repprove (Request $request, $id) {
		return (new ScheduleModel)->repprove($request, $id);
	} // Fim do método repprove

	// Mostra os dados do agendamento
	public function show ($id) 
	{
		$data = (new ScheduleModel)->getById($id);
		return JsonHelper::getResponseSucesso($data);
	} // Fim do método show

	// Lista os agendamentos do usuário
	public function getByUserId ($user_id) 
	{
		$schedules = (new ScheduleModel)->getByUserId($user_id);
		return JsonHelper::getResponseSucesso($schedules);
	} // Fim do método getByUserId

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

} // Fim da classe
