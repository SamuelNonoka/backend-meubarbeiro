<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerfilModel;
use App\Helpers\JsonHelper;
use App\Objects\PerfilObject;

// Class responsável pela api de perfis
class PerfilController extends Controller
{
	private $perfil_model;

	// Construtor da classe
	public function __construct() {
		$this->perfil_model = new PerfilModel();
	}	// Fim do construtor

	/**
	* Lista todas os perfis
	* @return Array
	*/
	public function index() 
	{
		$perfis_db	= $this->perfil_model->get();
		$perfis 		= [];

		foreach($perfis_db as $perfil_db)
		{
			$perfil_db 			= (object) $perfil_db;
			$perfil_object	= new PerfilObject();
			$perfil_object->setObjectFromDB($perfil_db);
			array_push($perfis, $perfil_object);
		} 

		return JsonHelper::getResponseSucesso($perfis);
	} // Fim do método index

} // Fim da classe
