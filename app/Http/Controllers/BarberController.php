<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\JsonHelper;
use App\Helpers\MailHelper;
use App\Helpers\TokenHelper;
use App\Helpers\EncriptacaoHelper;
use App\Helpers\ValidacaoHelper;
use App\Models\BarberModel;
use App\Models\BarbershopModel;
use App\Models\BarbershopRequestBarberModel;
use App\Models\ScheduleModel;
use App\Services\BarberService;

class BarberController extends Controller
{
	private $barber_service;

	public function __construct () {
		$this->barber_service = new BarberService();
	}

	public function changePassword (Request $request) {
		return $this->barber_service->changePassword($request);
	} // Fim do método changePassword

	public function crypt () {
		return $this->barber_service->crypt();
	}

	public function store (Request $request) {
		return $this->barber_service->store($request);
	} // Fim do método store

	public function storeWithGoogle (Request $request) {
		return $this->barber_service->storeWithGoogle($request);
	} // Fim do método storeWithGoogle

	public function recoveryPassword (Request $request) {
		return $this->barber_service->recoveryPassword($request);
	} // Fim do método recoveryPassword

	public function update (Request $request, $id) {
		return $this->barber_service->update($request, $id);
	} // Fim do método update

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

	// Faz o upload de uma nova imagem para o barbeiro
	public function uploadImage (Request $request) 
	{
		$barber 	= TokenHelper::getUser($request);
		$rules 		= ['img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'];
		$invalido	= ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		if (!$request->hasFile('img'))
			return JsonHelper::getResponseErro('Por favor, envie uma imagem');

		// Salva a imagem 
		$image 						= $request->file('img');
		$name							= $barber->uuid . rand(10, 99);
		$name 						=	$name .'.'.$image->getClientOriginalExtension();
		$path							= '/storage/uploads/barbers/profile/' . $barber->uuid;
    $destinationPath 	= public_path($path);
    $imagePath	 			= $destinationPath. "/".  $name;
		$image->move($destinationPath, $name);
		
		// Atualiza o barbeiro
		$path 				= $path . '/' . $name;
		$barber_arr 	= array('image_url' => $path);
		$barber_model	= new BarberModel();
		$barber_model->updateArray($barber->id, $barber_arr);
		
		$barber_db 	= $barber_model->getById($barber->id);
		$token			= TokenHelper::gerarTokenBarber ($request, $barber_db[0]);
		return JsonHelper::getResponseSucesso($token);
	} // Fim do método uploadImage

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

	// Confirme o cadastro do barbeiro
	public function confirm (Request $request) 
	{
		if (!$request->token)
			return JsonHelper::getResponseErro('Não foi possível confirmar o seu cadastro :( !');

		$barber_model = new BarberModel();
		$barber_db		= $barber_model->getByUuid($request->token);

		if (count($barber_db) == 0)
			return JsonHelper::getResponseErro('Não foi possível confirmar o seu cadastro :( !');
		
		$confirm = $barber_model->confirmRegister($barber_db[0]->id);

		if (!$confirm)
		return JsonHelper::getResponseErro('Não foi possível confirmar o seu cadastro :( !');

		return JsonHelper::getResponseSucesso('Cadastro confirmado :) !');
	} // Fim do método confirm

	// Busca barbeiro pelo id da barbearia
	public function getByBarbershop (Request $request) 
	{
		$barber	= TokenHelper::getUser($request);
		$data = (new BarberModel)->getByBarbershopId($barber->barbershop_id);
		return JsonHelper::getResponseSucesso($data);
	} // Fim do método getByBarbershopId

	public function getTotalBarbersByBarbershopId ($barbershop_id) 
	{
		$data 	= (new BarberModel)->getTotalBarbersByBarbershopId($barbershop_id);
		return JsonHelper::getResponseSucesso($data);
	}

	// Envia convite para barbeiro
	public function sendInvitation (Request $request) 
	{
		$rules = [ 'email' => 'required|max:50' ];
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
			return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");

		$barber_model = new BarberModel();
		$barber_db 		= $barber_model->getByEmail($request->email);
		
		if (count($barber_db) > 0) {
			if ($barber_db[0]->barber_status_id != $barber_model::AGUARDANDO)
				return JsonHelper::getResponseErro('Este barbeiro já está cadastrado!');
		}

		$barber					= TokenHelper::getUser($request);
		$barbershop_db 	= (new BarbershopModel)->getById($barber->barbershop_id);
		$token					= array(
			'barbershop_id'	=> $barber->barbershop_id,
			'barber_mail'		=> $request->email					
		);
		$expiration			= date('Y-m-d H:i', strtotime('+1 day'));
		$token					= TokenHelper::setToken($request, $token, $expiration);
		$sended 				= MailHelper::sendBarberInvitation($request->email, $barbershop_db['name'], $token);
		
		if (!$sended)
			JsonHelper::getResponseErro('Não foi possível enviar o e-mail!');

		return JsonHelper::getResponseSucesso('Convite enviado para o barbeiro!');
	} // Fim do método sendInvitation

