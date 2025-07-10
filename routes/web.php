<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MultiStepFormController;

Route::view('/', 'home')->name('home');

Route::get('/formulaire/ipbx-info', [MultiStepFormController::class, 'pbxInfo'])->name('form.pbx-info');
Route::post('/formulaire/ipbx-info', [MultiStepFormController::class, 'postPbxInfo']);

Route::get('/formulaire/num-list', [MultiStepFormController::class, 'numList'])->name('form.num-list');
Route::post('/formulaire/num-list', [MultiStepFormController::class, 'postNumList']);

Route::get('/formulaire/extensions', [MultiStepFormController::class, 'extension'])->name('form.extension');
Route::post('/formulaire/extensions', [MultiStepFormController::class, 'postExtension']);

Route::get('/formulaire/call-groups', [MultiStepFormController::class, 'callGroup'])->name('form.call-group');
Route::post('/formulaire/call-groups', [MultiStepFormController::class, 'postCallGroup']);

Route::get('/formulaire/timetable', [MultiStepFormController::class, 'timetable'])->name('form.timetable');
Route::post('/formulaire/timetable', [MultiStepFormController::class, 'postTimetable']);

Route::get('/formulaire/dialplan', [MultiStepFormController::class, 'dialplan'])->name('form.dialplan');
Route::post('/formulaire/dialplan', [MultiStepFormController::class, 'postDialplan']);

Route::get('/formulaire/svi', [MultiStepFormController::class, 'svi'])->name('form.svi');
Route::post('/formulaire/svi', [MultiStepFormController::class, 'postSvi']);

Route::get('/formulaire/informations-et-remarques', [MultiStepFormController::class, 'infos'])->name('form.infos');
Route::post('/formulaire/informations-et-remarques', [MultiStepFormController::class, 'postInfos']);

Route::get('/formulaire/recap', [MultiStepFormController::class, 'recap'])->name('form.recap');
Route::post('/formulaire/recap', [MultiStepFormController::class, 'postRecap']);

Route::post('/formulaire/reset', function () {
    session()->flush();
    return redirect()->route('form.pbx-info')->with('success', 'Session réinitialisée.');
})->name('form.reset');

// Debug
Route::view('/session', 'debug.session')->name('debug.session');
