<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JsonHelper;
use App\Helpers\TokenHelper;
use App\Helpers\ValidacaoHelper;
use App\Models\AddressModel;
use App\Models\BarberModel;
use App\Models\BarbershopModel;
use App\Models\BarbershopScheduleDayModel;
use DB;

class BarbershopController extends Controller
{
  // LIsta todas as barbearias
  public function index (Request $request) 
  {
    $name = $request->name ?? null;
    $data = (new BarbershopModel)->getAll($name);
    return JsonHelper::getResponseSucesso($data);
	} // Fim do método index

  public function show ($id) 
  {
    $data = (new BarbershopModel)->getById($id);
    return JsonHelper::getResponseSucesso($data);
  } // Fim do método show

  // Obtém os barbeiros da barbearia
  public function getBarbers ($id) 
  {
    $data = (new BarberModel)->getByBarbershopId($id);
    return JsonHelper::getResponseSucesso($data);
  } // Fim do método getBarbers

  public function store(Request $request)
	{
    // Valida a request
		$rules = [
			'name'			=> 'required|max:50',
      'barber_id' => 'required'
    ];

    $messages = [
      'name.required' => 'O nome da barbearia deve ser informado',
      'name.max'      => 'O nome da barbearia deve ter no máximo 50 caracteres',
      'barber_id'     => 'Informe o barbeiro'
    ];
		
		$invalido = ValidacaoHelper::validar($request->all(), $rules, $messages);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);
    
    // Verifica se o usuário está habilitado
    $barber_model = new BarberModel();
    $barber_db    = $barber_model->getById($request->barber_id);

    if (count($barber_db) == 0)
      return JsonHelper::getResponseErro('O barbeiro informado não existe na plataforma.');
    
    $barber_db = (object) $barber_db[0];
    
    if (!$barber_db->enabled)
      return JsonHelper::getResponseErro('Seu acesso está bloqueado na plataforma.');

    if ($barber_db->barbershop_id)
      return JsonHelper::getResponseErro('Você já possui uma barbearia cadastrada na plataforma.');

    // Cadastra a barbearia
    $barbershop_arr = array (
      'admin_id'  => $barber_db->id,
      'name'      => $request->name
    );

    DB::beginTransaction();
    $id = (new BarbershopModel)->storeObjeto($barbershop_arr);

    if ($id == 0)
      return JsonHelper::getResponseErro('Não foi possível salvar a barbearia.');
    
    // Atualiza o token do usuário
    $barber_arr = array('barbershop_id' => $id);
    $saved      = $barber_model->updateData($barber_db->id, $barber_arr); 

    if (!$saved) 
    {
      DB::rollBack();
      return JsonHelper::getResponseErro('Não foi possível salvar a barbearia.');
    }
    
    DB::commit();
    $barber_db->barbershop_id = $id;
    
    $token   	= TokenHelper::atualizarToken($request, $barber_db);
		$payload	= array("token" => $token);

