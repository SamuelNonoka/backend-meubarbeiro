<?php

namespace App\Services;

use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Repository\BarberRepository;
use App\Repository\BarbershopRequestBarberRepository;
use App\Services\BarberService;

class BarbershopRequestBarberService 
{
  private $barbershop_request_barber_repository;
  private $barber_repository;
  private $barber_service;

  public function __construct () {
    $this->barbershop_request_barber_repository = new BarbershopRequestBarberRepository();
    $this->barber_repository                    = new BarberRepository();
    $this->barber_service                       = new BarberService();
  } // Construtor

  public function cancelByBarber ($request, $id) {
		$barbershop_request_db = $this->barbershop_request_barber_repository->getById($id);
    
		if (!$barbershop_request_db)
			return JsonHelper::getResponseErro('Não foi possível cancelar a solicitação!');
		
    $barber = TokenHelper::getUser($request);
		
		if ($barber->id != $barbershop_request_db->barber_id)
			return JsonHelper::getResponseErro('Você não tem permissão para cancelar esta solicitação!');
			
		$this->barbershop_request_barber_repository->deleteById($id);
	
		return JsonHelper::getResponseSucesso('Solicitação cancelada com sucesso!');
  } // Cancela a solicitação enviada a barbearia

  public function checkBarbershopRequest ($request) {
    $barber 			          = TokenHelper::getUser($request);
		$barber_db 		          = $this->barber_repository->getById($barber->id);
		$barbershop_requests_db = $this->barbershop_request_barber_repository->getByBarberId($barber_db->id);
		
    foreach ($barbershop_requests_db as $key => $barbershop_request) {
      $barbershop_requests_db[$key]['barber'] = $this->barber_service->decrypt($barbershop_request['barber']);
    }
    
    return JsonHelper::getResponseSucesso($barbershop_requests_db);
  } // Verifica as solicitações feitas por um barbeiro

  public function sendBarberRequest ($request) 
  {
    if (!$request->barbershop_id)
			return JsonHelper::getResponseErro('Você precisa informar o código da barbearia!');

    $barber                 = TokenHelper::getUser($request);
    $barber_db 	            = $this->barber_repository->getById($barber->id);
    $barbershop_requests_db = $this->barbershop_request_barber_repository->getByBarberId($barber_db->id);
    
    if (count($barbershop_requests_db) > 0)
			return JsonHelper::getResponseErro('Você já enviou uma solicitação!');
		
		$barber_request = array(
			'barber_id'			=> $barber->id,
			'barbershop_id'	=> $request->barbershop_id
		);

    $id = $this->barbershop_request_barber_repository->store($barber_request);
  
		if (!$id > 0)
			return JsonHelper::getResponseErro('Não foi possível enviar sua solicitação!');

		$barbershop_requests_db = $this->barbershop_request_barber_repository->getByBarberId($barber->id);
		
    foreach ($barbershop_requests_db as $key => $barbershop_request) {
      $barbershop_requests_db[$key]['barber'] = $this->barber_service->decrypt($barbershop_request['barber']);
    }
    
    return JsonHelper::getResponseSucesso($barbershop_requests_db);
  } // Fim do método sendBarberRequest

} // Fim da classe