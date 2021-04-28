<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{
	private $user_service;

	public function __construct () {
		$this->user_service = new UserService();
	}

	public function blockUserByModerator (Request $request, $id) {
		return $this->user_service->blockUserByModerator($request, $id);
	} // Fim do método blockUserByModerator

	public function changePassword (Request $request) {
		return $this->user_service->changePassword($request);
	} // Fim do método changePassword

	public function changePasswordByCode (Request $request) {
		return $this->user_service->changePasswordByCode($request);
	} // Fim do método changePassword

	public function index (Request $request) {
		return $this->user_service->getAll($request);
	}

	public function recoveryPassword (Request $request)  {
		return $this->user_service->recoveryPassword($request);
	} // Fim do método

  public function store (Request $request) {
		return $this->user_service->store($request);
	} // Fim do método store

	public function storeWithGoogle (Request $request) {
		return $this->user_service->storeWithGoogle($request);
	} // Fim do método store with google

	public function unblockUserByModerator (Request $request, $id) {
		return $this->user_service->unblockUserByModerator($request, $id);
	} // Fim do método unblockBarberByModerator

	public function update (Request $request, $id) {
		return $this->user_service->update($request, $id);
	} // Fim do método update

	public function uploadImage (Request $request) {
		return $this->user_service->uploadImage($request);
	} // Fim do método uploadImage

	public function confirm (Request $request) {
		return $this->user_service->confirmRegister($request);
	} // Fim do método confirm

} // Fim da classe