<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ServiceModel extends AbstractModel
{
	protected $tabela = "services";
	
	// Busca os servicos de uma barbearia
	public function getByBarbershopId($barbershop_id) 
	{
		try {
			$data = DB::table($this->tabela)
				->select(
					'services.*'
				)
				->Leftjoin('services_types', 'services.service_type_id', '=', 'services_types.id')
				->where('services.barbershop_id', $barbershop_id)
				->get();
			
			/*if (count($data) > 0) 
			{
				$data = self::formatData($data);
				$data = $data[0];
			}*/

			return $data;
		} 
		catch (DBException $e) {
			return [];
		}
	} // Fim do método getByBarbershopId

} // Fim da classe
