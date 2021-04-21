<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HelpService;

class HelpController extends Controller
{
	private $help_service;

	public function __construct () {
		$this->help_service = new HelpService();
	}

	public function store (Request $request) {
		return $this->help_service->sendMessage($request);
	}

} // Fim da classe
