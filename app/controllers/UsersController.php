<?php

class UsersController extends BaseController {
	protected $layout = "layouts.main";

	public function __construct() {
		$this->beforeFilter('csrf', array('on'=>'post'));
		$this->beforeFilter('auth', array('only'=>array('getDashboard')));
	} 

	public function index(){
		$organization = app('organization');  
		$this->layout->content = View::make('layouts.users.users')
								->with('organization', $organization);
	}

    //users/register
	public function getRegister() {  
		$this->layout->content = View::make('layouts.users.form')
		->with('organization', app('organization'))
		->with('type', 'new');
	}

	public function getRecoverpassword(){
		$this->layout->content = View::make('layouts.users.formrecoverpassword')
		->with('organization', app('organization'));
	}

	//POST users/create
	public function postCreate() { 
 		$organization = app('organization');  
		$rules = User::$rules;
		$rules['password'] .= '|required';
		$validator = Validator::make(Input::all(), $rules, User::$messages);

		if ($validator->passes()) {


			$user = new User;
			$user->name = Input::get('nombres'); 
			$user->lastname = Input::get('apellidos');
			$user->identification = Input::get('identification');
			$user->phone = Input::get('telefono');
			$user->mail = Input::get('mail');
			$user->direction = Input::get('direccion');
			$user->password = Hash::make(Input::get('password'));
			$user->rol = 'User';
			$user->active = 1;
			//upload de image
			$file = Input::file('image'); 
			if($file!=null){
				$upload_success = $file->move('public/uploads/users/', $file->getClientOriginalName());
				$user->avatar = $file->getClientOriginalName();
			}
			$functionId = Input::get('functionid');
			if ($functionId == 0) {
    			//crear funcion
					$function = new Functions;
					$function->name = Input::get('function_name');
					$function->save();
					$user->functionid = $function->id;
			}else{
					$user->functionid = Input::get('functionid');
			}

			if($user->save()){
				try
				{
			    	// try
			    	Mail::send('layouts.users.welcome', array('firstname'=>Input::get('nombres'), 'mail'=>Input::get('mail'), 'password'=>Input::get('password')), function($message){
        				$message->to(Input::get('mail'), Input::get('nombres').' '.Input::get('apellidos'))->subject('Bienvenido!!');
    					});

    				return Redirect::to('/organization/members/'. $organization->auxName .'/all_members')
						->with('message', 'Cuenta creada exitósamente, revise los datos de su cuenta en su correo.');

				}
				catch(Exception $e)
				{
			    	// fail
			    	return Redirect::to('/organization/members/'. $organization->auxName .'/all_members')
						->with('message', 'Cuenta creada exitósamente, no se pudieron enviar los datos de su cuenta al correo.');
				}	
			}
		
		} else {
//			return Redirect::to('users/register')
			return Redirect::to('users/new')
			->with('error', 'Ocurrieron los siguientes errores.')
			->withErrors($validator)
			->withInput();   
		}
	}

	//  /users/login
	public function getLogin() {
		if(Auth::check()){
			return Redirect::to('users/dashboard')->with('message', '');
		}else{
			$this->layout->content = View::make('layouts.users.login');
		}
		
	}

	// /users/sigin
	public function postSignin() 
	{

		$data =array('mail'=>Input::get('mail'), 
				'password'=>Input::get('password'),
				'active'=>1);
		
		if (Auth::attempt($data)) {
			return Redirect::to('users/dashboard')->with('message', 'Ha iniciado sesión');
		} else {
			return Redirect::to('users/login')
			->with('error', 'Tu email/password es incorrecto o la cuenta está inactiva.')
			->withInput();
		}      
	}


	public function getEdit($id){

		try {
			$user = User::findOrFail($id);
		}catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
			$organization = app('organization');
		    return Redirect::to('/organization/members/'. $organization->auxName . '/all_members')
			->with('message', 'No existe el usuario');
		}

		$functions = Functions::all();
		$idFunction = $user->functionid;
		
		$this->layout->content = View::make('layouts.users.edit')
	//		->with('organization', app('organization'))
			->with('user', $user)
			->with('functions', $functions)
			->with('idFunction', $idFunction)
			->with('type', "edit");

