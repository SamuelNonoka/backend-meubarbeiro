<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class BarberModel extends AbstractModel
{
	protected $tabela = "barbers";
	
	// Busca todos os eventos
	public function getByEmailAndPassword($email, $password)
	{
		return DB::table($this->tabela)
						->where('email', $email)
						->where('password', $password)
						->get();
	} // Fim do método getByEmailAndPassword

	// Busca todos os barbeiros pelo uuid
	public function getByUuid ($uuid) 
	{
		return DB::table($this->tabela)->where('uuid', $uuid)->get();
	} // Fim do método getByUuid

	// Busca todos barbeiros pelo e-mail
	public function getByEmail ($email) {
		return DB::table($this->tabela)->where('email', $email)->get();
	} // Fim do método getByEmail

	// Obtem os barbeiros pelo id da barbearia
	public function getByBarbershopId ($barbershop_id) {
		return DB::table($this->tabela)->where('barbershop_id', $barbershop_id)->get();
	} // Fim do método getByBarbershopId

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

	// Altera a senha
	public function updatePassword ($id, $password) {
		return self::updateData($id, array('password' => $password));
	} // Fim do método updateRange

}  // Fim da classe
