<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Models\ServiceModel;

class ServiceController extends Controller
{
	protected $rules = [
		'name'						=>	'required|max:50',
		'price' 					=> 'required',
		'service_type_id'	=> 'required',
		'duration_time'		=> 'required'
	];

	// Salva um serviço
	public function store (Request $request) 
	{
		// Valida a request
		$invalido = ValidacaoHelper::validar($request->all(), $this->rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		$barber 				= TokenHelper::getUser($request);
		$barbershop_id 	= $barber->barbershop_id;

		$service = array(
			'service_type_id'	=> $request->service_type_id,
			'name'						=> $request->name,
			'price'						=> $request->price,
			'duration_time'		=> $request->duration_time,
			'barbershop_id'		=> $barbershop_id
		);

		$id = (new ServiceModel)->storeObjeto($service);

		if ($id == 0)
			return JsonHelper::getResponseErro('Não foi possível cadastrar o serviço!');

		$service['id'] = $id;
		
		return JsonHelper::getResponseSucesso($service);
	} // Fim do método que store

	// Atualiza os dados do serviço
	public function update (Request $request, $id) 
	{
		// Valida a request
		$invalido = ValidacaoHelper::validar($request->all(), $this->rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		$service = array(
			'service_type_id'	=> $request->service_type_id,
			'name'						=> $request->name,
			'price'						=> $request->price,
			'duration_time'		=> $request->duration_time,
			'barbershop_id'		=> $request->barbershop_id
		);

		$barber = TokenHelper::getUser($request);
		
		if ($barber->barbershop_id != $request->barbershop_id)
			return JsonHelper::getResponseErroPermissao('Você não tem permissão para editar esse serviço!');

		$saved = (new ServiceModel)->updateData($id, $service);

		if (!$saved)
			return JsonHelper::getResponseErro('Não foi possível salvar o serviço!');
		
		return JsonHelper::getResponseSucesso($service);
	} // Fim do método update

	// Fim do método getByBarbershopId
	public function getByBarbershopId ($barbershop_id) 
	{
		$data = (new ServiceModel)->getByBarbershopId($barbershop_id);
		return JsonHelper::getResponseSucesso($data);
	} // Fim do método getByBarbershopId

	// Deleta um servico
	public function destroy (Request $request, $id) 
	{
		$service_model 	= new ServiceModel();
		$service				= $service_model->getByid($id);
		$barber 				= TokenHelper::getUser($request);
		
		if ($barber->barbershop_id != $service[0]->barbershop_id)
			return JsonHelper::getResponseErroPermissao('Você não tem permissão para editar esse serviço!');
	
		$deleted = $service_model->remove($id);
		
		if (!$deleted)
			return JsonHelper::getResponseErro('Não foi possível remover o serviço!');
		
		return JsonHelper::getResponseSucesso('Serviço removido com sucesso!');
	} // Fim do método destroy

} // Fim da classe
