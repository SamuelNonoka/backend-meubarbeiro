<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

// Classe responsavel pela entidade Perfil
class PerfilModel extends Model
{
	private $tabela = "perfis";

	public const ADMINISTRADOR = array(
		"id"		=> 1,
		"nome"	=> "administrador"
	);

	public const PROPRIETARIO	= array(
		"id"		=> 2,
		"nome"	=> "proprietario"
	);
        
  public const GERENTE  		= array(
  	"id"		=> 3,
		"nome"	=> "gerente"
  );
        
  public const BARBEIRO     = array(
  	"id"		=> 4,
		"nome"	=> "barbeiro"
  );
        
  public const USUARIO      = array(
  	"id"		=> 5,
		"nome"	=> "usuario"
  );

	// Busca todos os eventos
	public function get() {
		return DB::table($this->tabela)->get();
	} // Fim do método get

} // Fim da classe
