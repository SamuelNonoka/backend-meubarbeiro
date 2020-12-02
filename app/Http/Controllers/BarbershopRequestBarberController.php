<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarberModel;
use App\Models\BarbershopModel;
use App\Models\BarbershopRequestBarberModel;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;

class BarbershopRequestBarberController extends Controller
{
	// Busca as requisições da barbearia
	public function barbershopRequestsByBarbershop (Request $request, $id) {
		$barber = TokenHelper::getUser($request);
		
		if ($barber->barbershop_id != $id)
			return JsonHelper::getResponseErro('Seu usuário não tem permissão para listar as solicitações!');

		$data = (new BarbershopRequestBarberModel)->getRequestByBarbershopId($id);
		return JsonHelper::getResponseSucesso($data);
	} // fim do método barbershopRequestsByBarbershop

	public function cancelByBarber (Request $request, $id) 
	{
		$barber		= TokenHelper::getUser($request);
		$model 		= new BarbershopRequestBarberModel();
		$requests = $model->getById($id);
		
		if (count($requests) == 0)
			return JsonHelper::getResponseErro('Não foi possível cancelar a solicitação!');
		
		$barber_request = $requests[0];
		
		if ($barber->id != $barber_request->barber_id)
			return JsonHelper::getResponseErro('Você não tem permissão para cancelar esta solicitação!');
			
		$deleted = $model->deleteById($id);

		if (!$deleted)
			return JsonHelper::getResponseErro('Não foi possível cancelar a solicitação!');
		
		return JsonHelper::getResponseSucesso('Solicitação cancelada com sucesso!');
	} // Fim do método cancelByBarber

	// Aprova solicitação do barbeiro
	public function approve (Request $request, $id) {
		$model			= new BarbershopRequestBarberModel();
		$request_db = $model->getById($id);
		
		if (count($request_db) == 0)
			return JsonHelper::getResponseErro('Não foi possível localizar a solicitação!');

		$barbershop_db = (new BarbershopModel)->getById($request_db[0]->barbershop_id);
		if (count($barbershop_db) == 0)
			return JsonHelper::getResponseErro('Não foi possível localizar a barbearia!');

		$barber					= TokenHelper::getUser($request);
		$barbershop_db 	= (object) $barbershop_db;

		if ($barbershop_db->admin_id != $barber->id)
			return JsonHelper::getResponseErro('Seu usuário não têm permissão para reprovar essa solicitação!');
		
		$barber_arr = array(
			'barbershop_id' 		=> $barbershop_db->id,
			'barber_status_id'	=> BarberModel::ATIVO
		);
		
		$saved = (new BarberModel)->updateData($request_db[0]->barber_id, $barber_arr);
		
		if (!$saved)
			return JsonHelper::getResponseErro('Não foi possível aprovar a solicitação!');
		
		$model->deleteById($id);
		return JsonHelper::getResponseSucesso('Solicitação aprovada!');
	} // Fim do método approve

	// Reprova solicitação do barbeiro
	public function reprove (Request $request, $id) {
		$model			= new BarbershopRequestBarberModel();
		$request_db = $model->getById($id);
		if (count($request_db) == 0)
			return JsonHelper::getResponseErro('Não foi possível localizar a solicitação!');

		$barbershop_db = (new BarbershopModel)->getById($request_db[0]->barbershop_id);
		if (count($barbershop_db) == 0)
			return JsonHelper::getResponseErro('Não foi possível localizar a barbearia!');

		$barber					= TokenHelper::getUser($request);
		$barbershop_db 	= (object) $barbershop_db;

		if ($barbershop_db->admin_id != $barber->id)
			return JsonHelper::getResponseErro('Seu usuário não têm permissão para reprovar essa solicitação!');

		$deleted = $model->deleteById($id);

		if (!$deleted)
			return JsonHelper::getResponseErro('Não foi possível remover a solicitação!');
		
		return JsonHelper::getResponseSucesso('Solicitação reprovada!');
	} // Fim do método reprove
}
