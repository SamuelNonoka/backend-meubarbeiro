<?php

namespace App\Objects;

// Classe responsável por modelar a instancia de perfil
class PerfilObject 
{
	// Variáveis
	public $id;
	public $nome;
	public $ativo;

	// Construtor da classe
	public function __construct(){
		self::setObjectWithId(0, '', false);
	}	// Fim do construtor

	// Configura o objeto
	public function setObjectWithId($id, $nome, $ativo)
	{
		$this->id 		= $id;
		$this->nome 	= $nome;
		$this->ativo 	= $ativo; 
	} // Fim do método setObjectWithId

	// Seta os dados do banco de dados
	public function setObjectFromDB($perfil_db) 
	{
		$perfil_db = (object) $perfil_db;
		self::setObjectWithId($perfil_db->id, $perfil_db->nome, $perfil_db->ativo);
	} // Fim do método setObjectFromDB

}	// Fim da classe