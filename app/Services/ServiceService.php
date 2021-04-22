<?php

namespace App\Services;

use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Repository\ServiceRepository;

class ServiceService 
{
  private $service_repository;
  protected $rules = [
		'name'						=>	'required|max:50',
		'price' 					=> 'required',
		'service_type_id'	=> 'required',
		'duration_time'		=> 'required'
	];


  public function __construct () {
    $this->service_repository = new ServiceRepository();
  }

  public function destroy ($request, $id) 
	{
		$service  = $this->service_repository->getByid($id);
		$barber   = TokenHelper::getUser($request);
		
		if ($barber->barbershop_id != $service->barbershop_id)
			return JsonHelper::getResponseErroPermissao('Você não tem permissão para editar esse serviço!');
	
		$this->service_repository->remove($id);
		return JsonHelper::getResponseSucesso('Serviço removido com sucesso!');
	} // Fim do método destroy

  public function getByBarbershopId ($barbershop_id) 
  {
    $data = $this->service_repository->getByBarbershopId($barbershop_id);
		return JsonHelper::getResponseSucesso($data); 
	} // Fim do método getByBarbershopId

  public function store ($request) 
	{
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

		$id             = $this->service_repository->store($service);
		$service['id']  = $id;
		
		return JsonHelper::getResponseSucesso($service);
	} // Fim do método que store

  public function update ($request, $id) 
	{
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

		$this->service_repository->update($service, $id);
		return JsonHelper::getResponseSucesso($service);
	} // Fim do método update

} // Fim da classe