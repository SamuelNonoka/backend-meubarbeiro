<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PushNotificationService;

class NotificationController extends Controller
{
	private $pushNotificationService;

	public function __construct () {
		$this->pushNotificationService = new PushNotificationService();
	}

  public function sendNewScheduleNotification (Request $request) {
		return $this->pushNotificationService->sendNewScheduleNotificationToBarber(45); 
	} // fim do método sendNewScheduleNotification

} // fim da Classe
