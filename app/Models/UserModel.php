<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

use App\Models; 

// Classe responsavel pela entidade usuario
class UserModel extends AbstractModel
{
  protected $tabela = "users";

  // Busca todos os usuarios
	public function get() 
	{
		try {
			return DB::table($this->tabela)->get();
		} 
		catch (Exception $e) {
			return null;
		}

	} // Fim do método get

	// Busca todos barbeiros pelo e-mail
	public function getByEmail ($email) {
		return DB::table($this->tabela)->where('email', $email)->get();
	} // Fim do método getByEmail

	// Busca todos os barbeiros pelo uuid
	public function getByUuid ($uuid) {
		return DB::table($this->tabela)->where('uuid', $uuid)->get();
	} // Fim do método getByUuid

	// Confirma o registro
	public function confirmRegister($id) 
	{
		try {
			DB::table($this->tabela)
				->where('id', $id)
				->update(array('enabled' => true));
			return true;
		} catch (Exception $e) {
			return false;
		}
	} // Fim do método confirmRegister

} // Fim da classe