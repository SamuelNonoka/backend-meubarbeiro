<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Services\BarberService;
use App\Services\BarbershopService;

class BarbershopController extends Controller
{
  private $barbershop_service;
  private $barber_service;

  public function __construct () {
    $this->barber_service = new BarberService();
    $this->barbershop_service = new BarbershopService();
  } // fim do construtor

  public function blockBarbershopByModerator (Request $request, $id) {
		return $this->barbershop_service->blockBarbershopByModerator($request, $id);
	} // Fim do método blockBarbershopByModerator

  public function getAllPaginated (Request $request) {
    return $this->barbershop_service->getAllPaginated($request);
  }

  public function getBarbers ($id) {
    return $this->barber_service->getByBarbershopId($id);
  } // Fim do método getBarbers

  public function index (Request $request) 
  {
    if ($request->name)
      return $this->barbershop_service->getByName($request->name);
    return $this->barbershop_service->getAllEnabled();
	} // Fim do método index

	public function show ($id) {
		return $this->barbershop_service->getById($id);
  } // Fim do método show

  public function store(Request $request) {
    return $this->barbershop_service->store($request);
  } // Fim do método store

  public function total ($id) {
    return $this->barbershop_service->getTotal($id);
  } // Fim do método total

  public function unblockBarbershopByModerator (Request $request, $id) {
		return $this->barbershop_service->unblockBarbershopByModerator($request, $id);
	} // Fim do método unblockBarbershopByModerator

  public function update (Request $request, $id) {
    return $this->barbershop_service->update($request, $id);
  } // Atualiza os dados da barbearia

	public function uploadImage (Request $request) {
		return $this->barbershop_service->uploadImage($request);
  } // Fim do método uploadImage
  
  public function uploadBackgroundImage (Request $request) {
		return $this->barbershop_service->uploadBackgroundImage($request);
  } // Fim do método uploadImage
 
} // Fim da classe