<?php

namespace App\Services;

use App\Helpers\JsonHelper;
use GuzzleHttp\Client;

class CepService 
{
  public function getCepFromViaCep ($cep) 
	{
		$url 			= "http://viacep.com.br/ws/" . $cep . "/json/";
		$client 	= new Client(['base_uri' => $url]);
		$response	= $client->request('Get', $url);
		$response	= json_decode($response->getBody()->getContents());

		$cep = array (
			'cep'					=> $response->cep ?? null,
			'logradouro'	=> $response->logradouro ?? null,
			'complemento'	=> $response->complemento ?? null,
			'bairro'			=> $response->bairro ?? null,
			'localidade'	=> $response->localidade ?? null,
			'uf'					=> $response->uf ?? null
		);

		return JsonHelper::getResponseSucesso($cep);
	} // Fim do método cep

  
} // Fim da classe