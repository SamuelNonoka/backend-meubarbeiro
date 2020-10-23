<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\JsonHelper;
use App\Helpers\MailHelper;
use App\Helpers\TokenHelper;
use App\Helpers\EncriptacaoHelper;
use App\Helpers\ValidacaoHelper;
use App\Models\BarberModel;

class BarberController extends Controller
{
	// Cadastra um barbeiro
	public function store (Request $request) 
	{
		// Valida a request
		$rules = [
			'name'			=>	'required|max:50',
      'email' 		=> 'required|max:50',
      'password'	=> 'required|min:6'
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

		// Verifica se existe um barbeiro com aquele e-mail cadastrado
		$barber_model = new BarberModel();

		// Verifica se já existe algum barbeiro cadastro com aquele e-mail
		$has_email = $barber_model->getByEmail($email);
		
		if (count($has_email) > 0)
			return JsonHelper::getResponseErro('O e-mail informado já está sendo utilizado!');

		$barber = array (
			'uuid'				=> $uuid,
			'name'				=> $name,
			'email'				=> $email,
			'password'		=> $password,
			'created_at'	=> date('Y-m-d')	
		);

		$id = $barber_model->storeObjeto($barber);
		
		if (!$id)
			return JsonHelper::getResponseErro('Não foi possível finalizar o seu cadastro!');
	
		$token   	= TokenHelper::atualizarToken($request, array('uuid' => $uuid));
		$payload	= array("token" => $token);

		// Envia o e-mail de acesso
		MailHelper::sendRegister($name, $email, $request->password, $uuid, $is_barber = true);
			
		return JsonHelper::getResponseSucesso($payload);
	} // Fim do método store

	// Atualiza os dados do usuário
	public function update (Request $request, $id) 
	{
		$rules = [
			'name' 	=> 'required|max:50',
			'phone'	=> 'required|max:11|min:8'
		];

		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		$barber 			= $request->all();
		$barber_model = new BarberModel();
		$updated			= $barber_model->updateData($id, $barber);
		$barber_db		= $barber_model->getById($id);

		if (!$updated)
			return JsonHelper::getResponseErro('Não foi possível alterar os dados!');

		$token   	= TokenHelper::atualizarToken($request, $barber_db[0]);
		$payload	= array("token" => $token);
	
		return JsonHelper::getResponseSucesso($payload);
	} // Fim do método update

	// Atualiza o plano do barbeiro
	public function updatePlan (Request $request, $id) 
	{
		// Valida a request
		$rules 		= ['plan_id' => 'required'];
		$invalido = ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		// Verifica se existe um barbeiro com aquele e-mail cadastrado
		$barber_model = new BarberModel();
		$barber_db 		= $barber_model->getById($id);

		if (count($barber_db) == 0)
			return JsonHelper::getResponseErro('Usuário informado não existe na aplicação!');

		$barber_db = $barber_db[0];
		
		if ($barber_db->plan_id == $request->plan_id)
			return JsonHelper::getResponseErro('Você já possui este plano!');

		$plan_due_date 	= date('Y-m-d');
		$plan_due_date 	= strtotime("+1 months", strtotime($plan_due_date));

		$update = array (
			'plan_id'				=> $request->plan_id,
			'plan_due_date'	=> $plan_due_date
		);

		$saved = $barber_model->updateArray($id, $update);

		if (!$saved)
			return JsonHelper::getResponseErro('Não foi possível alterar o seu plano!');
		
		$barber_db->plan_due_date	= $plan_due_date;
		$barber_db->plan_id				= $request->plan_id;

		$token 		= TokenHelper::atualizarToken($request, $barber_db);
		$payload	= array("token" => $token);

		// Envia o e-mail de troca de plano
		MailHelper::sendChangeBarberPlan($barber_db->email, $barber_db->name, 'Free');
			
		return JsonHelper::getResponseSucesso($payload);
	} // Fim do método updatePlan

	// Faz o upload de uma nova imagem para o barbeiro
	public function uploadImage (Request $request) 
	{
		$barber 	= TokenHelper::getUser($request);
		$rules 		= ['img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'];
		$invalido	= ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		if (!$request->hasFile('img'))
			return JsonHelper::getResponseErro('Por favor, envie uma imagem');

		// Salva a imagem 
		$image 						= $request->file('img');
		$name							= $barber->uuid . rand(10, 99);
		$name 						=	$name .'.'.$image->getClientOriginalExtension();
		$path							= '/storage/uploads/barbers/profile/' . $barber->uuid;
    $destinationPath 	= public_path($path);
    $imagePath	 			= $destinationPath. "/".  $name;
		$image->move($destinationPath, $name);
		
		// Atualiza o barbeiro
		$path 				= $path . '/' . $name;
		$barber_arr 	= array('image_url' => $path);
		$barber_model	= new BarberModel();
		$barber_model->updateArray($barber->id, $barber_arr);
		
		$barber_db 	= $barber_model->getById($barber->id);
		$token			= TokenHelper::gerarTokenBarber ($request, $barber_db[0]);
		return JsonHelper::getResponseSucesso($token);
	} // Fim do método uploadImage

	// Remove o plano
	public function cancelPlan(Request $request, $id) 
	{
		// Verifica se existe um barbeiro com aquele e-mail cadastrado
		$barber_model = new BarberModel();
		$barber_db 		= $barber_model->getById($id);

		if (count($barber_db) == 0)
			return JsonHelper::getResponseErro('Usuário informado não existe na aplicação!');

		$barber_db = $barber_db[0];	
		
		if (!$barber_db->plan_id)
			return JsonHelper::getResponseErro('Seu plano já foi cancelado!');
		
		$update =	array (
			'plan_id'				=> null,
			'plan_due_date'	=> null
		);

		$saved = $barber_model->updateArray($id, $update);

		if (!$saved)
			return JsonHelper::getResponseErro('Não foi possível cancelar o seu plano!');
		
		$barber_db->plan_due_date	= null;
		$barber_db->plan_id				= null;

		$token 		= TokenHelper::atualizarToken($request, $barber_db);
		$payload	= array("token" => $token);

		// Envia o e-mail de troca de plano
		MailHelper::sendCancelBarberPlan($barber_db->email, $barber_db->name, 'Free');
			
		return JsonHelper::getResponseSucesso($payload);
	} // Fim do método destroyPlan

	// Confirme o cadastro do barbeiro
	public function confirm (Request $request) 
	{
		if (!$request->token)
			return JsonHelper::getResponseErro('Não foi possível confirmar o seu cadastro :( !');

		$barber_model = new BarberModel();
		$barber_db		= $barber_model->getByUuid($request->token);

		if (count($barber_db) == 0)
			return JsonHelper::getResponseErro('Não foi possível confirmar o seu cadastro :( !');
		
		$confirm = $barber_model->confirmRegister($barber_db[0]->id);

		if (!$confirm)
		return JsonHelper::getResponseErro('Não foi possível confirmar o seu cadastro :( !');

		return JsonHelper::getResponseSucesso('Cadastro confirmado :) !');
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
		$barber_model = new BarberModel();

		// Verifica se já existe algum barbeiro cadastro com aquele e-mail
		$barber_db = $barber_model->getByEmail($request->email);
		
		if (count($barber_db) == 0)
			return JsonHelper::getResponseErro('Esse mail não está cadastrado na plataforma!');

		$barber_db	= $barber_db[0];
		$code 			= mt_rand(1000, 9999); // Código gerado para verificar o cadastro		
		$barber_model->updateCode ($barber_db->id, $code);

		$sended = MailHelper::sendRecoveryPassword($barber_db->email, $barber_db->name, $code, $barber_db->uuid, true);

		if (!$sended)
			return JsonHelper::getResponseErro('Não foi possível enviar o e-mail!');

		return JsonHelper::getResponseSucesso($barber_db->uuid);
	} // Fim do método

	// Alterar a senha do barbeiro
	public function changePassword (Request $request) 
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
		$barber_model = new BarberModel();
		$barber_db 		= $barber_model->getByUuid ($request->token);
		
		if (count($barber_db) == 0)
			return JsonHelper::getResponseErro('Não foi possível recuperar o token!');

		$barber_db = $barber_db[0];
		
		if ($barber_db->code != $request->code)
			return JsonHelper::getResponseErro('O código não está correto!');

		// Alterar senha
		$password	= EncriptacaoHelper::encriptarSenha($request->password);
		$saved 		= $barber_model->updatePassword($barber_db->id, $password);

		if (!$saved)
			return JsonHelper::getResponseErro('Não foi possível alterar sua senha!');

		return JsonHelper::getResponseSucesso('Senha alterda com sucesso!');
	} // Fim do método changePassword

	public function getTotalBarbersByBarbershopId ($barbershop_id) 
	{
		$data = (new BarberModel)->getTotalBarbersByBarbershopId($barbershop_id);
		return JsonHelper::getResponseSucesso($data);
	}

} // Fim da classe
