{{ Form::open(array('url'=>'usuarios/signin', 'class'=>'form-signin')) }}
    <h2 class="form-signin-heading">Iniciar sesión</h2>
 
    {{ Form::text('email', null, array('class'=>'input-block-level', 'placeholder'=>'Correo electrónico')) }}
    {{ Form::password('password', array('class'=>'input-block-level', 'placeholder'=>'Contraseña')) }}
 
    {{ Form::submit('Ingresar', array('class'=>'btn btn-large btn-primary btn-block'))}}
{{ Form::close() }}