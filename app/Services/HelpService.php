<?php

namespace App\Services;

use App\Helpers\JsonHelper;
use App\Helpers\MailHelper;
use App\Helpers\ValidacaoHelper;

class HelpService 
{
  public function sendMessage ($request) 
  {
    $rules = [
			'text'  => 'required',
			'max'		=> '200'
		];

		$messages = [
			'required'	=> 'Por favor, informe a sua dúvida',
			'max'				=> 'Sua mensagem deve ter no máximo 50 caracteres.'
		];

		$invalido = ValidacaoHelper::validar($request->all(), $rules, $messages);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		$token 		= $request->header('token');
		$payload 	= explode(".",$token);
		$payload 	= $payload[1];
		$payload	= base64_decode($payload);
		$payload	= json_decode($payload);
		$barber		= $payload->usuario;

		MailHelper::sendHelpBarber($barber->name, $barber->email, $request->text);	
		return JsonHelper::getResponseSucesso('Mensagem encaminhada com sucesso!');
  } // Fim do método sendMessage

} // Fim da classe