	// Bloqueia barbeiro
	public function blockBarber (Request $request, $id) 
	{
		$barber_model = new BarberModel();
		$barber_db 		= $barber_model->getById($id);
		$barber 			= TokenHelper::getUser($request);
		
		if (count($barber_db) == 0)
			return JsonHelper::getResponseErro('Não foi possível localizar o barbeiro!'); 
		
		$barber_db 			= $barber_db[0];
		$barbershop_db	= (new BarbershopModel)->getById($barber_db->barbershop_id);
		
		if ($barbershop_db == null)
			return JsonHelper::getResponseErro('Não foi possível localizar a barbearia!');

		if ($barbershop_db['id'] != $barber->barbershop_id || $barbershop_db['admin_id'] != $barber->id)
			return JsonHelper::getResponseErro('Você não tem permissão para realizar essa ação!');

		$schedules = (new ScheduleModel)->getFutureAprovedByBarberId($barber_db->id);
		
		if (count($schedules) > 0)
			return JsonHelper::getResponseErro('Não é possível bloquear o barbeiro pois ele tem agendamentos pendentes!');

		$barber 	= array('barber_status_id' => $barber_model::BLOQUEADO);
		$updated	= $barber_model->updateData($id, $barber); 

		if (!$updated)
			return JsonHelper::getResponseErro('Não foi possível bloquear o barbeiro!');

		return JsonHelper::getResponseSucesso('Barbeiro bloqueado!');
	} // Fim do método blockBarber

	// Desbloqueio barbeiro
	public function unlockBarber (Request $request, $id) 
	{
		$barber_model = new BarberModel();
		$barber_db 		= $barber_model->getById($id);
		$barber 			= TokenHelper::getUser($request);
		
		if (count($barber_db) == 0)
			return JsonHelper::getResponseErro('Não foi possível localizar o barbeiro!'); 
		
		$barber_db 			= $barber_db[0];
		$barbershop_db	= (new BarbershopModel)->getById($barber_db->barbershop_id);
		
		if ($barbershop_db == null)
			return JsonHelper::getResponseErro('Não foi possível localizar a barbearia!');

		if ($barbershop_db['id'] != $barber->barbershop_id || $barbershop_db['admin_id'] != $barber->id)
			return JsonHelper::getResponseErro('Você não tem permissão para realizar essa ação!');

		$barber 	= array('barber_status_id' => $barber_model::ATIVO);
		$updated	= $barber_model->updateData($id, $barber); 

		if (!$updated)
			return JsonHelper::getResponseErro('Não foi possível desbloquear o barbeiro!');

		return JsonHelper::getResponseSucesso('Barbeiro desbloqueado!');
	} // Fim do método unlockBarber

	// Verifica se o barbeiro já enviou uma solicitação
	public function checkBarbershopRequest (Request $request) 
	{
		$barber_model = new BarberModel();
		$barber 			= TokenHelper::getUser($request);
		$barber_db 		= $barber_model->getById($barber->id);
		$barber_db		= $barber_db[0];
		$requests 		= (new BarbershopRequestBarberModel)->getRequestByBarberId($barber_db->id);
		return JsonHelper::getResponseSucesso($requests);
	} // Fim do método checkBarbershopRequest

	// envia uma solicitação à barbearia
	public function barbershopRequest (Request $request) 
	{
		if (!$request->barbershop_id)
			return JsonHelper::getResponseErro('Você precisa informar o código da barbearia!');

		$barbershop_request_barber_model = new BarbershopRequestBarberModel();
		$barber_model = new BarberModel();
		$barber 			= TokenHelper::getUser($request);
		$barber_db 		= $barber_model->getById($barber->id);
		$barber_db		= $barber_db[0];
		$requests 		= $barbershop_request_barber_model->getRequestByBarberId($barber_db->id);

		if (count($requests) > 0)
			return JsonHelper::getResponseErro('Você já enviou uma solicitação!');
		
		$barber_request = array(
			'barber_id'			=> $barber->id,
			'barbershop_id'	=> $request->barbershop_id
		);

		$id = $barbershop_request_barber_model->store($barber_request);

		if (!$id > 0)
			return JsonHelper::getResponseErro('Não foi possível enviar sua solicitação!');

		$requests = $barbershop_request_barber_model->getRequestByBarberId($barber->id);
		return JsonHelper::getResponseSucesso($requests);
	} // Fim do método checkBarbershopRequest

} // Fim da classe
