<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\EncriptacaoHelper;
use App\Helpers\JsonHelper;
use App\Helpers\MailHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Models\UserModel;
use App\Services\UserService;

class UserController extends Controller
{
	private $user_service;

	public function __construct () {
		$this->user_service = new UserService();
	}

  public function store (Request $request) {
		return $this->user_service->store($request);
	} // Fim do método store

	public function storeWithGoogle (Request $request) {
		return $this->user_service->storeWithGoogle($request);
	} // Fim do método store with google

	public function update (Request $request, $id) {
		return $this->user_service->update($request, $id);
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

	// Envia e-mail de recuperar senha para um barbeiro
	public function recoveryPassword (Request $request) 
	{
		// Valida a request
		$rules = [ 'email' => 'required|max:50' ];
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		// Verifica se o email é válido
    if (!filter_var($request->email, FILTER_VALIDATE_EMAIL))
			return JsonHelper::getResponseErro("Por favor, informe um e-mail válido.");

		// Verifica se existe um barbeiro com aquele e-mail cadastrado
		$user_model = new UserModel();

		// Verifica se já existe algum barbeiro cadastro com aquele e-mail
		$user_db = $user_model->getByEmail($request->email);
		
		if (count($user_db) == 0)
			return JsonHelper::getResponseErro('Esse mail não está cadastrado na plataforma!');

		$user_db	= $user_db[0];
		$code 		= mt_rand(1000, 9999); // Código gerado para verificar o cadastro		
		$user_model->updateCode ($user_db->id, $code);

		$sended = MailHelper::sendRecoveryPassword($user_db->email, $user_db->name, $code, $user_db->uuid);

		if (!$sended)
			return JsonHelper::getResponseErro('Não foi possível enviar o e-mail!');

		return JsonHelper::getResponseSucesso($user_db->uuid);
	} // Fim do método

	// Alterar a senha do barbeiro
	public function changePasswordByCode (Request $request) 
	{
		// Valida a request
		$rules = [ 
			'token'			=> 'required',
			'code' 			=> 'required|size:4',
			'password'	=> 'required|min:6' 
		];

		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		// Verifica se o barbeiro existe
		$user_model = new UserModel();
		$user_db 		= $user_model->getByUuid ($request->token);
		
		if (count($user_db) == 0)
			return JsonHelper::getResponseErro('Não foi possível recuperar o token!');

		$user_db = $user_db[0];
		
		if ($user_db->code != $request->code)
			return JsonHelper::getResponseErro('O código não está correto!');

		// Alterar senha
		$password	= EncriptacaoHelper::encriptarSenha($request->password);
		$saved 		= $user_model->updatePassword($user_db->id, $password);

		if (!$saved)
			return JsonHelper::getResponseErro('Não foi possível alterar sua senha!');

		return JsonHelper::getResponseSucesso('Senha alterda com sucesso!');
	} // Fim do método changePassword

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