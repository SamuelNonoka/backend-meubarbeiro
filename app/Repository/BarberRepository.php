<?php

namespace App\Repository;

use Illuminate\Database\QueryException as DBException;
use App\Models\BarberModel;
use App\Repository\ScheduleRepository;
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

  public function confirmRegister($id) 
	{
    $this->model->where('id', $id)
      ->update(array('enabled' => true));
	} // Fim do método confirmRegister

  public function getAll ($search, $status, $order) {
    $query = $this->model;
    if ($search) {
      $query = $query->where("name", "like", "%" . $search . "%")
        ->orWhere('email', 'like', "{$search}%");
    }
    if ($status) {
      $query = $query->where('enabled', $status);
    }
    if ($order) {
      $query = $query->orderBy($order);
    }
    $data = $query->paginate(10);
    foreach ($data as $key => $item) {
      $data[$key]['status'] = $item->status;
    }
    return $data;
  } // Fim do método getByEmail

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

  public function getByBarbershopIdAndBarbersIds ($barbershop_id, $barbers_ids) {
    $query = $this->model->where('barbershop_id', $barbershop_id);

    if (count($barbers_ids) > 0)
      $query->whereIn('id', $barbers_ids);
             
    $data = $query->get();

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

  public function getTotalByBarbershopId ($barbershop_id) {
    return $this->model
            ->where('barbershop_id', $barbershop_id)
            ->where('barber_status_id', self::ATIVO)
            ->count();
  }

  public function ranking ($barbershop_id) 
  {
    $data = $this->model
              ->where('barbers.barbershop_id', $barbershop_id)
              ->get();
    
    $barbers = [];

    foreach ($data as $item) 
    {
      $item['qtd_schedules']  = $item->schedules
                                ->where('schedule_status_id', ScheduleRepository::AGENDADO)
                                ->where('barbershop_id', $barbershop_id)
                                ->count();
      $item['revenues']       = $item->schedules
                                ->where('schedule_status_id', ScheduleRepository::AGENDADO)
                                ->where('barbershop_id', $barbershop_id)
                                ->sum('price');
      array_push($barbers, $item);
    }

    return $barbers;
  }
} 