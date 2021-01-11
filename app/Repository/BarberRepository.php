<?php

namespace App\Repository;

use Illuminate\Database\QueryException as DBException;
use App\Models\BarberModel;
use DB;

class BarberRepository extends AbstractRepository
{
  public const AGUARDANDO = 1;
	public const ATIVO 			= 2;
  public const BLOQUEADO 	= 3;
  protected $tabela       = 'barbers';
  
  public function __construct () {
    parent::__construct((new BarberModel));
  }

  public function getByEmail ($email) {
    return DB::table($this->tabela)
      ->where('email', $email)
      ->get();
  } // Fim do método getByEmail
} 