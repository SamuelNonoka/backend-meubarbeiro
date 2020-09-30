<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JsonHelper;
use App\Models\TimeModel;

class TimeController extends Controller
{
	// obtem os horários disponíveis da barbearia
	public function getAvailableByBarbershopId ($barbershop_id, $date) 
	{
		$times = (new TimeModel)->getAvailableByBarbershopId($barbershop_id, $date);
		return JsonHelper::getResponseSucesso(array_values($times));
	} // Fim do método getAvailableByBarbershopId

} // Fim da classe