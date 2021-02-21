<?php
use App\Models\BarbershopModel;

use App\Helpers\MailHelper;
/*Route::get('send-mail', function () {
  MailHelper::sendRegisterWithGoogle('SAMUEL', 'nonokapereira@gmail.com');	
});*/

Route::get('teste-email', function () {
  //MailHelper::sendHelpBarber('Samuel Pereira', 'nonokapereira@gmail.com', 'descriao da duvida');
  /*return view('mails.register')->with(array(
    "name"      => 'name',
    "email"	    => 'nonokapereira@gmail.com',
    'password'  => '123',
    'uuid'      => '123',
    'is_barber' => false,
    'confirm_link' => 'kdsk',
    'remove_link' => 'dsjbfdj',
    'acesso' => 'sasa'
  ));*/
  //dd('email-teste');
  //MailHelper::sendRegister('SAMUEL', 'nonokapereira@gmail.com', '123123', '123123', true);
  //MailHelper::sendRegister('SAMUEL', 'samuel.pereira95@yahoo.com.br', '123123', '123123', true);	
  //MailHelper::sendRegister('SAMUEL', 'nonokapereira@gmail.com', '123123', '123123', true);
  //dd('enviou');
});

// Rotas públicas
Route::prefix('auth')->group(function () {
  Route::prefix('barber')->group(function() {
    Route::post('login', 'Auth\LoginController@loginBarber');
    Route::post('login-google', 'Auth\LoginController@loginBarberWithGoogle');
    Route::post('register', 'BarberController@store');
    Route::post('register-google', 'BarberController@storeWithGoogle');
    Route::post('register/confirm', 'BarberController@confirm');
    Route::post('recovery-password', 'BarberController@recoveryPassword');
    Route::post('change-password', 'BarberController@changePassword');
  });
  Route::prefix('user')->group(function() {
    Route::post('change-password', 'UserController@changePasswordByCode');
    Route::post('login', 'Auth\LoginController@loginUser');
    Route::post('login-google', 'Auth\LoginController@loginUserWithGoogle');
    Route::post('recovery-password', 'UserController@recoveryPassword');
    Route::post('register', 'UserController@store');
    Route::post('register-google', 'UserController@storeWithGoogle');
    Route::post('register/confirm', 'UserController@confirm');
  });
});
Route::get('/autenticar', 'Auth\AutenticacaoController@autenticar');
Route::prefix('barbershop')->group(function() {
  Route::get('/', 'BarbershopController@index');
  Route::get('/{id}', 'BarbershopController@show');
  Route::get('/{id}/barber', 'BarbershopController@getBarbers');
  Route::get('/{id}/time/available/{date}', 'TimeController@getAvailableByBarbershopId');
});

Route::prefix('schedule')->group(function() {
  Route::get('/{id}', 'ScheduleController@show');
  Route::post('/', 'ScheduleController@store');
  Route::get('/user/{id}', 'ScheduleController@getByUserId');
});

Route::get('service/barbershop/{id}', 'ServiceController@getByBarbershopId');
Route::prefix('crypt')->group(function() {
  Route::get('barber', 'BarberController@crypt');
  Route::get('user', 'UserController@crypt');
});

// Rotas privadas
Route::middleware('auth:api')->group(function () {
  Route::resource('profile', 'ProfileController');
  Route::prefix('barber')->group(function() {
    Route::post('block/{id}', 'BarberController@blockBarber');
    Route::post('unlock/{id}', 'BarberController@unlockBarber');
    Route::post('image', 'BarberController@uploadImage');
    Route::get('by-barbershop', 'BarberController@getByBarbershop');
    Route::post('invitation', 'BarberController@sendInvitation');
    Route::get('check-barbershop-request', 'BarberController@checkBarbershopRequest');
    Route::post('barbershop-request', 'BarbershopController@barberRequest');
    Route::put('plan/{id}', 'BarberController@updatePlan');
    Route::delete('plan/{id}', 'BarberController@cancelPlan');
  });
  Route::resource('barber', 'BarberController');

  Route::prefix('barbershop')->group(function() {
    Route::post('/', 'BarbershopController@store');
    Route::put('/{id}', 'BarbershopController@update');
    Route::post('/image', 'BarbershopController@uploadImage');
    Route::post('/background', 'BarbershopController@uploadBackgroundImage');
    Route::get('/{id}/total-barbers', 'BarberController@getTotalBarbersByBarbershopId');
    Route::get('/{id}/total-schedules-done', 'ScheduleController@getTotalDoneByBarbershopId');
    Route::get('/{id}/total-schedules-waiting', 'ScheduleController@getTotalWaitingByBarbershopId');
    Route::get('/{id}/total-schedules-of-day', 'ScheduleController@getTotalOfDayByBarbershopId');
  });

  Route::prefix('barbershop-request')->group(function() {
    Route::delete('/{id}/cancel', 'BarbershopRequestBarberController@cancelByBarber');
    Route::get('/barbershop/{id}', 'BarbershopRequestBarberController@barbershopRequestsByBarbershop');
    Route::post('/{id}/approve', 'BarbershopRequestBarberController@approve');
    Route::delete('/{id}/reprove', 'BarbershopRequestBarberController@reprove');
  });

  Route::get('cep/{cep}', 'CepController@getCepFromViaCep');
  Route::resource('help', 'helpController');

  Route::prefix('schedule')->group(function() {
    Route::get('/barbershop/{barbershop_id}', 'ScheduleController@getByBarbershopDate');
    Route::get('/barbershop/{barbershop_id}/pending', 'ScheduleController@getByBarbershopPending');
    Route::put('/{id}/approve', 'ScheduleController@approve');
    Route::put('/{id}/repprove', 'ScheduleController@repprove');
  });
  
  Route::prefix('service')->group(function() {
    Route::post('/', 'ServiceController@store');
    Route::put('/{id}', 'ServiceController@update');
    Route::delete('/{id}', 'ServiceController@destroy');
  });

  Route::prefix('user')->group(function() {
    Route::post('/change-password', 'UserController@changePassword');
    Route::put('/{id}', 'UserController@update');
  });

  Route::put('schedule/{id}/user/cancel', 'ScheduleController@cancelByUser');
});
