<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarberModel;
use App\Models\BarbershopModel;
use App\Models\BarbershopRequestBarberModel;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Services\BarbershopRequestBarberService;

class BarbershopRequestBarberController extends Controller
{	
	private $barbershop_request_barber_service;

  public function __construct () {
    $this->barbershop_request_barber_service = new BarbershopRequestBarberService();
  } // Fim do construtor

	public function barberRequest (Request $request) {
    return $this->barbershop_request_barber_service->sendBarberRequest($request);
  } // Barbeiro solicita fazer parte da barbearia

	public function checkBarbershopRequest (Request $request) {
		return $this->barbershop_request_barber_service->checkBarbershopRequest($request);
	} // Fim do método checkBarbershopRequest

	public function barbershopRequestsByBarbershop (Request $request, $id) {
		return $this->barbershop_request_barber_service->getByBarbershopId($request, $id);
	} // fim do método barbershopRequestsByBarbershop

	public function cancelByBarber (Request $request, $id) {
		return $this->barbershop_request_barber_service->cancelByBarber($request, $id);
	} // Fim do método cancelByBarber

	public function approve (Request $request, $id) {
		return $this->barbershop_request_barber_service->approve($request, $id);	
	} // Fim do método approve

	public function reprove (Request $request, $id) {
		return $this->barbershop_request_barber_service->reprove($request, $id);
	} // Fim do método reprove
}
