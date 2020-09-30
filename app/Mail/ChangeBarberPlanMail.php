<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangeBarberPlanMail extends Mailable
{
	use Queueable, SerializesModels;
  private $barber_name;
  private $plan_name;
	private $plan_value;
	
	public function __construct($barber_name, $plan_name)
	{
		$this->barber_name = $barber_name;
		$this->plan_name	 = $plan_name;
		$this->plan_value  = 'Grátis';
	}

	public function build()
	{
		return $this->from('nonokapereira@gmail.com')
						->subject('Meu Barbeiro - Plano aderido!')
						->view('mails.change-barber-plan')
						->with(array(
							'barber_name' => $this->barber_name,
							'plan_name'		=> $this->plan_name,
							'plan_value'  => $this->plan_value
						));
	}
}
