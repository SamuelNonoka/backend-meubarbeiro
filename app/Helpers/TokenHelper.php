<?php

namespace App\Helpers;

use Illuminate\Http\Request;

// Classe responsável por mapear os token da aplicação
class TokenHelper 
{
	// Gerar um novo token para a aplicação
	public static function gerarTokenBarber (Request $request, $object = null)
	{
		//if ($_SERVER['REMOTE_ADDR'] != env('APP_DOMAIN_ACESSO'))
			//return null;

		return (new TokenHelper)->setToken($request, $object);
	} // Fim do método gerar token

	// Atualiza o token do usuário
	public static function atualizarToken ($request, $usuario_obj) {
		return (new TokenHelper)->setToken($request, $usuario_obj);
	} // Atualiza o token do usuário

	// Configura o token
	public static function setToken ($request, $usuario = [], $expiration = null) 
	{
		if (!$expiration)
			$expiration = date('Y-m-d H:i', strtotime('+1 hour'));

		$header	= array(
			'alg'	=> 'HS256',
			'typ'	=> 'JWT'
		);

		$header = json_encode($header);
		$header = base64_encode($header);

		// Payload
		$data_expiracao	= $expiration;
		$payload 				= array(
			'iss' => 'localhost',
			"usuario"					=> $usuario,
			"data_expiracao"	=> $data_expiracao
		);

		$payload = json_encode($payload);
		$payload = base64_encode($payload);

		$header_payload = "$header.$payload"; 
		$assinatura			= hash_hmac('sha256', $header_payload, env('APP_KEY'), true);
		$assinatura 		= base64_encode($assinatura);
		$token 					= "$header_payload.$assinatura";

		return $token;
	} // configura o token

	// Get user
	public static function getUser (Request $request) 
	{
		$token	= $request->header('token');
		$part 	= explode(".",$token);

		if(count($part) != 3)
			return false;

		$payload = $part[1];
		$payload = json_decode(base64_decode($payload));
		
		return $payload->usuario;
	} // Fim do método getUser

	// Verifica se o token é válido
	public static function eValido ($token) 
	{
		$part = explode(".",$token);

		if(count($part) != 3)
			return false;

		$header 				= $part[0];
		$payload 				= $part[1];
		$signature 			= $part[2];
		$header_payload = "$header.$payload";
		$valid 					= hash_hmac('sha256', $header_payload, env('APP_KEY'), true);
		$valid 					= base64_encode($valid);
		$signature 			= stripslashes($signature);
		$valid 					= stripslashes($valid);

		//dd($signature, $valid);
		return ($signature == $valid) ? true : false;
	} // Fim do método eValido

	// Valida a data do token 
	public static function dataExpirada($token) 
	{
		$part = explode(".",$token);
		
		if(count($part) != 3)
			return true;

		$payload 	= $part[1];
		$payload 	= base64_decode($payload);
		$payload 	= json_decode($payload);

		if(!isset($payload->data_expiracao))
			return true;
		
		$data_expiracao	= $payload->data_expiracao;
		$data_atual			= date('Y-m-d H:i');

		if($data_expiracao <= $data_atual)
			return true;

		return false;
	}

} // Fim da classe