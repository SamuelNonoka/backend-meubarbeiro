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
    
	public function __construct($name, $email)
	{
		$this->name   		= $name;
		$this->email			= $email;
	}

	public function build()
	{
		return $this->from('contato@appmeubarbeiro.com.br')
						->subject('Meu Barbeiro - Seja Bem-vindo!')
						->view('mails.register-google')
						->with(array(
							"name"	=> $this->name,
							"email"	=> $this->email
						));
	}
}
