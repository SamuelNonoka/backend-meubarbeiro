<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterGoogleMail extends Mailable
{
	use Queueable, SerializesModels;
	private $name;
	private $email;
	private $acesso;
    
	public function __construct($name, $email, $is_barber)
	{
		$this->name   		= $name;
		$this->email			= $email;
		$this->acesso			= $is_barber ? env('APP_SITE_URL') . '/autenticacao/login' : env('APP_SITE_URL') . '/autenticacao/barbeiro/login';
	}

	public function build()
	{
		return $this->from(env('MAIL_FROM_ADDRESS'))
						->subject('Meu Barbeiro - Seja Bem-vindo!')
						->view('mails.register-google')
						->with(array(
							"name"	=> $this->name,
							"email"	=> $this->email,
							"acesso" => $this->acesso
						));
	}
}