    return JsonHelper::getResponseSucesso($payload);
  } // Fim do método store

  public function update (Request $request, $id) 
  {
    $barber_arr = $request->only(['name', 'description', 'phone_number', 'instagram_url', 'facebook_url']);
    
    if ($request->address) {
      $address_request = (object) $request->address;
      $address_arr     = [];
    
      if (isset($address_request->cep))
        $address_arr['cep'] = $address_request->cep;
      
      if (isset($address_request->public_place))
        $address_arr['public_place'] = $address_request->public_place;

      if (isset($address_request->number))
        $address_arr['number'] = $address_request->number;

      if (isset($address_request->neighborhood))
        $address_arr['neighborhood'] = $address_request->neighborhood;

      if (isset($address_request->city))
        $address_arr['city'] = $address_request->city;

      if (isset($address_request->uf))
        $address_arr['uf'] = $address_request->uf;

      if (isset($address_request->map_url))
        $address_arr['map_url'] = $address_request->map_url;

      if (isset($address_request->complement))
        $address_arr['complement'] = $address_request->complement;

      $address_model = new AddressModel();

      if ($request->address_id) {
        $address_model->updateData($request->address_id, $address_arr);
      }
      else {
        $address_id = $address_model->storeObjeto($address_arr);
        if ($address_id != 0)
          $barber_arr['address_id'] = $address_id;
      }
    }

    // Schedules days
    if ($request->schedules_days) 
    {
      $schedules                    = $request->schedules_days;
      $barbershop_scheduleday_model = new BarbershopScheduleDayModel();
      $schedules_db                 = $barbershop_scheduleday_model->getByBarbershopId($id);

      // Faz o loop com os horários enviados
      foreach ($schedules as $schedule) 
      {
        $has_schedule   = false;
        $schedule_db_id = null;

        foreach ($schedules_db as $schedule_db) 
        {
          if ($schedule['schedule_day_id'] == $schedule_db->schedule_day_id) {
            $schedule_db_id = $schedule_db->id;
            $has_schedule   = true;
            break;
          }
        } // Fim do loop dos horários do db

        $schedule_arr = array (
          'schedule_day_id' => $schedule['schedule_day_id'],
          'barbershop_id'   => $id,
          'open'            => true,
          'start'           => $schedule['start'],
          'end'             => $schedule['end']
        );

        if ($has_schedule) {
          if ($schedule['open']) {
            $barbershop_scheduleday_model->updateData($schedule_db_id, $schedule_arr);
          } else {
            $barbershop_scheduleday_model->remove($schedule_db_id);
          }
        } else if ($schedule['open']) {
          $barbershop_scheduleday_model->storeObjeto($schedule_arr);
        }
      } // Fim do loop
    
    } // Fim do schedules days

    $barbershop_model = new BarbershopModel();
    $saved            = $barbershop_model->updateData($id, $barber_arr); 

    if (!$saved)
      return JsonHelper::getResponseErro('Não foi possível salvar a barbearia.');
  
    $barbershop_db  = $barbershop_model->getById($id);
    $token   	      = TokenHelper::atualizarToken($request, $barbershop_db);
		$payload	      = array(
      'token'       => $token, 
      'barbershop'  => $barbershop_db
    );

    return JsonHelper::getResponseSucesso($payload);
  } // Atualiza os dados da barbearia
  
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
    $barbershop_id    = $barber->barbershop_id;                
		$image 						= $request->file('img');
		$name							= $barber->uuid . rand(10, 99);
		$name 						=	$name .'.'.$image->getClientOriginalExtension();
		$path							= '/storage/uploads/barbershop/profile/' . $barbershop_id;
    $destinationPath 	= public_path($path);
    $imagePath	 			= $destinationPath. "/".  $name;
		$image->move($destinationPath, $name);
		
		// Atualiza o barbeiro
		$path 				    = $path . '/' . $name;
		$barbershop_arr   = array('image_url' => $path);
		$barbershop_model = new BarbershopModel();
		$barbershop_model->updateArray($barbershop_id, $barbershop_arr);
		return JsonHelper::getResponseSucesso($path);
  } // Fim do método uploadImage
  
  // Faz o upload da imagem de backgound para o barbeiro
	public function uploadBackgroundImage (Request $request) 
	{
		$barber 	= TokenHelper::getUser($request);
		$rules 		= ['img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'];
		$invalido	= ValidacaoHelper::validar($request->all(), $rules);

		if ($invalido) 
			return JsonHelper::getResponseErro($invalido);

		if (!$request->hasFile('img'))
			return JsonHelper::getResponseErro('Por favor, envie uma imagem');

    // Salva a imagem
    $barbershop_id    = $barber->barbershop_id;                
		$image 						= $request->file('img');
		$name							= $barber->uuid . rand(10, 99);
		$name 						=	$name .'.'.$image->getClientOriginalExtension();
		$path							= '/storage/uploads/barbershop/background/' . $barbershop_id;
    $destinationPath 	= public_path($path);
    $imagePath	 			= $destinationPath. "/".  $name;
		$image->move($destinationPath, $name);
		
		// Atualiza o barbeiro
		$path 				    = $path . '/' . $name;
		$barbershop_arr   = array('background_url' => $path);
		$barbershop_model = new BarbershopModel();
		$barbershop_model->updateArray($barbershop_id, $barbershop_arr);
		return JsonHelper::getResponseSucesso($path);
  } // Fim do método uploadImage
 
} // Fim da classe