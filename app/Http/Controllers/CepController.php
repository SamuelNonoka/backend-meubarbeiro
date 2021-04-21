<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CepService;

class CepController extends Controller
{
	private $cep_service;

	public function __construct() {
		$this->cep_service = new CepService();
	}

	public function getCepFromViaCep ($cep) {
		return $this->cep_service->getCepFromViaCep($cep);
	} // Fim do método cep

} // Fim da classe
