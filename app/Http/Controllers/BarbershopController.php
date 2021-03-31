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
use App\Services\BarberService;
use App\Services\BarbershopService;
use DB;

class BarbershopController extends Controller
{
  private $barbershop_service;
  private $barber_service;

  public function __construct () {
    $this->barber_service = new BarberService();
    $this->barbershop_service = new BarbershopService();
  } // fim do construtor

  public function getBarbers ($id) {
    return $this->barber_service->getByBarbershopId($id);
  } // Fim do método getBarbers

  public function index (Request $request) 
  {
    if ($request->name)
      return $this->barbershop_service->getByName($request->name);
    else return $this->barbershop_service->getAllEnabled();
	} // Fim do método index

  public function store(Request $request) {
    return $this->barbershop_service->store($request);
  } // Fim do método store

  public function update (Request $request, $id) {
    return $this->barbershop_service->update($request, $id);
  } // Atualiza os dados da barbearia

  /** Controlles antigos **/
  public function show ($id) 
  {
    $data = (new BarbershopModel)->getById($id);
    return JsonHelper::getResponseSucesso($data);
  } // Fim do método show

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