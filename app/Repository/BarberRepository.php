<?php

namespace App\Repository;

use Illuminate\Database\QueryException as DBException;
use App\Models\BarberModel;
use DB;

class BarberRepository 
{
  public const AGUARDANDO = 1;
	public const ATIVO 			= 2;
  public const BLOQUEADO 	= 3;
  private $barber_model;
  private $tabela = 'barbers';

  public function __construct () {
    $this->barber_model = new BarberModel();
  }

  public function store ($barber) {
    $barber['created_at'] = date('Y-m-d H:i:s');
    $barber['encrypted']  = true;
    
   	try {
      return DB::table($this->tabela)
        ->insertGetId($barber);
		}
		catch (DBException $e) {
			return 0;
		}
  }

  public function getByEmail ($email) {
    return DB::table($this->tabela)
      ->where('email', $email)
      ->get();
	}
}