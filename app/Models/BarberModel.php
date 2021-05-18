<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarberModel extends AbstractModel
{
	use SoftDeletes;

	protected $table 				= "barbers";
	protected $tabela 			= "barbers";
	public const AGUARDANDO = 1;
	public const ATIVO 			= 2;
	public const BLOQUEADO 	= 3;

	public function status () {
		return $this->belongsTo('App\Models\BarbersStatusModel', 'barber_status_id');
	}

	public function schedules () {
		return $this->hasMany('App\Models\ScheduleModel', 'barber_id');
	}

}  // Fim da classe
