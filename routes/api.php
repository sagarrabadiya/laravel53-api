<?php

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

$methods = ['index', 'store', 'update', 'destroy', 'show'];
$methodsWithoutUpdate = ['index', 'store', 'destroy', 'show'];

Route::resource('companies', 'CompaniesController', ['only' => ['show', 'update']]);
Route::post('/companies/{company}/public_image', 'UploadController@uploadPublicImage');
Route::post('/companies/{company}/files', 'UploadController@upload');

Route::resource('users', 'UsersController', ['only' => $methods]);
Route::get('me', 'UsersController@me');

Route::resource('projects', 'ProjectsController', ['only' => $methods]);
Route::get('projects/{project}/stats', 'ProjectsController@statistics');

Route::resource('projects.roles', 'RolesController', ['only' => $methods]);

Route::resource('projects.team', 'TeamsController', ['only' => $methods]);

Route::resource('projects.board_items', 'BoardItemsController', ['only' => $methods]);

Route::resource('projects.milestones', 'MilestonesController', ['only' => $methods]);
Route::get('projects/{project}/milestones/{milestone}/stats', 'MilestonesController@statistics');

Route::resource('projects.notes', 'NotesController', ['only' => $methods]);

Route::resource('projects.notes.pages', 'NotePagesController', ['only' => $methods]);

Route::resource('projects.tickets', 'TicketsController', ['only' => $methods]);

Route::resource('projects.tickets.comments', 'CommentsController', ['only' => $methodsWithoutUpdate]);

Route::resource('projects.board_items.comments', 'CommentsController', ['only' => $methodsWithoutUpdate]);

Route::resource('projects.tickets.files', 'FilesController', ['only' => $methodsWithoutUpdate]);

Route::resource('projects.board_items.files', 'FilesController', ['only' => $methodsWithoutUpdate]);

Route::resource('projects.board_items.comments.files', 'FilesController', ['only' => $methodsWithoutUpdate]);

Route::resource('projects.tickets.comments.files', 'FilesController', ['only' => $methodsWithoutUpdate]);
