<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecoveryPasswordMail extends Mailable
{
  use Queueable, SerializesModels;
  private $name;
	private $code;
	private $link;
	
	public function __construct($name, $code, $uuid, $is_barber)
	{
		$this->name = $name;
		$this->code	= $code;
		$this->link	= env('APP_SITE_URL');
		$this->link .= ($is_barber) ? "/autenticacao/barbeiro/alterar-senha/{$uuid}" : "/autenticacao/alterar-senha/{$uuid}";
	}

	public function build()
	{
		return $this->from(env('MAIL_FROM_ADDRESS'))
						->subject('Meu Barbeiro - Alterar Senha')
						->view('mails.recovery-password')
						->with(array(
							"name"  => $this->name,
							"code"	=> $this->code,
							"link"	=> $this->link  
						));
	}
}
