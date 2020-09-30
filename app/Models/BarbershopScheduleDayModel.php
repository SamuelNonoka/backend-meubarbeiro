<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class BarbershopScheduleDayModel extends AbstractModel
{
	protected $table = "barbershops_schedules_days";
	protected $tabela = "barbershops_schedules_days";
	
	// Obtem o relacionamento pelo id da barbearia
	public function getByBarbershopId ($barbershop_id) 
	{	
		try {
			return DB::table($this->tabela)
				->where('barbershop_id', $barbershop_id)
				->get();
		} catch (DBException $e) {
			return [];
		}
	} // Fim do método getByBarbershopId

} // Fim da classe
