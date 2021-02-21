<?php

namespace App\Services;

use App\Services\BarberService;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Repository\BarberRepository;
use App\Repository\BarbershopRepository;
use App\Repository\BarbershopRequestBarberRepository;

class BarbershopService 
{
  private $barbershop_repository;

  public function __construct () {
    $this->barber_repository                    = new BarberRepository();
    $this->barbershop_repository                = new BarbershopRepository();
    $this->barbershop_request_barber_repository = new BarbershopRequestBarberRepository();
  }

  public function getByName ($name) {
    return JsonHelper::getResponseSucesso($this->barbershop_repository->getByName($name)); 
  } // Fim do método getByName

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
      $barbershop_requests_db[$key]['barber'] = (new BarberService)->decrypt($barbershop_request['barber']);
    }
    
    return JsonHelper::getResponseSucesso($barbershop_requests_db);
  } // Fim do método sendBarberRequest

} // Fim da classe