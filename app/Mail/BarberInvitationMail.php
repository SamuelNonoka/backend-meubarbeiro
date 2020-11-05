<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BarberInvitationMail extends Mailable
{
	use Queueable, SerializesModels;
	private $barbershop_name;
	private $link;

	public function __construct($barbershop_name, $token)
	{
		$this->barbershop_name	= $barbershop_name;
		$this->link							= env('APP_SITE_URL') . '/autenticacao/barbeiro/cadastro?token=' . $token;
	}

	public function build()
	{
		return $this->from('contato@appmeubarbeiro.com.br')
						->subject('Meu Barbeiro - Convite!')
						->view('mails.barber-invitation')
						->with(array(
							'barbershop_name'	=> $this->barbershop_name,
							'link'						=> $this->link
						));
	}
}
