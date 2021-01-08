<?php

namespace App\Repository;

use App\Models\AbstractModel;

class AbstractRepository 
{
  protected $model;
  protected $tabela;

  public function __construct(AbstractModel $model) {
    $this->model = $model;
  }

  public function update ($data, $id) {
    $this->model->where('id', $id)->update($data);
  } // Fim do método update

  public function getById ($id) {
    return $this->model->find($id);
  } // Fim do método update

} // Fim da classe