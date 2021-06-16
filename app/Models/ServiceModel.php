<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceModel extends AbstractModel {
	use SoftDeletes;
	protected $table = "services";
	protected $primaryKey  = "id";

	public function schedules () {
		return $this->belongsToMany('App\Models\ServicesModels', 'schedules_services', 'service_id', 'schedule_id');
	}
} // Fim da classe
