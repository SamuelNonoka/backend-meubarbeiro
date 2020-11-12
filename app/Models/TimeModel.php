<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BarbershopScheduleDayModel;
use DB;

// Fim da classe
class TimeModel extends Model
{
	const TIMES = ['00:00:00', '00:15:00', '00:30:00', '00:45:00', '01:00:00', '01:15:00', '01:30:00', '01:45:00', '02:00:00', '02:15:00', '02:30:00','02:45:00', '03:00:00', '03:15:00', '03:30:00', '03:45:00', '04:00:00', '04:15:00', '04:30:00', '04:45:00', '05:00:00', '05:15:00', '05:30:00', '05:45:00', '06:00:00', '06:15:00', '06:30:00', '06:45:00', '07:00:00', '07:15:00', '07:30:00', '07:45:00', '08:00:00', '08:15:00', '08:30:00', '08:45:00', '09:00:00', '09:15:00', '09:30:00', '09:45:00', '10:00:00', '10:15:00', '10:30:00', '10:45:00', '11:00:00', '11:15:00', '11:30:00', '11:45:00', '12:00:00', '12:15:00', '12:30:00', '12:45:00', '13:00:00', '13:15:00', '13:30:00', '13:45:00', '14:00:00', '14:15:00', '14:30:00', '14:45:00', '15:00:00', '15:15:00', '15:30:00', '15:45:00', '16:00:00', '16:15:00', '16:30:00', '16:45:00', '17:00:00', '17:15:00', '17:30:00', '17:45:00', '18:00:00', '18:15:00', '18:30:00', '18:45:00', '19:00:00', '19:15:00', '19:30:00', '19:45:00', '20:00:00', '20:15:00', '20:30:00', '20:45:00', '21:00:00', '21:15:00', '21:30:00', '21:45:00', '22:00:00', '22:15:00', '22:30:00', '22:45:00', '23:00:00', '23:15:00', '23:30:00', '23:45:00'];

	// Recupera os horários disponíves da barbearia para aquele dia
	public function getAvailableByBarbershopId ($barbershop_id, $date) 
	{
		// Pega todos os horários
		$times = TimeModel::TIMES;

		// Verifica o horario atual
		$date_now = date('Y-m-d');

		if (strtotime($date_now) == strtotime($date)) {
			$time_now = date('H:i:s');
		
			foreach ($times as $key => $time) 
			{
				if (strtotime($time) <= strtotime($time_now))
					unset($times[$key]);
			}

			if (count($times) == 0)
				return [];
		}
		
		// REcupera os horários da barbearia
		$start_time = null;
		$end_time		= null;
		
		$barbershop_schedules_day = (new BarbershopScheduleDayModel)->getByBarbershopId($barbershop_id);
		
		// Recupera o horário inicial e final da empresa
		foreach($barbershop_schedules_day as $schedule_day)
		{
			$day_of_week = date('w', strtotime($date));
			if ($schedule_day->schedule_day_id == $day_of_week) 
			{
				if (!$schedule_day->open)
					return [];

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

		if (count($times) == 0)
			return [];

		// Remover os horários já agendados
		$barbers		= (new BarberModel)->getByBarbershopId($barbershop_id);
		$schedules 	= (new ScheduleModel)->getByBarbershopDate($barbershop_id, $date);
		
		foreach ($barbers as $key => $value) {
			$barbers[$key]['times'] = $times;
		}
		
		$barbers    = collect($barbers);
		$barbers		= $barbers->map(function ($barber, $key) {
			return [
				'id'				=> $barber['id'],
				'name'			=> $barber['name'],
				'image_url'	=> $barber['image_url'],
				'times'			=> $barber['times']
			];
		});
		
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
								if (strtotime($time) >= strtotime($schedule_start_time) && strtotime($time) <= strtotime($schedule_end_time)) {
									unset($barber['times'][$key_time]);
									$barbers[$key] = $barber;
									break;
								}
							}
							break;
						}
					}
				}
			}
		}

		return $barbers;
	} // Fim do método getAvailableByBarbershopId

} // Fim da classe
