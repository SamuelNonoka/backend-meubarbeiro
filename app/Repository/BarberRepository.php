<?php

namespace App\Repository;

use Illuminate\Database\QueryException as DBException;
use App\Models\BarberModel;
use DB;

class BarberRepository extends AbstractRepository
{
  public const AGUARDANDO = 1;
	public const ATIVO 			= 2;
  public const BLOQUEADO 	= 3;
  protected $tabela       = 'barbers';
  
  public function __construct () {
    parent::__construct((new BarberModel));
  }

  public function getByEmail ($email) {
    return $this->model->where('email', $email)->get();
  } // Fim do método getByEmail

  public function getByBarbershopId ($barbershop_id) {
    $data = $this->model->where('barbershop_id', $barbershop_id)->get();

    foreach ($data as $key => $item) {
      $data[$key]['status'] = $item->status;
    }

    return $data;
  } // Fim do método getByEmail

  public function getById ($id) {
    return $this->model->find($id);
  } // Fim do método getById

  public function getByUuid ($uuid) {
    return $this->model->where('uuid', $uuid)->get();
  } // Fim do método getByEmail

  public function getTotalBarbersByBarbershopId ($barbershop_id) 
  {
    return $this->model->where('barbershop_id', $barbershop_id)
            ->where('barber_status_id', self::ATIVO)
            ->count();
		
  } // Fim do método getTotalBarbersByBarbershopId
} 