<?php

namespace App\Repository;

use App\Models\UserModel;

class UserRepository extends AbstractRepository
{
  public function __construct () {
    parent::__construct((new UserModel));
  }

  public function getByUuid ($uuid) {
    return $this->model->where('uuid', $uuid)->first();
  } // Fim do método getById

} // Fim da classe