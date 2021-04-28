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
  } // Fim do Construtor

  public function getById ($id) 
  {
    $data           = $this->model->find($id);
    $data->address  = $data->address;
    return $data;
  } // Fim da classe getById

  public function getAllPaginated($search, $status, $order) 
  {
    $query = $this->model;
    if ($search)
      $query = $query->where("name", "like", "%" . $search . "%");
    
    if ($status)
      $query = $query->where('barbershop_status_id', $status);

    if ($order)
      $query = $query->orderBy($order);

    $data = $query->paginate(10);
    
    foreach ($data as $key => $item) {
      $data[$key]['address']  = $item->address;
      $data[$key]['status']   = $item->status;
    }
    return $data;
  } // Fim do método getAllPaginated

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