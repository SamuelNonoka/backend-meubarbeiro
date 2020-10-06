<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class HelpMail extends Mailable
{
	use Queueable, SerializesModels;
  private $name;
  private $email;
	private $text;
	
	public function __construct($name, $email, $text)
	{
		$this->name   = $name;
		$this->email	= $email;
		$this->text   = $text;
	}

	public function build()
	{
		return $this->from('contato@appmeubarbeiro.com.br')
						->subject('Meu Barbeiro - Ajuda!')
						->view('mails.help')
						->with(array(
							'name'  => $this->name,
							'email'	=> $this->email,
							'text'  => $this->text
						));
	}
}
