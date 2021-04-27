<?php

namespace App\Repository;

use App\Models\UserModel;

class UserRepository extends AbstractRepository
{
  public function __construct () {
    parent::__construct((new UserModel));
  }

  public function confirmRegister($id) 
	{
		$this->model->where('id', $id)
				->update(array('enabled' => true));
	} // Fim do método confirmRegister

  public function getAll ($search, $status, $order) {
    $query = $this->model;
    if ($search) {
      $query = $query->where("name", "like", "%" . $search . "%")
        ->orWhere('email', 'like', "{$search}%");
    }
    if ($status) {
      $query = $query->where('enabled', $status);
    }
    if ($order) {
      $query = $query->orderBy($order);
    }
    return $this->model->paginate(10);
  } // Fim do método getByEmail

  public function getByUuid ($uuid) {
    return $this->model->where('uuid', $uuid)->first();
  } // Fim do método getById

} // Fim da classe