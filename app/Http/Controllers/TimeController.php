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
		$data = (new TimeModel)->getAvailableByBarbershopId($barbershop_id, $date);
		return JsonHelper::getResponseSucesso($data);
	} // Fim do método getAvailableByBarbershopId

} // Fim da classe