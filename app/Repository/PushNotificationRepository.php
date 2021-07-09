<?php 

namespace App\Repository;

class PushNotificationRepository 
{
  public function sendNotification ($notificationBody) 
  {
    $SERVER_API_KEY = 'AAAAECZ5x9M:APA91bH5vyezm9k2kv_z9c51wQoUZH7xUaYcTzFHevjYavdg8AHHxL587FWaY1ct6xm3_QXGWj7CUg3Azj0RqEiAqVsMlpmBs5XokAkbA4TFMHS98OC6gUvJJ0Ns49wxWWD0waL0mlBC';    
    $dataString     = json_encode($notificationBody);
    $headers        = [
      'Authorization: key=' . $SERVER_API_KEY,
      'Content-Type: application/json',
    ];
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
    $response = curl_exec($ch);
    return;
  } // Fim do método sendNotification

} // Fim da classe