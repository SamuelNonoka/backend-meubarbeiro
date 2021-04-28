<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AddressModel;
use App\Models\BarbershopStatusModel;
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

	public function status () {
		return $this->belongsTo('App\Models\BarbershopStatusModel', 'barbershop_status_id');
	}

} // Fim da classe
