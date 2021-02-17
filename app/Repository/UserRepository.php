<?php

namespace App\Repository;

use App\Models\UserModel;

class UserRepository extends AbstractRepository
{
  public function __construct () {
    parent::__construct((new UserModel));
  }

} // Fim da classe