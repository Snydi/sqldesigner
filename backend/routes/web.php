<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/blog', fn() => view('blog.index'));
Route::get('/blog/mysql-workbench-alternative', fn() => view('blog.mysql-workbench-alternative'));
Route::get('/blog/how-to-design-mysql-database-schema', fn() => view('blog.how-to-design-mysql-database-schema'));
Route::get('/blog/er-diagram-tool-online', fn() => view('blog.er-diagram-tool-online'));
Route::get('/blog/mysql-foreign-key', fn() => view('blog.mysql-foreign-key'));
Route::get('/blog/mysql-data-types', fn() => view('blog.mysql-data-types'));
Route::get('/blog/database-normalization', fn() => view('blog.database-normalization'));
Route::get('/blog/how-to-draw-er-diagram', fn() => view('blog.how-to-draw-er-diagram'));
Route::get('/blog/mysql-vs-postgresql', fn() => view('blog.mysql-vs-postgresql'));

Route::get('/{any}', function () {
    return view('layouts.app');
})->where('any', '.*');







