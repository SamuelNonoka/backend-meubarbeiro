<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarberDeviceTokenModel extends AbstractModel
{
	use SoftDeletes;

	protected $table = "barbers_device_tokens";

}  // Fim da classe
