<?php
use App\Models\BarbershopModel;

// Rotas públicas
Route::prefix('auth')->group(function () {
  Route::prefix('barber')->group(function() {
    Route::post('login', 'Auth\LoginController@loginBarber');
    Route::post('register', 'BarberController@store');
    Route::post('register/confirm', 'BarberController@confirm');
    Route::post('recovery-password', 'BarberController@recoveryPassword');
    Route::post('change-password', 'BarberController@changePassword');
  });
  Route::prefix('user')->group(function() {
    Route::post('change-password', 'UserController@changePasswordByCode');
    Route::post('login', 'Auth\LoginController@loginUser');
    Route::post('recovery-password', 'UserController@recoveryPassword');
    Route::post('register', 'UserController@store');
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
    Route::post('barbershop-request', 'BarberController@barbershopRequest');
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
