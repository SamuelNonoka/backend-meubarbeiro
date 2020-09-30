<?php

namespace App\Helpers;

// Classe responsável pelas funções auxiliares de jsn
class JsonHelper 
{
	// Retorno padrão do json
	public static function getResponseSucesso($data)
	{
		$json = array(
			"status"	=> "sucesso",
			"codigo"	=> 200,
			"corpo"		=> $data
		);

		return response(json_encode($json), 200);
	} // Fim do método getResponseSucesso	

	// Retorna um erro padrão
	public static function getResponseErro($data)
	{
		$json = array(
			"status"	=> "erro",
			"codigo"	=> 400,
			"corpo"		=> $data
		);

		return response(json_encode($json), 200);
	} // Fim do método getResponseErro

	// Retorna um erro de autenticação
	public static function getResponseErroAutenticacao($data)
	{
		$json = array(
			"codigo"	=> 401,
			"status"	=> "sem autenticação",
			"corpo"		=> $data
		);

		return response(json_encode($json), 401);
	} // Fim do método getResponseErroAutenticacao

	// Retorna um erro de permissão
	public static function getResponseErroPermissao($data)
	{
		$json = array(
			"status"	=> "sem permissão",
			"codigo"	=> 403,
			"corpo"		=> $data
		);

		return response(json_encode($json), 403);
	} // Fim do método getResponseErroPermissao

} // Fim da classe