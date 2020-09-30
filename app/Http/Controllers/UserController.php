<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\EncriptacaoHelper;
use App\Helpers\JsonHelper;
use App\Helpers\MailHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  // Cria um novo usuário na plataforma.
  public function store (Request $request)
  {
    // Valida a request
		$rules = [
			'name'			=> 'required|max:50',
      'email' 		=> 'required|max:50',
      'password'  => 'required|min:6'
    ];
		
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		$name 		= $request->name;
		$email		= $request->email;
		$password	= EncriptacaoHelper::encriptarSenha($request->password);
		$uuid			= (string) Str::uuid();

		// Verifica se o email é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
      return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");
      
		// Verifica se existe um usuário com aquele e-mail cadastrado
		$user_model = new UserModel();
		$has_email  = $user_model->getByEmail($email);
		
		if (count($has_email) > 0)
			return JsonHelper::getResponseErro('O e-mail informado já está sendo utilizado!');

		$user = array (
			'uuid'			=> $uuid,
			'name'			=> $name,
			'email'			=> $email,
			'password'  => $password	
    );

		$id = $user_model->storeObjeto($user);
		
		if (!$id)
			return JsonHelper::getResponseErro('Não foi possível finalizar o seu cadastro!');
	
		$token   	= TokenHelper::atualizarToken($request, array('uuid' => $uuid));
		$payload	= array("token" => $token);

		// Envia o e-mail de acesso
		MailHelper::sendRegister($name, $email, $request->password, $uuid, $is_barber = false);
			
		return JsonHelper::getResponseSucesso($payload);
	} // Fim do método store

	// Altera a senha
	public function update (Request $request, $id)
	{
		$token 		= $request->header('token');
		$payload 	= explode(".",$token);
		$payload 	= $payload[1];
		$payload	= base64_decode($payload);
		$payload	= json_decode($payload);
		$usuario  = $payload->usuario;

		if ($id != $usuario->id)
			return JsonHelper::getResponseErro('Seu usuário não tem permissão para alterar os dados!');

		$usuario = array(
			'name'					=> $request->name,
			'phone_number'	=> $request->phone_number
		);
		
		$usuario_model 	= new UserModel();
		$saved 					= $usuario_model->updateData($id, $usuario);

		if (!$saved)
			return JsonHelper::getResponseErro('Não foi possível salvar os seus dados!');

		$usuario_db = $usuario_model->getById($id);
		$token 			= TokenHelper::gerarTokenBarber($request, $usuario_db[0]);

		return JsonHelper::getResponseSucesso($token);
	} // Fim do método update

	// Confirme o cadastro do usuário
	public function confirm (Request $request) 
	{
		if (!$request->token)
			return JsonHelper::getResponseErro('Não foi possível confirmar o seu cadastro :(');

		$user_model = new UserModel();
		$user_db		= $user_model->getByUuid($request->token);

		if (count($user_db) == 0)
			return JsonHelper::getResponseErro('Não foi possível confirmar o seu cadastro :(');
		
		$confirm = $user_model->confirmRegister($user_db[0]->id);

		if (!$confirm)
			return JsonHelper::getResponseErro('Não foi possível confirmar o seu cadastro :(');

		return JsonHelper::getResponseSucesso('Cadastro confirmado :)');
	} // Fim do método confirm

	// Altera a senha
	public function changePassword (Request $request) 
	{
		if (!$request->password)
			return JsonHelper::getResponseErro('Por favor, informe a senha!');

		$token 		= $request->header('token');
		$payload 	= explode(".",$token);
		$payload 	= $payload[1];
		$payload	= base64_decode($payload);
		$payload	= json_decode($payload);
		$user 		= array('password' => EncriptacaoHelper::encriptarSenha($request->password));
		$saved 		= (new UserModel)->updateData($payload->usuario->id, $user);

		if (!$saved)
			return JsonHelper::getResponseErro('Não foi possível alterar a senha!');

		return JsonHelper::getResponseSucesso('Senha alterada com sucesso!');
	} // Fim do método changePassword

} // Fim da classe
