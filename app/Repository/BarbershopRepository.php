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

  public function getByName ($name) {
    return $this->model->where('name', 'like', '%'.$name.'%')->get();
  } // Fim do método getByName

} // Fim da classe