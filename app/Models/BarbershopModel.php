<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AddressModel;
use DB;

class BarbershopModel extends AbstractModel
{
	protected $table 				= "barbershops";
	protected $tabela 			= "barbershops";
	public const AGUARDANDO = 1;
	public const ATIVA 			= 2;
	public const BLOQUEADA 	= 3;

	public function address() {
		return $this->belongsTo('App\Models\AddressModel');
	}

} // Fim da classe
