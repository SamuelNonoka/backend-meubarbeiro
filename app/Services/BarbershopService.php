<?php

namespace App\Services;

use App\Services\BarberService;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Repository\BarberRepository;
use App\Repository\BarbershopRepository;
use DB;

class BarbershopService 
{
  private $barber_repository;
  private $barbershop_repository;
  private $barber_service;

  public function __construct () {
    $this->barber_repository      = new BarberRepository();
    $this->barbershop_repository  = new BarbershopRepository();
    $this->barber_service         = new BarberService();
  }

  public function getAllEnabled () {
    return JsonHelper::getResponseSucesso($this->barbershop_repository->getAllEnabled());
  } // Fim do método getAll

  public function getAll () {
    return JsonHelper::getResponseSucesso($this->barbershop_repository->getAll());
  } // Fim do método getAll

  public function getByName ($name) {
    return JsonHelper::getResponseSucesso($this->barbershop_repository->getByName($name)); 
  } // Fim do método getByName

  public function store ($request) {
    $rules = [
			'name'			=> 'required|max:50',
      'barber_id' => 'required'
    ];

    $messages = [
      'name.required' => 'O nome da barbearia deve ser informado',
      'name.max'      => 'O nome da barbearia deve ter no máximo 50 caracteres',
      'barber_id'     => 'Informe o barbeiro'
    ];
		
		$invalido = ValidacaoHelper::validar($request->all(), $rules, $messages);
    
    if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

    $barber_db = $this->barber_repository->getById($request->barber_id);
    
    if (!$barber_db)
      return JsonHelper::getResponseErro('O barbeiro informado não existe na plataforma.');

    if (!$barber_db->enabled)
      return JsonHelper::getResponseErro('Seu acesso está bloqueado na plataforma.');

    if ($barber_db->barbershop_id)
      return JsonHelper::getResponseErro('Você já possui uma barbearia cadastrada na plataforma.');

    $barbershop_arr = array (
      'admin_id'  => $barber_db->id,
      'name'      => $request->name
    );

    DB::beginTransaction();
    $id = $this->barbershop_repository->store($barbershop_arr);

    if ($id == 0)
      return JsonHelper::getResponseErro('Não foi possível salvar a barbearia.');

    $barber_arr = array('barbershop_id' => $id);
    $this->barber_repository->update($barber_arr, $barber_db->id); 
      
    DB::commit();
    $barber_db->barbershop_id = $id;
      
    $barber_db  = $this->barber_service->decrypt($barber_db);
    $token   	  = TokenHelper::atualizarToken($request, $barber_db);
    $payload	  = array("token" => $token);
  
    return JsonHelper::getResponseSucesso($payload);
  } // Fim do método store

} // Fim da classe