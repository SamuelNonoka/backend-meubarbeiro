<?php

namespace App\Repository;

use Illuminate\Database\QueryException as DBException;
use App\Models\AddressModel;

class AddressRepository extends AbstractRepository 
{
  public function __construct () {
    parent::__construct((new AddressModel));
  }
} // Fim da classe AddressRepository