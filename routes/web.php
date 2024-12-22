<?php

use App\Livewire\Presenter\PresenterList;
use App\Models\Presenter;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
  dd(Presenter::all());
});

Route::get('/presenters', PresenterList::class);
