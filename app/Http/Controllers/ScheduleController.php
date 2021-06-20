<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\TokenHelper;
use App\Services\ScheduleService;

class ScheduleController extends Controller
{
	private $schedule_service;

	public function __construct () {
		$this->schedule_service = new ScheduleService();
	} // Fim do construtor

	public function approve (Request $request, $id) {
		return $this->schedule_service->approve($request, $id);
	} // Fim do método approve

	public function cancelByUser (Request $request, $id) {
		return $this->schedule_service->cancelByUser($request, $id);
	} // Fim do método cancelByUser

	public function getByBarberId (Request $request, $barber_id, $barbershop_id) {
		return $this->schedule_service->getByBarberId($request, $barber_id, $barbershop_id);
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

	public function getTotalByBarber (Request $request, $barber_id, $barbershop_id) {
		return $this->schedule_service->getTotalByBarber($request, $barber_id, $barbershop_id);
	}

	public function getTotalDoneByBarbershopId ($barbershop_id) {
		return $this->schedule_service->getTotalDoneByBarbershopId($barbershop_id);
	}

	public function getTotalOfDayByBarbershopId ($barbershop_id) {
		return $this->schedule_service->getTotalOfDayByBarbershopId($barbershop_id);
	}

	public function getTotalWaitingByBarbershopId ($barbershop_id) {
		return $this->schedule_service->getTotalWaitingByBarbershopId($barbershop_id);
	}

	public function getTotalPendingByBarbershop ($barbershop_id) {
		return $this->schedule_service->getTotalPendingByBarbershop($barbershop_id);
	} // Fim do método qtdWaitingToApprove

	public function removePendingSchedules () {
		return $this->schedule_service->removePendingSchedules();
	} // Fim do método removerSolicitacoesNaoAtendidas

	public function repprove (Request $request, $id) {
		return $this->schedule_service->repprove($request, $id);
	} // Fim do método repprove

	public function show ($id) {
		return $this->schedule_service->getById($id);
	} // Fim do método show

	public function store (Request $request) {
		return $this->schedule_service->store($request); 
	} // Fim da classe

} // Fim da classe
