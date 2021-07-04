<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BarberDeviceTokenService;

class BarberDeviceTokenController extends Controller
{
	private $barberDeviceTokenService;

	public function __construct () {
		$this->barberDeviceTokenService = new BarberDeviceTokenService();
	} // fim do Construtor

  public function storeDeviceToken (Request $request) 
	{
		$request = $request->only(['barber_id', 'device_token']);
		return $this->barberDeviceTokenService->storeDeviceToken($request);
	} // fim do método storeDeviceToken

} // Fim da classe
