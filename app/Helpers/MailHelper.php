<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use App\Mail\BarberInvitationMail;
use App\Mail\ChangeBarberPlanMail;
use App\Mail\CancelBarberPlanMail;
use App\Mail\HelpMail;
use App\Mail\RecoveryPasswordMail;
use App\Mail\RegisterMail;
use App\Mail\RegisterGoogleMail;

// Classe responsável pelos envios de email
class MailHelper
{
  // Envia e-mail de ajuda
  public static function sendBarberInvitation($barber_mail, $barbershop_name, $token)
  {
    $barber_invitation_mail = new BarberInvitationMail($barbershop_name, $token);
    return self::send($to = $barber_mail, $mail = $barber_invitation_mail); // Dispara o e-mail
  } // Fim do método sendHelpBarber

  // Envia e-mail de ajuda
  public static function sendHelpBarber($name, $email, $text)
  {
    $helpMail = new HelpMail($name, $email, $text);
    return self::send($to = null, $mail = $helpMail); // Dispara o e-mail
  } // Fim do método sendHelpBarber

  // Envia e-mail de recuperação de senha
  public static function sendRecoveryPassword($email, $name, $code, $uuid, $is_barber = false)
  {
    $recoveryPasswordMail = new RecoveryPasswordMail($name, $code, $uuid, $is_barber);
    return self::send($to = $email, $mail = $recoveryPasswordMail); // Dispara o e-mail
  } // Fim do método sendRecoveryPassword

  public static function sendRegister($name, $email, $password, $uuid, $is_barber = false)
  {
    $registerMail = new RegisterMail($name, $email, $password, $uuid, $is_barber);
    return self::send($email, $registerMail); // Dispara o e-mail
  } // Fim do método sendRegister

  public static function sendRegisterWithGoogle($name, $email)
  {
    $registerMail = new RegisterGoogleMail($name, $email);
    return self::send($email, $registerMail); // Dispara o e-mail
  } // Fim do método sendRegister

  // Envia e-mail quando o barbeiro altera o plano
  public static function sendChangeBarberPlan($barber_mail, $barber_name, $plan_name)
  {
    $changeBarberPlanMail = new ChangeBarberPlanMail($barber_name, $plan_name);
    return self::send($to = $barber_mail, $mail = $changeBarberPlanMail); // Dispara o e-mail
  } // Fim do método sendRegister

  // Envia e-mail quando o barbeiro cancelar o plano
  public static function sendCancelBarberPlan($barber_mail, $barber_name, $plan_name)
  {
    $cancelBarberPlanMail = new CancelBarberPlanMail($barber_name, $plan_name);
    return self::send($to = $barber_mail, $mail = $cancelBarberPlanMail); // Dispara o e-mail
  } // Fim do método sendCancelBarberPlan

  // Envia o email
  private static function send($to, $mail)
  {
    if (!$to)
      $to = 'nonokapereira@gmail.com';

    try {
      Mail::to($to)->send($mail);
      return true;
    }
    catch(Exception $e) {
      return false;
    }
  } // Fim do método send

} // Fim da classe MailHelper
