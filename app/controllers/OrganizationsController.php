<?php 

class OrganizationsController extends BaseController {
	protected $layout = "layouts.main";

	public function getNew() {
		$this->layout->content = View::make('layouts.organizations.organizationForm');
	}

	public function postCreate(){

		$validator = Validator::make(Input::all(), Organization::$rules);
		if ($validator->passes()) {

			//Upload the logo 
			$file = Input::file('image');
			$upload_success = Input::file('image')
			->move('public/uploads', $file->getClientOriginalName());

			//save the register
			$organization = new Organization;
			$organization->name = Input::get('name'); 
			$organization->test = Input::get('test'); 
			$organization->logo = $file->getClientOriginalName();
			$organization->address = Input::get('address'); 
			$organization->save();
			return Redirect::to('organization/new')
			->with('message', 'Registro creado con exito'); 


		}else{
			return Redirect::to('organization/new')
			->with('error', 'Ocurrieron los siguientes errores')
			->withErrors($validator)
			->withInput();   	
		}
		
	}

	public function getIndex() { 
		$this->layout->content = View::make('layouts.organizations.index')  ;
	}

	/**
	 * Show all projects of one enterprise 
	 **/
	public function getName($name){ 


		$organization = app('organization'); 
		
		setlocale(LC_MONETARY, 'en_US');
		foreach ($organization->projects as $project) {
			$project->nameAux = str_replace(" ",'-', $project->name);
			$project->nameAux = $this->stripAccents($project->nameAux);
			$project->budgetEstimated = $this->asDollars($project->budgetEstimated);
			//$pricetotal = asDollars($pricetotal);
			//$project->budgetEstimated = money_format('%(#10n',  $project->budgetEstimated);
		}
		//die();
		$this->layout->content = View::make('layouts.organizations.projects')
		->with('organization', $organization);
	}


	/**
	*Show all members of one enterprise
	**/
	public function getMembers($name){
		$organization = app('organization');
		$users = User::all(); 
		$this->layout->content = View::make('layouts.users.users')
		->with('organization', $organization)
		->with('users', $users);

	}


	/**
	*Edit organization
	**/
	public function getEdit(){
		$organization = app('organization');
		$this->layout->content = View::make('layouts.organizations.organizationEdit')
		->with('organization', $organization) ;
	}

	/**
	*
	**/
	public function postUpdate(){
		 
		$organization =   Organization::find(Input::get('id'));
		$organization->name = Input::get('name');  

		$file = Input::file('image');
		if($file!=null){
			$upload_success = Input::file('image')->move('public/uploads', $file->getClientOriginalName());
			$organization->logo = $file->getClientOriginalName();
		}

		$organization->address = Input::get('address'); 
		$organization->webPage = Input::get('webPage'); 
		$organization->save();
		return Redirect::to('organization/edit')
		->with('message', 'Registro actualizado con exito'); 
	}

	/**
	* Replace accents 
	**/
	private function stripAccents($str) {
		return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
	}

	private function asDollars($value) {
		return '$' . number_format($value, 2);
	}
}
?>
