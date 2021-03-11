<?php

namespace App\Repository;

use Illuminate\Database\QueryException as DBException;
use App\Models\BarbershopModel;
use DB;

class BarbershopRepository extends AbstractRepository
{
  public function __construct () {
    parent::__construct((new BarbershopModel));
  }

  public function getAll () 
  {
    $data = $this->model->get();

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