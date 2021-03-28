<?php

namespace App\Services;

use Illuminate\Http\Request;

use App\Helpers\JsonHelper;
use App\Repository\BarbershopScheduleDayRepository;
use App\Repository\ScheduleRepository;
use App\Repository\BarberRepository;
use App\Services\BarberService;

class TimeService 
{
  const TIMES = ['00:00:00', '00:15:00', '00:30:00', '00:45:00', '01:00:00', '01:15:00', '01:30:00', '01:45:00', '02:00:00', '02:15:00', '02:30:00','02:45:00', '03:00:00', '03:15:00', '03:30:00', '03:45:00', '04:00:00', '04:15:00', '04:30:00', '04:45:00', '05:00:00', '05:15:00', '05:30:00', '05:45:00', '06:00:00', '06:15:00', '06:30:00', '06:45:00', '07:00:00', '07:15:00', '07:30:00', '07:45:00', '08:00:00', '08:15:00', '08:30:00', '08:45:00', '09:00:00', '09:15:00', '09:30:00', '09:45:00', '10:00:00', '10:15:00', '10:30:00', '10:45:00', '11:00:00', '11:15:00', '11:30:00', '11:45:00', '12:00:00', '12:15:00', '12:30:00', '12:45:00', '13:00:00', '13:15:00', '13:30:00', '13:45:00', '14:00:00', '14:15:00', '14:30:00', '14:45:00', '15:00:00', '15:15:00', '15:30:00', '15:45:00', '16:00:00', '16:15:00', '16:30:00', '16:45:00', '17:00:00', '17:15:00', '17:30:00', '17:45:00', '18:00:00', '18:15:00', '18:30:00', '18:45:00', '19:00:00', '19:15:00', '19:30:00', '19:45:00', '20:00:00', '20:15:00', '20:30:00', '20:45:00', '21:00:00', '21:15:00', '21:30:00', '21:45:00', '22:00:00', '22:15:00', '22:30:00', '22:45:00', '23:00:00', '23:15:00', '23:30:00', '23:45:00'];

  private $barber_repository;
  private $barbershop_scheduleday_repository;
  private $barber_service;
  private $schedule_repository;

  public function __construct () {
    $this->barber_repository                  = new BarberRepository();
    $this->barbershop_scheduleday_repository  = new BarbershopScheduleDayRepository();
    $this->schedule_repository                = new ScheduleRepository();
    $this->barber_service                     = new BarberService();
  }

  // Recupera os horários disponíves da barbearia para aquele dia
  public function getAvailableByBarbershopId ($request, $barbershop_id, $date) 
  {
    $barbers_ids  = $request->barbers ? explode(',', $request->barbers) : [];
    $times        = self::TIMES;
    $date_now     = date('Y-m-d');

		if (strtotime($date_now) == strtotime($date)) {
			$time_now = date('H:i:s');
		
			foreach ($times as $key => $time) {
				if (strtotime($time) <= strtotime($time_now))
					unset($times[$key]);
			}
		}
		
		// REcupera os horários da barbearia
		$start_time = null;
		$end_time		= null;

    $barbershop_schedules_day = $this->barbershop_scheduleday_repository->getByBarbershopId($barbershop_id);
		
		// Recupera o horário inicial e final da empresa
		foreach($barbershop_schedules_day as $schedule_day)
		{
			$day_of_week = date('w', strtotime($date));
			if ($schedule_day->schedule_day_id == $day_of_week) 
			{
				$start_time = $schedule_day->start;
				$end_time		= $schedule_day->end;
				break;
			}
		}
		
		// Verifica os horários do dia
		foreach ($times as $key => $time) 
		{
			if (strtotime($time) < strtotime($start_time) || strtotime($time) > strtotime($end_time))
				unset($times[$key]);
		}

    // Remover os horários já agendados
    $barbers		= $this->barber_repository->getByBarbershopId($barbershop_id, $barbers_ids);
    foreach ($barbers as $key => $barber_db) {
      $barbers[$key] = $this->barber_service->decrypt($barber_db);
      unset($barbers[$key]['password']);
    }
    $schedules  = $this->schedule_repository->getByBarbershopDate($barbershop_id, $date);
    
    foreach ($barbers as $key => $value) {
			$barber_times = [];
			foreach ($times as $time) {
				array_push($barber_times, array('time' => $time));
			}	
			$barbers[$key]['times'] = $barber_times;
		}
		
		foreach ($schedules as $schedule) 
		{
			foreach ($times as $key => $time) 
			{
				$schedule_start_time	= explode(' ', $schedule->start_date)[1];
				$schedule_end_time		= explode(' ', $schedule->end_date)[1];
				
				if (strtotime($time) >= strtotime($schedule_start_time) && strtotime($time) <= strtotime($schedule_end_time)) {
					foreach ($barbers as $key => $barber) {
						if ($barber['id'] === $schedule->barber_id) {
							foreach ($barber['times'] as $key_time => $time) {
								if (strtotime($time['time']) >= strtotime($schedule_start_time) && strtotime($time['time']) <= strtotime($schedule_end_time)) {
									unset($barbers[$key]['times'][$key_time]);
									break;
								}
							}
							break;
						}
					}
				}
			}
		}

		foreach ($barbers as $key => $barber) {
			$barbers[$key]['times'] = array_values($barber['times']);
		}

    //array_values($barbers)
		return JsonHelper::getResponseSucesso($barbers);
  } // Fim do método getAvailableByBarbershopId

} // Fim da classe