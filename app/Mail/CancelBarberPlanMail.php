<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CancelBarberPlanMail extends Mailable
{
	use Queueable, SerializesModels;
  private $barber_name;
  private $plan_name;
  private $plan_value;
  private $date;
	
	public function __construct($barber_name, $plan_name)
	{
		$this->barber_name = $barber_name;
		$this->plan_name	 = $plan_name;
    $this->plan_value  = 'Grátis';
    $this->date        = date('Y-m-d H:i');
	}

	public function build()
	{
		return $this->from('contato@appmeubarbeiro.com.br')
						->subject('Meu Barbeiro - Plano cancelado!')
						->view('mails.cancel-barber-plan')
						->with(array(
							'barber_name' => $this->barber_name,
							'plan_name'		=> $this->plan_name,
							'plan_value'  => $this->plan_value
						));
	}
}
