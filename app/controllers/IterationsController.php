<?php 

class IterationsController extends BaseController {
	protected $layout = "layouts.main";

 	public function index() {
 		 
 	}

    public function show($id) { 
   
    	try {
			$iteration = Iterations::findOrFail($id); 
			$iterations = Iterations::where('projectid','=', $iteration->projectid)->get();
			$project = Project::findOrFail($iteration->projectid);
			$issues = Issue::where('iterationid','=', $id)->get();  
			//$issues = $iteration->issues;
			$countIssues = sizeof($issues);
			$categories = Category::all();
			$idCategory = 0;
			$totalPoints = $issues->sum('points'); 
			$materiales = Material::all();
			$personal = PersonalType::all();

			$team = Teams::where('projectid','=',$project->id)->get()->first(); 

			$members = DB::table('memberof')->where('teamid','=', $team->id)->get();



			$hasmembers = (sizeof($members)>0)? true : false;
			
			$users = array();
			foreach($members as $member){
				
				$users[] = User::findOrFail($member->usersid);
				echo $hasmembers;

			}
 
 	 

			$this->layout->content = View::make('layouts.iterations.show')
								->with('iteration', $iteration)
								->with('iterations', $iterations)
								->with('issues', $issues)
								->with('categories', $categories)
								->with('idCategory', $idCategory)
								->with('countIssues', $countIssues)
								->with('totalPoints', $totalPoints)
								->with('project', $project)
								->with('materiales', $materiales)
								->with('personal', $personal)
								->with('hasmembers', $hasmembers)
								->with('members', $members)
								->with('users', $users)
								->with('message', '');
		}catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
		    return Redirect::to('/projects/')
			->with('message', 'Error al crear la iteración');
		}
    }


	public function create(){   
		$projectid = Input::get('projectid');
		$project = Project::findOrFail($projectid);  
		$iterations =sizeof($project->iterations);
		$members = sizeof($project->team->users); 
		if($iterations < 1 and $members < 1){
			return Redirect::to('/projects/'.$project->id)
			->with('error', 'No se han agregado miembros a equipo del proyecto.');
		}else{
			$this->layout->content = View::make('layouts.iterations.form')
				->with('project', $project) 
				->with('type',  'new');	
		}

		
	}

	//date validator
	public function validateDate($inicio, $fin){
		 
		if($inicio > $fin){
			return false;
		}else{ 
			return true;
		}
	}

	public function validateDateWithProjectDate($projectid, $inicio, $fin){
		$project = Project::findOrFail($projectid);
        $start_date = $project->startDate;
        $end_date = $project->endDate;

		if($inicio >= $start_date && $fin <=$end_date){
			return true;
		}else{ 
			return false;
		}
	}

	//save mew
	public function store(){

		$validator = Validator::make(Input::all(), Iterations::$rules, Iterations::$messages);

		$inicio = Input::get('start'); 
		$end  = Input::get('end');   
		$projectid = Input::get('projectid');


		$valido = $this->validateDate($inicio, $end); 
		if(!$valido){
			return Redirect::to('iterations/create?projectid='.Input::get('projectid'))
				->with('error', 'La fecha inicial es mayor que la final')
				->withErrors($validator)
				->withInput();
		}else{
			$valido = $this->validateDateWithProjectDate($projectid, $inicio, $end); 
			if(!$valido){
				return Redirect::to('iterations/create?projectid='.Input::get('projectid'))
					->with('error', 'La fecha de inicio o fin estan fuera de las fechas de ejecución del projecto.')
					->withErrors($validator)
					->withInput();
			}
		}

		if($validator->passes() && $valido){
		//if($validator->passes()){
			$iterations = new Iterations;
			$iterations->name = Input::get('name'); 
			$iterations->start = Input::get('start'); 
			$iterations->end = Input::get('end');   
			$iterations->estimatedBudget = Input::get('estimatedBudget');  
			$iterations->projectid = $projectid;
			$iterations->save();


			$organization = app('organization');
			return Redirect::to('/iterations/'.$iterations->id)
			->with('message', 'Iteracion creada con exito'); 
			
		}else{
			return Redirect::to('iterations/create?projectid='.Input::get('projectid'))
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
			->with('error', 'No existe el proyecto');
		}
		
		 
		$this->layout->content = View::make('layouts.projects.new')
		->with('project', $project)->with('type',  'edit')  ;
	}

	public function update($id){
		/*$project = Project::findOrFail($id);
		$project->fill(Input::all());
		$project->save();
		$organization = app('organization');
		return Redirect::to('organization/name/'.$organization->auxName.'/projects')
			->with('message', 'Registro actualizado');*/

		$validator = Validator::make(Input::all(), Iterations::$rules, Iterations::$messages);

		$inicio = Input::get('start'); 
		$end  = Input::get('end');   
		$projectid = Input::get('projectid');


		$valido = $this->validateDate($inicio, $end); 
		if(!$valido){
			return Redirect::to('iterations/create?projectid='.Input::get('projectid'))
				->with('error', 'La fecha inicial es mayor que la final')
				->withErrors($validator)
				->withInput();
		}else{
			$valido = $this->validateDateWithProjectDate($projectid, $inicio, $end); 
			if(!$valido){
				return Redirect::to('iterations/edit'.$id)
					->with('error', 'La fecha de inicio o fin estan fuera de las fechas de ejecución del projecto.')
					->withErrors($validator)
					->withInput();
			}
		}

		if($validator->passes() && $valido){
			$project->fill(Input::all());
			$project->save();
			$organization = app('organization');
			return Redirect::to('organization/name/'.$organization->auxName.'/projects')
				->with('message', 'Registro actualizado');
		}else{
			return Redirect::to('iterations/create?projectid='.Input::get('projectid'))
			->with('error', 'Ocurrieron los siguientes errores')
			->withErrors($validator)
			->withInput();
		}


	}


	public function destroy($id){
		//project
		$project = Project::find($id);
		if( sizeof($project->iterations) < 1 ){
			$project->delete();
		}

		$organization = app('organization');
		return Redirect::to('organization/name/'.$organization->auxName.'/projects')->with('message', 'Registro eliminado');
	}
}
?>