<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\TokenHelper;
use App\Services\BarberService;

class BarberController extends Controller
{
	private $barber_service;

	public function __construct () {
		$this->barber_service = new BarberService();
	} // Construtor da classe

	public function blockBarber (Request $request, $id) {
		return $this->barber_service->blockBarber($request, $id);
	} // Fim do método blockBarber

	public function blockBarberByModerator (Request $request, $id) {
		return $this->barber_service->blockBarberByModerator($request, $id);
	} // Fim do método blockBarberByModerator

	public function changePassword (Request $request) {
		return $this->barber_service->changePassword($request);
	} // Fim do método changePassword

	public function confirm (Request $request) {
		return $this->barber_service->confirmRegister($request);
	} // Fim do método confirm

	public function crypt () {
		return $this->barber_service->crypt();
	} // Encripta dados

	public function getRevenuesByBarber (Request $request, $barber_id, $barbershop_id) {
		return $this->barber_service->getRevenuesByBarber(
			$request,
			$barber_id,
			$barbershop_id
		);
	}

	public function getByBarbershop (Request $request) 
	{
		$barber	= TokenHelper::getUser($request);
		return $this->barber_service->getByBarbershopId($request, $barber->barbershop_id);
	} // Fim do método getByBarbershopId

	public function getTotalBarbersByBarbershopId ($barbershop_id) {
		return $this->barber_service->getTotalBarbersByBarbershopId($barbershop_id);
	} // Fim do método getTotalBarbersByBarbershopId

	public function index (Request $request) {
		return $this->barber_service->getAll($request);
	}

	public function ranking (Request $request, $barbershop_id) {
		return $this->barber_service->ranking($request, $barbershop_id);
	} // Fim do método recoveryPassword

	public function recoveryPassword (Request $request) {
		return $this->barber_service->recoveryPassword($request);
	} // Fim do método recoveryPassword

	public function resendRegisterMail (Request $request) {
		return $this->barber_service->resendRegisterMail($request);
	}

	public function store (Request $request) {
		return $this->barber_service->store($request);
	} // Fim do método store

	public function storeWithGoogle (Request $request) {
		return $this->barber_service->storeWithGoogle($request);
	} // Fim do método storeWithGoogle

	public function sendInvitation (Request $request) {
		return $this->barber_service->sendInvitation($request);
	} // Fim do método sendInvitation

	public function update (Request $request, $id) {
		return $this->barber_service->update($request, $id);
	} // Fim do método update

	public function uploadImage (Request $request) {
		return $this->barber_service->uploadImage($request);
	} // Fim do método uploadImage

	public function unblockBarber (Request $request, $id) {
		return $this->barber_service->unlockBarber($request, $id);
	} // Fim do método unlockBarber

	public function unblockBarberByModerator (Request $request, $id) {
		return $this->barber_service->unblockBarberByModerator($request, $id);
	} // Fim do método unblockBarberByModerator

	/*** MÉTODOS NÃO UTILIZADOS */

	/*
	* Atualiza o plano do barbeiro
	* Não sei se será utilizado
	**/
	public function updatePlan (Request $request, $id) 
	{
		// Valida a request
		$rules 		= ['plan_id' => 'required'];
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		// Verifica se existe um barbeiro com aquele e-mail cadastrado
		$barber_model = new BarberModel();
		$barber_db 		= $barber_model->getById($id);

		if (count($barber_db) == 0)
			return JsonHelper::getResponseErro('Usuário informado não existe na aplicação!');

		$barber_db = $barber_db[0];
		
		if ($barber_db->plan_id == $request->plan_id)
			return JsonHelper::getResponseErro('Você já possui este plano!');

		$plan_due_date 	= date('Y-m-d');
		$plan_due_date 	= strtotime("+1 months", strtotime($plan_due_date));

		$update = array (
			'plan_id'				=> $request->plan_id,
			'plan_due_date'	=> $plan_due_date
		);

		$saved = $barber_model->updateArray($id, $update);

		if (!$saved)
			return JsonHelper::getResponseErro('Não foi possível alterar o seu plano!');
		
		$barber_db->plan_due_date	= $plan_due_date;
		$barber_db->plan_id				= $request->plan_id;

		$token 		= TokenHelper::atualizarToken($request, $barber_db);
		$payload	= array("token" => $token);

		// Envia o e-mail de troca de plano
		MailHelper::sendChangeBarberPlan($barber_db->email, $barber_db->name, 'Free');
			
		return JsonHelper::getResponseSucesso($payload);
	} // Fim do método updatePlan

	/**
	* Remove o plano
	* Não sei se será utilzado
	**/
	public function cancelPlan(Request $request, $id) 
	{
		// Verifica se existe um barbeiro com aquele e-mail cadastrado
		$barber_model = new BarberModel();
		$barber_db 		= $barber_model->getById($id);

		if (count($barber_db) == 0)
			return JsonHelper::getResponseErro('Usuário informado não existe na aplicação!');

		$barber_db = $barber_db[0];	
		
		if (!$barber_db->plan_id)
			return JsonHelper::getResponseErro('Seu plano já foi cancelado!');
		
		$update =	array (
			'plan_id'				=> null,
			'plan_due_date'	=> null
		);

		$saved = $barber_model->updateArray($id, $update);

		if (!$saved)
			return JsonHelper::getResponseErro('Não foi possível cancelar o seu plano!');
		
		$barber_db->plan_due_date	= null;
		$barber_db->plan_id				= null;

		$token 		= TokenHelper::atualizarToken($request, $barber_db);
		$payload	= array("token" => $token);

		// Envia o e-mail de troca de plano
		MailHelper::sendCancelBarberPlan($barber_db->email, $barber_db->name, 'Free');
			
		return JsonHelper::getResponseSucesso($payload);
	} // Fim do método destroyPlan

} // Fim da classe
