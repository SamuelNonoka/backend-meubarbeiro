<?php

namespace App\Repository;

use App\Models\AbstractModel;

class AbstractRepository 
{
  protected $model;
  protected $tabela;

  public function __construct(AbstractModel $model) {
    $this->model = $model;
  } // Fim do Constructor

  public function getById ($id) {
    return $this->model->find($id);
  } // Fim do método getById

  public function getNotEncrypted () {
    return $this->model->where('encrypted', false)->first();
	} // Fim do método getNotCrypted

  public function update ($data, $id) {
    $barber['updated_at'] = date('Y-m-d H:i:s');
    $this->model->where('id', $id)->update($data);
  } // Fim do método update

} // Fim da classe