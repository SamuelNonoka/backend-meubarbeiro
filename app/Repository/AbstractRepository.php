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

  public function remove ($id) {
		$this->model->where('id', $id)->delete();
	} // Fim do método remove

  public function store ($object) 
  {
    try {
      $data = date('Y-m-d H:i:s');
      $object['created_at'] = "'$data'";
   	  return $this->model->insertGetId($object);
    } catch (\Illuminate\Database\QueryException $e) {
      \Log::error('Não foi possível salvar o registro');
      \Log::error($e->getMessage());
      return null;
    }
    
  } // Fim do método store

  public function getByEmail ($email) {
    return $this->model->where('email', $email)->get();
  } // Fim do método getByEmail

  public function getById ($id) {
    return $this->model->find($id);
  } // Fim do método getById

  public function getNotEncrypted () {
    return $this->model->where('encrypted', false)->first();
	} // Fim do método getNotCrypted

  public function update ($data, $id) 
  {
    $data['updated_at'] = date('Y-m-d H:i:s');
    $this->model->where('id', $id)->update($data);
  } // Fim do método update

} // Fim da classe