<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

use App\Models; 

// Classe responsavel pela entidade usuario
class UserModel extends AbstractModel
{
	protected $table = 'users';
  protected $tabela = "users";

	// Altera a senha
	public function updatePassword ($id, $password) {
		return self::updateData($id, array('password' => $password));
	} // Fim do método updateRange

} // Fim da classe