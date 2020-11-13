<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JsonHelper;
use App\Models\TimeModel;

class TimeController extends Controller
{
	// obtem os horários disponíveis da barbearia
	public function getAvailableByBarbershopId (Request $request, $barbershop_id, $date) 
	{
		$barbers_ids = $request->barbers ? explode(',', $request->barbers) : [];
		$data = (new TimeModel)->getAvailableByBarbershopId($barbershop_id, $date, $barbers_ids);
		return JsonHelper::getResponseSucesso($data);
	} // Fim do método getAvailableByBarbershopId

} // Fim da classe