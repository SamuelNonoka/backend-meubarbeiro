<?php

namespace App\Models;

use Illuminate\Database\QueryException as DBException;
use Illuminate\Database\Eloquent\Model;
use DB;

// Classe padrão de database
class AbstractModel extends Model
{
	protected $tabela = "";

	// Busca todos os barbeiros pelo Id
	public function getById ($id) 
	{
		try {
			return DB::table($this->tabela)->where('id', $id)->get();
		} catch (DBException $e) {
			return [];
		}
	} // Fim do método getById

	// Salva Um objeto no banco de dados
	public function storeObjeto ($objeto) 
	{
		try {
			$objeto['created_at'] = date('Y-m-d H:i:s');
			return DB::table($this->tabela)->insertGetId($objeto);
		}
		catch (DBException $e) {
			return 0;
		}

	} // Fim da classe

	// Faz update padrão na aplicacao
	public function updateData ($id, $data) 
	{
		try {
			DB::table($this->tabela)
				->where('id', $id)
				->update($data);

			return true;
		} catch (Exception $e) {
			return false;
		}
	} // Fim do método updateRange

	// Alterar dados
	public function updateArray ($id, $data) {
		return self::updateData($id, $data);
	} // Fim do método updateArray

	// Altera o codigo do barbeiro
	public function updateCode ($id, $code) {
		return self::updateData($id, array('code' => $code));
	} // Fim do método updateRange

	// Remove um registro do banco de dados
	public function remove ($id) 
	{
		try {
			DB::table($this->tabela)
				->where('id', $id)
				->delete();

			return true;
		} catch (Exception $e) {
			return false;
		}
	} // Fim do método remove

} // Fim da classe
