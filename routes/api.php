<?php
use App\Models\BarbershopModel;

use App\Helpers\MailHelper;

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
  Route::get('/barber/{id}/waiting-to-finish', 'ScheduleController@getWaitingToFinishByBarberId');
});

Route::get('service/barbershop/{id}', 'ServiceController@getByBarbershopId');
Route::prefix('crypt')->group(function() {
  Route::get('barber', 'BarberController@crypt');
  Route::get('user', 'UserController@crypt');
});

Route::post('barber/resend-register-mail', 'BarberController@resendRegisterMail');
Route::post('user/resend-register-mail', 'UserController@resendRegisterMail');
Route::get('barbershop/{id}/barber-ranking', 'BarberController@ranking');
Route::post('notification/send-new-schedule-notification', 'NotificationController@sendNewScheduleNotification');

// Rotas privadas
Route::middleware('auth:api')->group(function () {
  Route::resource('profile', 'ProfileController');
  Route::prefix('barber')->group(function() {
    Route::post('block/{id}', 'BarberController@blockBarber');
    Route::post('unlock/{id}', 'BarberController@unblockBarber');
    Route::post('image', 'BarberController@uploadImage');
    Route::get('by-barbershop', 'BarberController@getByBarbershop');
    Route::post('invitation', 'BarberController@sendInvitation');
    Route::get('check-barbershop-request', 'BarbershopRequestBarberController@checkBarbershopRequest');
    Route::post('barbershop-request', 'BarbershopRequestBarberController@barberRequest');
    Route::put('plan/{id}', 'BarberController@updatePlan');
    Route::delete('plan/{id}', 'BarberController@cancelPlan');
    Route::get('{id}/barbershop/{barbershop_id}/schedules', 'ScheduleController@getByBarberId');
    Route::get('{id}/barbershop/{barbershop_id}/revenues', 'BarberController@getRevenuesByBarber');
    Route::post('save-device-token', 'BarberDeviceTokenController@storeDeviceToken');
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
    Route::get('/{id}/total', 'BarbershopController@total');
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
    Route::get('/total/barber/{barber_id}', 'ScheduleController@getTotalByBarber');
    Route::get('/barbershop/{barbershop_id}', 'ScheduleController@getByBarbershopDate');
    Route::get('/barbershop/{barbershop_id}/pending', 'ScheduleController@getByBarbershopPending');
    Route::put('/{id}/approve', 'ScheduleController@approve');
    Route::put('/{id}/repprove', 'ScheduleController@repprove');
    Route::put('/{id}/finish', 'ScheduleController@finish');
    Route::get('/barbershop/{barbershop_id}/pending/total', 'ScheduleController@getTotalPendingByBarbershop');
  });
  
  Route::prefix('service')->group(function() {
    Route::post('/', 'ServiceController@store');
    Route::put('/{id}', 'ServiceController@update');
    Route::delete('/{id}', 'ServiceController@destroy');
  });

  Route::prefix('user')->group(function() {
    Route::post('/image', 'UserController@uploadImage');
    Route::post('/change-password', 'UserController@changePassword');
    Route::put('/{id}', 'UserController@update');
  });

  Route::put('schedule/{id}/user/cancel', 'ScheduleController@cancelByUser');
});

// Rodas dos Moderadores
Route::post('/moderator/auth/login', 'ModeratorController@login');
Route::middleware('moderator:api')->group(function () {
  Route::prefix('barber')->group(function() {
    Route::get('/', 'BarberController@index');
    Route::put('/{id}/block', 'BarberController@blockBarberByModerator');
    Route::put('/{id}/unblock', 'BarberController@unblockBarberByModerator');
  });
  
  Route::prefix('user')->group(function() {
    Route::get('/', 'UserController@index');
    Route::put('/{id}/block', 'UserController@blockUserByModerator');
    Route::put('/{id}/unblock', 'UserController@unblockUserByModerator');
  });
  
  Route::get('/moderators/barbershop', 'BarbershopController@getAllPaginated');
  Route::put('/barbershop/{id}/block', 'BarbershopController@blockBarbershopByModerator');
  Route::put('/barbershop/{id}/unblock', 'BarbershopController@unblockBarbershopByModerator');
});

Route::get('remove-pending-schedules', 'ScheduleController@removePendingSchedules');

Route::get('/teste-cron', function () {
  \Log::info("Test Cron!");
});