<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

// Both Clients and Doctors are Users, only distinguished by the column 'type'
// We built this way so there's only one table, for authentication purposes

Route::
    namespace('Api')->group(function () {
      Route::post('auth/login', 'AuthController@login');
      Route::post('auth/sanctum/csrf-cookie', 'AuthController@login');
      Route::post('users/register', 'UserController@register');
      Route::post('users/getReset', 'UserController@getReset');
      Route::post('users/reset/password', 'UserController@resetPassword');

      Route::group(['middleware' => 'auth:sanctum'], function () {
        // auth
        Route::post('auth/logout', 'AuthController@logout');
        Route::get('auth/user', 'AuthController@user');


        // users
        Route::apiResource('users', 'UserController');

        // vacations
        Route::apiResource('vacations', 'VacationDaysController');

        // absences
        Route::apiResource('absences', 'AbsenceController');

        // tarefas
        Route::apiResource('tarefas', 'TarefasController');

        // checklists
        Route::apiResource('checklists', 'ChecklistController');

        // Absence type
        Route::apiResource('types', 'TypeController');

        // questionType
        Route::apiResource('questionType', 'QuestionTypeController');

        // EventsToCalendar
        Route::get('absences/get/EventsToCalendar', 'AbsenceController@getEventsToCalendar');

        Route::post('absences/update/Ferias', 'AbsenceController@updateFerias');

        //Type Getter
        Route::get('types/get/Types', 'TypeController@getTypes');

        //Users Getter
        Route::get('users/get/Users', 'UserController@getUsers');

        //Vactions Getter
        Route::get('vacations/get/Vacations', 'VacationDaysController@getVacations');

        //Type Status Update
        Route::post('types/update/Status', 'TypeController@updateStatus');
        Route::apiResource('questionType', 'QuestionTypeController');

        //frota
        Route::apiResource('truckFleet', 'TruckFleetController');


        //tipo de transporte
        Route::apiResource('transportType', 'TransportTypeController');
        Route::get('transportTypes/get/transportTypes', 'TransportTypeController@getTransportTypes');


        //Marca
        Route::apiResource('truckBrand', 'TruckBrandController');
        Route::get('truckBrands/get/truckBrands', 'TruckBrandController@getTruckBrands');

        //Modelo
        Route::apiResource('truckModel', 'TruckModelController');
        Route::get('truckModels/get/truckModels', 'TruckModelController@getTruckModels');

        //Garage
        Route::apiResource('garage', 'GarageController');
        Route::get('garage/get/truckFleets', 'GarageController@getTruckFleets');
        Route::get('garage/get/truckStatus', 'GarageController@getTruckStatus');
        Route::get('garage/get/truckBreakdowns', 'GarageController@getTruckBreakdows');
        Route::post('garage/update/Status', 'GarageController@updateStatus');



      });
    });