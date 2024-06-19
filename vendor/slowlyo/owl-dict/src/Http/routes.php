<?php

use Slowlyo\OwlDict\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('/admin_dict/dict_type_options', [Controllers\OwlDictController::class, 'dictTypeOptions']);
Route::resource('/admin_dict', Controllers\OwlDictController::class);
