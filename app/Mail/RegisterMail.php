<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterMail extends Mailable
{
	use Queueable, SerializesModels;
	private $name;
	private $email;
	private $password;
	private $is_barber;
	private $uuid;
	private $confirm_link;
	private $remove_link;
	
	public function __construct($name, $email, $password, $uuid, $is_barber = false)
	{
		$this->name   		= $name;
		$this->email			= $email;
		$this->password 	= $password;
		$this->uuid				= $uuid;
		$this->is_barber	= $is_barber;
		$confirm					= $is_barber ? 'barbeiro/confirmar-cadastro' : 'confirmar-cadastro';
		$remove						= $is_barber ? 'barbeiro/remover-cadastro' : 'remover-cadastro';

		$this->confirm_link	= env('APP_SITE_URL') . '/autenticacao/' . $confirm . '/' . $uuid;
		$this->remove_link	= env('APP_SITE_URL') . '/autenticacao/' . $remove . '/' . $uuid;
	}

	public function build()
	{
		return $this->from(env('MAIL_FROM_ADDRESS'))
						->subject('Meu Barbeiro - Seja Bem-vindo!')
						->view('mails.register')
						->with(array(
							"name"  				=> $this->name,
							"password"			=> $this->password,
							"email"					=> $this->email,
							"confirm_link"	=> $this->confirm_link,  
							"remove_link"		=> $this->remove_link,
							"is_barber"			=> $this->is_barber
						));
	}
}