			//->with('type', "edit");
	}

	public function getEditprofile(){

		try {
			$user = Auth::user();
		}catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
			$organization = app('organization');
		    return Redirect::to('/organization/members/'. $organization->auxName . '/all_members')
			->with('error', 'No existe el usuario');
		}

		$functions = Functions::all();
		$idFunction = $user->functionid;
		
		$this->layout->content = View::make('layouts.users.editprofile')
			->with('user', $user)
			->with('functions', $functions)
			->with('idFunction', $idFunction)
			->with('type', "edit");
	}	

	public function postUpdate($id){
		$user = User::findOrFail($id);
		$rules = User::$rules;
		$rules['identification'] .= ',identification,' . $id;
		$rules['mail'] .= ',mail,' . $id;
		$validator = Validator::make(Input::all(), $rules, User::$messages);

		if($validator->passes()){
			$user->name = Input::get('nombres'); 
			$user->lastname = Input::get('apellidos');
			$user->identification = Input::get('identification');
			$user->phone = Input::get('telefono');
			$user->mail = Input::get('mail');
			$user->direction = Input::get('direccion');

			if(!empty(Input::get('password'))){
				$user->password = Hash::make(Input::get('password'));
			}
			
			//upload de image
			$file = Input::file('image');  
			if($file!=null){
				$upload_success = $file->move('public/uploads/users/', $file->getClientOriginalName());
				$user->avatar = $file->getClientOriginalName();
			}
			$functionId = Input::get('functionid');
			if ($functionId == 0) {
    			//crear funcion
					$function = new Functions;
					$function->name = Input::get('function_name');
					$function->save();
					$user->functionid = $function->id;
			}else{
					$user->functionid = Input::get('functionid');
			}
			$user->save();
			$organization = app('organization');

			if( isset($_POST['myprofile']) ){

			//if(Input::get('myprofile')){	
				return Redirect::to('/users/dashboard')
				->with('message', 'Registro actualizado');
			}else{
				return Redirect::to('/organization/members/'. $organization->auxName . '/all_members')
				->with('message', 'Registro actualizado');	
			}

			

		}else{
			return Redirect::to('users/edit/'.$id)
			->with('error', 'Ocurrieron los siguientes errores')
			->withErrors($validator)
			->withInput();
		}		
	}


 
 	/**
 	* Get home after logged in
 	**/
	public function getDashboard() {
		$organization = app('organization');
		$projectsCount = sizeof($organization->projects);
		$idProjects = array();
		foreach($organization->projects as $project){
			$idProjects[]=$project->id;
		}

		$iterations = Iterations::whereIn('projectid', $idProjects)->get();
		$idIterations = array();

		
		foreach($iterations as $iteration){
			$idIterations[]=$iteration->id;
		}

		$issues = Issue::whereIn('iterationid', $idIterations)->get();

		$idStories = array();
		foreach ($issues  as $issue) {
			$idStories[] = $issue->id;
		}
		$tasks = Task::whereIn('issueid', $idStories)->get();
		
		$this->layout->content = View::make('layouts.users.dashboard')
    	->with('organization', $organization) 
    	->with('projectsCount', $projectsCount)
    	->with('iterationsCount', count($iterations))
    	->with('taskCount', count($tasks));
	}

	public function getLogout() {
    	Auth::logout();
    	return Redirect::to('users/login')->with('message', 'Ha finalizado su sesión');
	}


	public function getNew() {
		//$this->layout->content = View::make('layouts.users.register');
		$functions = Functions::all();
		$idFunction = 0;
		$this->layout->content = View::make('layouts.users.form')
		->with('organization', app('organization'))
		->with('functions', $functions)
		->with('idFunction', $idFunction)
		->with('type', 'new');
	}

	public function postSendpasswordrecovery(){
		$mail = Input::get('mail'); 
		$user = User::where('mail', '=', $mail )->get()->first();

		if ($user) {
			$passwordTmp = substr( $user->name, 0, 3) . substr( $user->lastname, 0, 3);
			$user->password = Hash::make($passwordTmp);
			$user->save();
			Mail::send('layouts.users.recoverpassword', array('name'=>$user->name, 'mail'=> $user->mail, 'password'=> $passwordTmp), function($message){
        		$message->to(Input::get('mail'))->subject('Nuevo password!!');
    		});

    		return Redirect::to('users/login')
			->with('message', 'Se ha generado un constraseña temporal, revise su cuenta de correo.')
			->withInput();

//			return Redirect::to('users/login')
//			->with('message', 'Se ha generado un constraseña temporal, está pendiente el envío a su correo.')
//			->withInput();

		} else {
			return Redirect::to('users/recoverpassword')
			->with('message', 'Tu email no se encuentra registrado.')
			->withInput();
		}      
	}

	//INACTIVAR
	public function getInactivate($id){
		$user = $this->actualizar($id,0);
		$organization = app('organization');
		if($user->save()){ 
			 
			return Redirect::to('/organization/members/'. $organization->auxName . '/all_members')
			->with('message', 'Registro actualizado')
			->with('organization', $organization);
		}else{
			return Redirect::to('/organization/members/'. $organization->auxName . '/all_members')
				->with('error', 'Error al actualizar');	
		}
	}

	//Activar
	public function getActivate($id){
		$user = $this->actualizar($id,1);
		$organization = app('organization');
		if($user->save()){ 
			 
			return Redirect::to('/organization/members/'. $organization->auxName . '/all_members')
			->with('message', 'Registro actualizado')
			->with('organization', $organization);
		}else{

			die("12121");
			return Redirect::to('/organization/members/'. $organization->auxName . '/all_members')
				->with('error', 'Error al actualizar');	
		}
	}

	private function actualizar($id, $active){
		$user = User::findOrFail($id); 
		$user->active=$active;
		
		return $user;
		
	}



	public function postFinalize($id){

	}
/*
	public function postPassword()
    {
        $rules = array(
            'password' => 'required',
            'newpassword' => 'required|min:5',
            'repassword' => 'required|same:newpassword'
        );

        $messages = array(
                'required' => 'El campo :attribute es obligatorio.',
                'min' => 'El campo :attribute no puede tener menos de :min carácteres.'
        );

        $validation = Validator::make(Input::all(), $rules, $messages);
        if ($validation->fails())
        {
            return Redirect::to('password')->withErrors($validation)->withInput();
        }
        else{
            if (Hash::check(Input::get('password'), Auth::user()->Password))
            {
                $cliente = new cliente();
                $cliente = Auth::user();
                $cliente->Password = Hash::make(Input::get('newpassword'));
                $cliente->save();
               
                   
                   if($cliente->save()){
                        return Redirect::to('password')->with('notice', 'Nueva contraseña guardada correctamente');
                   }
                   else
                   {
                       return Redirect::to('password')->with('notice', 'No se ha podido guardar la nueva contaseña');
                    }
            }
            else
            {
                return Redirect::to('password')->with('notice', 'La contraseña actual no es correcta')->withInput();
            }

        }
    }
	*/
}
?>