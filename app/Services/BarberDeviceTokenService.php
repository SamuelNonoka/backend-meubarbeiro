<?php

namespace App\Services;

use App\Helpers\JsonHelper;
use App\Helpers\ValidacaoHelper;
use App\Repository\BarberDeviceTokenRepository;

class BarberDeviceTokenService 
{
  protected $barberDeviceTokenRepository;

  public function __construct () {
    $this->barberDeviceTokenRepository = new BarberDeviceTokenRepository();
  }

  public function storeDeviceToken ($request) 
  {
    $rules = [
			'barber_id'     => 'required',
      'device_token'  => 'required'
    ];

    $invalido = ValidacaoHelper::validar($request, $rules);
    
    if ($invalido) 
      return JsonHelper::getResponseErro($invalido);

    $barberDeviceTokens = $this->barberDeviceTokenRepository->getByBarberIdAndDeviceToken(
      $request['barber_id'],
      $request['device_token']
    );

    if (count($barberDeviceTokens) > 0)
      return JsonHelper::getResponseSucesso('Token Armazenado!');  
    
    $this->barberDeviceTokenRepository->store($request);
    return JsonHelper::getResponseSucesso('Token Armazenado!');
  } // fim do método storeDeviceToken

} // Fim da classe