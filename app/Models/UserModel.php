<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

// Classe responsavel pela entidade usuario
class UserModel extends AbstractModel
{
	use SoftDeletes;
	protected $table = 'users';
  protected $tabela = "users";

	// Altera a senha
	public function updatePassword ($id, $password) {
		return self::updateData($id, array('password' => $password));
	} // Fim do método updateRange

} // Fim da classe