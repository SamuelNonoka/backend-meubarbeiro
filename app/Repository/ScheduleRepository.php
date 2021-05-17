<?php

namespace App\Repository;

use App\Models\ScheduleModel;

class ScheduleRepository extends AbstractRepository
{
  public const AGUARDANDO = 3;
	public const AGENDADO = 1;
	public const CANCELADO = 2;
	public const REPROVADO = 4;

  public function __construct () {
    parent::__construct((new ScheduleModel));
  }

  public function getAmmountByBarber ($barber_id, $barbershop_id) 
  {
    return $this->model
      ->where('barber_id', $barber_id)
      ->where('barbershop_id', $barbershop_id)
      ->where('schedule_status_id', $this::AGENDADO)
      ->sum('price');
  }

  public function getByBarber ($barber_id, $barbershop_id, $start_date, $end_date) 
  {
    $query = $this->model
              ->where('schedules.barber_id', $barber_id)
              ->where('schedules.barbershop_id', $barbershop_id)
              ->where('schedules.schedule_status_id', self::AGENDADO);
    
    if ($start_date != 'null' && $end_date != 'null') {
      $query->whereRaw("DATE(schedules.start_date) >= '$start_date'");
      $query->whereRaw("DATE(schedules.end_date) <= '$end_date'");
    }
              
    $data = $query->orderBy('start_date', 'desc')
              ->paginate(10);
		
    foreach ($data as $key => $schedule)
    {
      $data[$key]['user']     = $schedule->user;
      $data[$key]['barber']   = $schedule->barber;
      $data[$key]['services'] = $schedule->services;
    }

    return $data;
  } // Fim do método getByBarber

  public function getByBarbershopDate ($barbershop_id, $date) 
	{
    return $this->model->where('barbershop_id', $barbershop_id)
            ->whereRaw("date(start_date) = '$date'")
            ->get();
	} // Fim do método getByBarbershopDate

  public function getByBarbershop ($barbershop_id, $date, $barber_id = null) 
	{
    $query = $this->model->where('schedules.barbershop_id', $barbershop_id)
              ->whereRaw("DATE(schedules.start_date) = '$date'");
		
    if ($barber_id)
			$query->whereRaw("schedules.barber_id = '$barber_id'");
		
    $data     = $query->get();
    $barbers  = [];

    foreach ($data as $key => $item) {
      foreach ($item->services as $i => $service) {
        $data[$key]['services'][$i] = $service;
      }
      $data[$key]['user'] = $item->user;
      
      $has_barber = false;
      foreach ($barbers as $key => $barber) {
        if ($barber['id'] == $item->barber['id']) {
          $has_barber = true;
          break;
        }
      }

      if (!$has_barber) {
        $barber             = $item->barber->toArray();
        $barber['schedules'] = [];
        array_push($barbers, $barber);
      }
    }
    
    foreach ($data as $item) {
      foreach ($barbers as $key => $barber) {
        if ($item['barber_id'] == $barber['id']) {
          $item = $item->toArray();
          unset($item['barber']);
          array_push($barbers[$key]['schedules'], $item);  
        }
      }
    }

    return $barbers;
	} // Fim do método getByBarbershop

  public function getByBarbershopPending ($barbershop_id, $barber_id) 
  {
    $date = date('Y-m-d H:m:i');
    $data = $this->model->where('barbershop_id', $barbershop_id)
            ->where('barber_id', $barber_id)
            ->where('schedule_status_id', self::AGUARDANDO)
            ->whereRaw("DATE(schedules.start_date) > '$date'")
            ->whereRaw("DATE(schedules.end_date) > '$date'")
            ->orderBy('start_date', 'desc')
            ->paginate(10);

    foreach ($data as $key => $item) 
    {
      $data[$key]->status               = $item->status;
      $data[$key]->barber               = $item->barber;
      $data[$key]->user                 = $item->user;
      $data[$key]->barbershop           = $item->barbershop;
      $data[$key]->barbershop->address  = $item->barbershop->address;
      $data[$key]->services             = $item->services;
    }

    return $data;
  } // Fim do método getByBarbershopPending

  public function getById ($id) 
  {
		$data = $this->model->find($id);
    if ($data) {
      $data->status               = $data->status;
      $data->barber               = $data->barber;
      $data->user                 = $data->user;
      $data->barbershop           = $data->barbershop;
      $data->barbershop->address  = $data->barbershop->address;
      $data->services             = $data->services;
    }
    return $data;
	} // Fim do método getByUserId

  public function getByUserId ($user_id) {
		return $this->model->where('user_id', $user_id)->get();
	} // Fim do método getByUserId

  public function getFutureAprovedByBarberId ($barber_id) 
	{
    $date = date('Y-m-d H:i:s');
    return $this->model->where('barber_id', $barber_id)
            ->where('schedule_status_id', '=', self::AGENDADO)
            ->whereRaw("date(start_date) >= '$date'")
            ->get();
	} // Obtem os agendamentos aprovados do barbeiro

  public function getTotalByBarber ($barber_id, $start_date, $end_date) 
  {
    $data = $this->model
            ->where('barber_id', $barber_id)
            ->where('schedule_status_id', self::AGENDADO);
    
    if ($start_date) $data->whereRaw("date(start_date) >= '$start_date'");
    if ($end_date) $data->whereRaw("date(end_date) <= '$end_date'");
    
    return $data->count();
  } // Fim do método getTotalByBarber

  public function getTotalDoneByBarbershopId ($barbershop_id) 
	{
		$date = date('y-m-d H:i:s');
    return $this->model->where('barbershop_id', $barbershop_id)
            ->where('schedule_status_id', self::AGENDADO)
            ->whereRaw("date(end_date) <= '$date'")
            ->count();
	} // Fim do método getTotalDoneByBarbershopId

  public function getTotalOfDayByBarbershopId ($barbershop_id) 
	{
		$date = date('y-m-d');
    return $this->model->where('barbershop_id', $barbershop_id)
      ->where('schedule_status_id', self::AGENDADO)
      ->whereRaw("date(end_date) = '$date'")
      ->count();
	} // Fim do método getTotalOfDayByBarbershopId

  public function getTotalWaitingByBarbershopId ($barbershop_id) 
	{
		$date = date('y-m-d H:i:s');
    return $this->model->where('barbershop_id', $barbershop_id)
            ->where('schedule_status_id', self::AGUARDANDO)
            ->whereRaw("date(end_date) > '$date'")
            ->count();
	} // Fim do Método

  public function getTotalByBarbershopId ($barbershop_id) {
    return $this->model
              ->where('barbershop_id', $barbershop_id)
              ->where('schedule_status_id', self::AGENDADO)
              ->count();
  }

  public function getTotalRevenuesByBarbershopId ($barbershop_id) {
    return $this->model
              ->where('barbershop_id', $barbershop_id)
              ->where('schedule_status_id', self::AGENDADO)
              ->sum('price');
  }

} // Fim da classe