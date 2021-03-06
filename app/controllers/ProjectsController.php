<?php 

class ProjectsController extends BaseController {
	protected $layout = "layouts.main";

 	public function index() {
 		 
 	}

    public function show($id) { 

    	try {
			$project = Project::findOrFail($id); 
			$iterations =sizeof($project->iterations);
			$idIterations = array();
			/*			
			$current_date = date("Y-m-d");
			print_r($current_date);  
			print_r(gettype($current_date));
			print_r(' < ');  
	        $project = Project::findOrFail($id);
	        $end_date = $project->endDate;
	        print_r($end_date);
	        print_r(gettype($end_date));
	        print_r(' >>> ');  
	        if($current_date < $end_date){
				 print_r('mayor');
			}else{ 
				 print_r('menor');
			}
			die();
			*/
			foreach ($project->iterations as $iteration) {
				$idIterations[] = $iteration->id;
			}

			if($iterations==0){
				$totalStories=0;
				$storiesCompleted=0;
				$storiesProgress=0;
				$this->layout->content = View::make('layouts.projects.show')
								->with('project', $project)
								->with('iterations', $iterations)
								->with('totalStories', $totalStories)
								->with('completed', $storiesCompleted)
								->with('doing', $storiesProgress);
			}else{

				$resultBudget = $this->getBudgetSummary($id);
				$resultTime = $this->getTimeSummary($id);
				$stories= Issue::whereIn('iterationid', $idIterations)->get();
				$totalStories = sizeof($stories);
				$storiesCompleted = Issue::whereIn('iterationid', $idIterations)->
								where('currentState', 'TO-DO')->get();
				$storiesProgress = Issue::whereIn('iterationid', $idIterations)->
								where('currentState', 'DOING')->get();
								$this->layout->content = View::make('layouts.projects.show')
								->with('estimatedBudget', $resultBudget['estimated_budget'])
								->with('realBudget', $resultBudget['real_budget'])
								->with('resultBudget', $resultBudget['result_budget'])
								->with('estimatedTime', $resultTime['estimated_time'])
								->with('realTime', $resultTime['real_time'])
								->with('resultTime', $resultTime['result_time'])
								->with('project', $project)
								->with('iterations', $iterations)
								->with('totalStories', $totalStories)
								->with('completed', count($storiesCompleted))
								->with('doing', count($storiesProgress));
			}

		}catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
			$organization = app('organization');
		    return Redirect::to('/materials/')
			->with('message', 'No existe el material');
		}
    }


	public function create(){  
		$this->layout->content = View::make('layouts.projects.new')
		->with('organization', app('organization')) 
		->with('type',  'new') ;
	}

	//save mew
	public function store(){
		//$validator = Validator::make(Input::all(), Project::$rules);
		$validator = Validator::make(Input::all(), Project::$rules, Project::$messages);

		if ($validator->passes()) { 

			$project = new Project;
			$project->name = Input::get('name'); 
			$project->startDate = Input::get('startDate'); 
			$project->endDate = Input::get('endDate');   
			$project->budgetEstimated = Input::get('budgetEstimated');  
			$project->organizationid = Input::get('organizationid'); 
			if($project->save()){
				$team = new Teams;
				$team->name = 'Grupo - ' . $project->name;
				$team->projectid = $project->id;
				$team->save();
			}
			$organization = app('organization');
			return Redirect::to('organization/name/'.$organization->auxName.'/projects')
			->with('message', 'Registro creado con exito'); 
		}else{
			return Redirect::to('projects/create')
			->with('error', 'Ocurrieron los siguientes errores')
			->withErrors($validator)
			->withInput();   	
		}
	}

	public function edit($id){
		try {
			$project = Project::findOrFail($id);
		}catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
			$organization = app('organization');
		    return Redirect::to('/organization/name/'.$organization->auxName.'/projects')
			->with('message', 'No existe el proyecto');
		}
		
		 
		$this->layout->content = View::make('layouts.projects.new')
		->with('project', $project)->with('type',  'edit')  ;
	}

	public function update($id){
		$project = Project::findOrFail($id);

		$validator = Validator::make(Input::all(), Project::$rules, Project::$messages);

		if ($validator->passes()) { 

			$project->fill(Input::all());
			$project->save();
			$organization = app('organization');
			return Redirect::to('organization/name/'.$organization->auxName.'/projects')
				->with('message', 'Registro actualizado');
		}else{
			return Redirect::to('projects/'.$id.'/edit')
			->with('error', 'Ocurrieron los siguientes errores')
			->withErrors($validator)
			->withInput();   	
		}
	}

	public function destroy($id){
		//project
		$project = Project::find($id);
		$organization = app('organization');
		if( sizeof($project->iterations) < 1 ){
			$team = $project->team();
			$team->delete();
			$project->delete();
			return Redirect::to('organization/name/'.$organization->auxName.'/projects')
				->with('message', 'Registro eliminado correctamente');
		}else{
			return Redirect::to('organization/name/'.$organization->auxName.'/projects')
			->with('error', 'El proyecto ya se encuentra iniciado.');
//			->withInput();   	
		}

		
		
	}

	public function getMembers($id){
		$organization = app('organization');
		$project = Project::find($id);
		$team = $project->team;
		$users = User::all();
		$members;

		//echo 'Hoal mundo >>>>>>>>>>>>>>>>>>>>>>>>';
		//die;

		if($team != null){
			$members = Teams::find($team->id)->users;
			$members = array_pluck($members, 'id');
			$this->layout->content = View::make('layouts.projects.addMembers')
								->with('organization', $organization)
								->with('project',$project)
								->with('team', $team)
								->with('users', $users)
								->with('members',$members);	
		}
		//$users = Teams::find($team->id)->users();
		//$organization = app('organization');
		//$this->layout->content = View::make('layouts.projects.addMembers')
		//						->with('organization', $organization)
		//						->with('users', $users);
		//$this->layout->content = View::make('layouts.projects.addMembers');
		//						->with('organization', $organization)
		//						->with('users', $users);
	}

	public function postAsigned() { 
 		$members = Input::get('users_id');
 		$project_id = Input::get('project_id');
 		$team_id = Input::get('team_id');
 		$var = null;
 		$num = count($members) ;
		$team = Teams::find($team_id);
    	$team->users()->sync($members);
 		return Redirect::to('projects/members/'.$project_id)->with('message', 'Se han asignado los miembros  .' );
	}

	public function getFinalize($id){
		try {
			$project = Project::findOrFail($id); 
			$iterations =sizeof($project->iterations);
			$totalRealTime = 0;
			$totalEstimatedTime = 0;
			foreach ($project->iterations as $iteration) {

				$totalEstimatedTime += $iteration->estimatedBudget;
				$totalRealTime += $iteration->realBudget;

			}

			if($iterations==0){
				
			}else{
				
			}

		}catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
			$organization = app('organization');
			return Redirect::to('organization/name/'.$organization->auxName.'/projects')
			->with('error', 'No existen iteraciones en el proyecto.');
		}
	}

	public function getBudgetSummary($id){
		try {
			$project = Project::findOrFail($id); 
			$iterations =sizeof($project->iterations);
			$totalEstimatedBudget = 0;
			$totalRealBudget = 0;
			$diferencia = 0;
			$resultado = 0;
			$respuesta = '';
			$result= array();
			if(!$iterations==0){
				foreach ($project->iterations as $iteration) {
					$totalEstimatedBudget += $iteration->estimatedBudget;
					$totalRealBudget += $iteration->realBudget;
				}

				//Aumento
				if($totalEstimatedBudget > $totalRealBudget){
					$respuesta = $respuesta . 'Existe una reducción del ';
					$diferencia = $totalEstimatedBudget - $totalRealBudget;
				//Reduccion
				}else{
					$respuesta = $respuesta . 'Existe un aumento del ';
					$diferencia = $totalRealBudget - $totalEstimatedBudget;
				}

				$resultado = ($diferencia / $totalEstimatedBudget) * 100;
				$resultado = round($resultado, 2);
				$respuesta = $respuesta . $resultado . '% referente al presupuesto estimado.'; 

				$result['estimated_budget'] = round($totalEstimatedBudget, 2);
				$result['real_budget'] = round($totalRealBudget, 2);
				$result['result_budget'] = $respuesta;

				return $result;


			}else{
				
				$result['estimated_budget'] = $totalEstimatedBudget;
				$result['real_budget'] = $totalRealBudget;
				$result['result_budget'] = $respuesta;	
				return $result;		
			}

		}catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
			$organization = app('organization');
			return Redirect::to('organization/name/'.$organization->auxName.'/projects')
			->with('error', 'No existen iteraciones en el proyecto.');
		}

	}

	public function getTimeSummary($id){
		try {
			$project = Project::findOrFail($id); 
			$iterations =sizeof($project->iterations);
			$totalEstimatedTime = 0;
			$totalRealTime = 0;
			$diferencia = 0;
			$resultado = 0;
			$respuesta = '';
			$result= array();
			if(!$iterations==0){
				foreach ($project->iterations as $iteration) {
					$totalEstimatedTime += $iteration->estimatedTime;
					$totalRealTime += $iteration->realTime;
				}

				//Aumento
				if($totalEstimatedTime > $totalRealTime){
					$respuesta = $respuesta . 'Existe una reducción del ';
					$diferencia = $totalEstimatedTime - $totalRealTime;
				//Reduccion
				}else{
					$respuesta = $respuesta . 'Existe un aumento del ';
					$diferencia = $totalRealTime - $totalEstimatedTime;
				}

				if($diferencia > 0){
					$resultado = ($diferencia / $totalEstimatedTime) * 100;	
				}
				
				
				$resultado = round($resultado, 2);
				$respuesta = $respuesta . $resultado . '% referente al tiempo estimado.'; 

				$result['estimated_time'] = round($totalEstimatedTime, 2);
				$result['real_time'] = round($totalRealTime, 2);
				$result['result_time'] = $respuesta;

				return $result;

			}else{
				
				$result['estimated_time'] = $totalEstimatedBudget;
				$result['real_time'] = $totalRealBudget;
				$result['result_time'] = $respuesta;	
				return $result;		
			}

		}catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
			$organization = app('organization');
			return Redirect::to('organization/name/'.$organization->auxName.'/projects')
			->with('error', 'No existen iteraciones en el proyecto.');
		}
	}

}
?>