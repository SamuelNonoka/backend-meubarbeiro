<?php

namespace App\Services;

use App\Helpers\JsonHelper;
use App\Repository\BarberDeviceTokenRepository;
use App\Repository\PushNotificationRepository;
use App\Repository\ScheduleRepository;

class PushNotificationService 
{
  private $barberRepository;
  private $pushNotificationRepository;
  private $scheduleRepository;

  public function __construct () {
    $this->barberDeviceTokenRepository  = new BarberDeviceTokenRepository();
    $this->pushNotificationRepository   = new PushNotificationRepository();
    $this->scheduleRepository           = new ScheduleRepository();
  } // fim do construtor

  public function sendNewScheduleNotificationToBarber ($scheduleId) 
  {
    $schedule = $this->scheduleRepository->getById($scheduleId);

    if (!$schedule)
      return JsonHelper::getResponseErro('Agendamento não encontrado!');

    $barberDeviceTokens = $this->barberDeviceTokenRepository->getDeviceTokensByBarber($schedule->barber->id);

    if (count($barberDeviceTokens) === 0)
      return JsonHelper::getResponseErro('Não possui token!');

    $devicesToken = [];
    foreach ($barberDeviceTokens as $item) {
      array_push($devicesToken, $item->device_token);
    }

    $notificationBody = array (
      'registration_ids'  => $devicesToken,
      'notification'      => array (
        'title' => 'Nova solicitação de agendamento!',
        'body'  => array(
          'description' => 'Corte simples',
          'type'        => 'newSchedule',
          'link'        => env('APP_SITE_URL') . '/admin/dashboard/solicitacoes'
        )
      )
    );

    $this->pushNotificationRepository->sendNotification($notificationBody);
    return JsonHelper::getResponseSucesso('Notificação enviada com sucesso!');
  } // fim do método sendNewScheduleNotificationToBarber

} // fim da classe