<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ModeratorService;

class ModeratorController extends Controller
{
	private $moderator_service;

	public function __construct () {
		$this->moderator_service = new ModeratorService();
	}

	public function login (Request $request) {
		return $this->moderator_service->login($request);
	}
}
