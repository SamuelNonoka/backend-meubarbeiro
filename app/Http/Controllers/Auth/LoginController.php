<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EncriptacaoHelper;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Models\BarberModel;
use App\Models\UserModel;
use App\Services\LoginService;

class LoginController extends Controller
{
	private $login_service;

	public function __construct () {
		$this->login_service = new LoginService();
	}

	// Tenta fazer login na aplicacao
	public function loginBarber (Request $request) {
		return $this->login_service->loginBarber($request);
	} // Fim do método logar

	public function loginUser (Request $request) {
		return $this->login_service->loginUser($request);
	} // Fim do método logar

	public function loginUserWithGoogle (Request $request) {
		return $this->login_service->loginUserWithGoogle($request);
	} // Fim do método logar com o google

	public function loginBarberWithGoogle (Request $request) {
		return $this->login_service->loginBarberWithGoogle($request);
	} // Fim do método logar com o google

} // Fim da classe
