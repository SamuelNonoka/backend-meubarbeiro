<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\ScheduleService;

class NewScheduleJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private $schedule;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct ($schedule) {
		$this->schedule = $schedule;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle (ScheduleService $scheduleService) {
		return $scheduleService->proccessNewScheduleJob($this->schedule);
	}

} // fim da Classe