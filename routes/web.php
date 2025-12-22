<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WildixFormController;
use App\Http\Controllers\YeastarFormController;

Route::view('/', 'home')->name('home');
Route::view('/8cbffd67cd71ad6da41f3e3ce14198affc72cf262de25305007aa80d955996a3', 'easter-egg')->name('easter-egg');

Route::prefix('/pbx/wildix')->group(function () {
Route::get('/general-info', [WildixFormController::class, 'generalInfo'])->name('wildix.general_info');
Route::post('/general-info', [WildixFormController::class, 'postGeneralInfo']);

Route::get('/num-list', [WildixFormController::class, 'numList'])->name('wildix.num_list');
Route::post('/num-list', [WildixFormController::class, 'postNumList']);

Route::get('/extensions', [WildixFormController::class, 'extension'])->name('wildix.extension');
Route::post('/extensions', [WildixFormController::class, 'postExtension']);

Route::get('/devices', [WildixFormController::class, 'devices'])->name('wildix.device');
Route::post('/devices', [WildixFormController::class, 'postDevices']);

// Route::get('/formulaire/dect', [WildixFormController::class, 'dect'])->name('form.dect');
// Route::post('/formulaire/dect', [WildixFormController::class, 'postDect']);

Route::get('/call-groups', [WildixFormController::class, 'callGroup'])->name('wildix.call_group');
Route::post('/call-groups', [WildixFormController::class, 'postCallGroup']);

Route::get('/timetable', [WildixFormController::class, 'timetable'])->name('wildix.timetable');
Route::post('/timetable', [WildixFormController::class, 'postTimetable']);

Route::get('/svi', [WildixFormController::class, 'svi'])->name('wildix.svi');
Route::post('/svi', [WildixFormController::class, 'postSvi']);

Route::get('/dialplan', [WildixFormController::class, 'dialplan'])->name('wildix.dialplan');
Route::post('/dialplan', [WildixFormController::class, 'postDialplan']);

Route::get('/informations-et-remarques', [WildixFormController::class, 'infos'])->name('wildix.infos');
Route::post('/informations-et-remarques', [WildixFormController::class, 'postInfos']);

Route::get('/recap', [WildixFormController::class, 'recap'])->name('wildix.recap');
Route::post('/recap', [WildixFormController::class, 'postRecap']);


Route::get('/export', [WildixFormController::class, 'export'])->name('wildix.export');

Route::post('/reset', function () {
    Session::forget('form_wildix');
    return redirect()->route('wildix.general_info')->with('info', 'Session réinitialisée.');
})->name('wildix.reset');
});




Route::prefix('/pbx/yeastar')->group(function () {
    Route::get('/general-info', [YeastarFormController::class, 'generalInfo'])->name('yeastar.general_info');
    Route::post('/general-info', [YeastarFormController::class, 'postGeneralInfo']);
    
    Route::get('/num-list', [YeastarFormController::class, 'numList'])->name('yeastar.num_list');
    Route::post('/num-list', [YeastarFormController::class, 'postNumList']);
    
    Route::get('/extensions', [YeastarFormController::class, 'extension'])->name('yeastar.extension');
    Route::post('/extensions', [YeastarFormController::class, 'postExtension']);
    
    // Route::get('/devices', [YeastarFormController::class, 'devices'])->name('yeastar.device');
    // Route::post('/devices', [YeastarFormController::class, 'postDevices']);
    
    Route::get('/call-groups', [YeastarFormController::class, 'callGroup'])->name('yeastar.call_group');
    Route::post('/call-groups', [YeastarFormController::class, 'postCallGroup']);
    
    Route::get('/timetable', [YeastarFormController::class, 'timetable'])->name('yeastar.timetable');
    Route::post('/timetable', [YeastarFormController::class, 'postTimetable']);
    
    Route::get('/svi', [YeastarFormController::class, 'svi'])->name('yeastar.svi');
    Route::post('/svi', [YeastarFormController::class, 'postSvi']);
    
    Route::get('/dialplan', [YeastarFormController::class, 'dialplan'])->name('yeastar.dialplan');
    Route::post('/dialplan', [YeastarFormController::class, 'postDialplan']);
    
    Route::get('/informations-et-remarques', [YeastarFormController::class, 'infos'])->name('yeastar.infos');
    Route::post('/informations-et-remarques', [YeastarFormController::class, 'postInfos']);
    
    Route::get('/recap', [YeastarFormController::class, 'recap'])->name('yeastar.recap');
    Route::post('/recap', [YeastarFormController::class, 'postRecap']);
    
    
    Route::get('/export', [YeastarFormController::class, 'export'])->name('yeastar.export');
    
    Route::post('/reset', function () {
        Session::forget('form_yeastar');
        return redirect()->route('yeastar.general_info')->with('info', 'Session réinitialisée.');
    })->name('yeastar.reset');
    });




// Debug
// Route::view('/formulaire/session', 'debug.session')->name('debug.session');
// Route::view('/formulaire/contact/yeastar', 'emails.yeastar')->name('yeastar.emails.contact');
// Route::view('/formulaire/contact/wildix', 'emails.wildix')->name('wildix.emails.contact');
// Route::view('/formulaire/pdf/yeastar', 'pdf.yeastar')->name('debug.pdf');
// Route::view('/formulaire/pdf/wildix', 'pdf.wildix')->name('debug.pdf');