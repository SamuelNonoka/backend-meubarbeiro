<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JsonHelper;
use App\Models\TimeModel;
use App\Services\TimeService;

class TimeController extends Controller
{
	private $time_service;

	public function __construct () {
		$this->time_service = new TimeService();
	}
 
	public function getAvailableByBarbershopId (Request $request, $barbershop_id, $date) 
	{
		$barbers_ids  = $request->barbers ? explode(',', $request->barbers) : [];
		return $this->time_service->getAvailableByBarbershopId($request, $barbershop_id, $date, $barbers_ids);
	} // Fim do método getAvailableByBarbershopId

} // Fim da classe