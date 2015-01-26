<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(array('before' => 'auth'), function()
{
	Route::controller('organization', 'OrganizationsController');
	Route::controller('states','ScrumStatesController');
	Route::resource('projects', 'ProjectsController');
	Route::resource('materials', 'MaterialsController');
	Route::resource('teams', 'TeamsController');
	Route::resource('taskBoard', 'TaskBoardController'); 
	Route::resource('issue', 'IssueController');
	Route::resource('iterations', 'IterationsController');
	Route::resource('task', 'TaskController');
	Route::resource('personalType', 'PersonalTypeController');
});
 
Route::controller('users', 'UsersController');
Route::get('/', 'UsersController@getLogin');

Route::post('task', function(){ 
	if(Request::ajax()){ 
		$task = new Task; 
		$task->name = Input::get("name");
		$task->summary = Input::get("summary");
		//$task->points  = Input::get("points");
		$task->points  = 1;
		$task->timeEstimated = Input::get("test");
		$task->timeRemaining = Input::get("test");
		$task->scrumid = 1; //estado todo ...quemado por código
		$task->issueid = Input::get("issueid"); 
		$task->save();
		if($task){  
			return Response::json(array(
                    'success' => true,
                    'message' => 'Tarea registrada',
                    'task' =>  $task
            ));
		}else{
			 return Response::json(array(
                'success' => false,
                'errors' => 'Error al grabar'
            )); 
		}
	}
});
//get specific task
Route::get('tareas/getTask', function(){ 
	if(Request::ajax()){ 
		$id = Input::get("id");
		$task = Task::findOrFail($id);
		return Response::json(array('task'=>$task));
	}
});

//get all task
Route::get('tareas/taskAll', function(){ 
	if(Request::ajax()){ 
		$id = Input::get("id");
		$tasks = Task::where('issueid','=', $id)->get();
		return Response::json(array('tasks'=>$tasks));
	}
});

//udpate task
Route::get('tareas/updateTaks', function(){ 
	if(Request::ajax()){ 
		$id = Input::get("id");
		$state = Input::get("state");
		$task = Task::findOrFail($id);
		
		switch($state){
			case "todo":
				$task->scrumid = 1; 
			break;
			case "haciendo":
				$task->scrumid = 2;
			break;
			case "hecho":
				$task->scrumid = 3;
			break;
		}
		$task->save();
		return Response::json(array('succes'=>'1'));
	}
});
