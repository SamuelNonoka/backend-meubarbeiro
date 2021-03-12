<?php

namespace App\Repository;

use Illuminate\Database\QueryException as DBException;
use App\Models\BarbershopModel;
use DB;

class BarbershopRepository extends AbstractRepository
{
  public const AGUARDANDO = 1;
	public const ATIVO 			= 2;
  public const BLOQUEADO 	= 3;

  public function __construct () {
    parent::__construct((new BarbershopModel));
  }

  public function getAllEnabled () 
  {
    $data = $this->model->where('barbershop_status_id', self::ATIVO)->get();

    foreach ($data as $key => $item) {
      $data[$key]['address'] = $item->address;
    }
    
    return $data;
  } // Fim do método getAll

  public function getByName ($name) 
  {
    $data = $this->model->where('name', 'like', '%'.$name.'%')->get();

    foreach ($data as $key => $item) {
      $data[$key]['address'] = $item->address;
    }
    
    return $data;
  } // Fim do método getByName

} // Fim da classe