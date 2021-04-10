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

	public function changePassword (Request $request) {
		return $this->user_service->changePassword($request);
	} // Fim do método changePassword

	public function changePasswordByCode (Request $request) {
		return $this->user_service->changePasswordByCode($request);
	} // Fim do método changePassword

	public function recoveryPassword (Request $request)  {
		return $this->user_service->recoveryPassword($request);
	} // Fim do método

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

} // Fim da classe