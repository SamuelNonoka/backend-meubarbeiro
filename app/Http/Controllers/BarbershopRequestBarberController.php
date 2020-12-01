<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
	}
}
