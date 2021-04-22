<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ServiceService;

class ServiceController extends Controller
{
	private $service_service;

	public function __construct () {
		$this->service_service = new ServiceService();
	}

	public function destroy (Request $request, $id) {
		return $this->service_service->destroy($request, $id);
	} // Fim do método destroy

	public function getByBarbershopId ($barbershop_id) {
		return $this->service_service->getByBarbershopId($barbershop_id);
	} // Fim do método getByBarbershopId

	public function store (Request $request) {
		return $this->service_service->store($request);
	} // Fim do método que store

	public function update (Request $request, $id) {
		return $this->service_service->update($request, $id);
	} // Fim do método update

} // Fim da classe
