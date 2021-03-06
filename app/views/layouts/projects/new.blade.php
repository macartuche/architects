
<br><br>

<div id="projectError">
	@if($errors->all())
	<ul class="alert alert-error">
		@foreach($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul> 
	@endif
</div>
	<h1>Crear/Editar proyecto</h1>
	<div class="panel">
		<?php 
			if($type == "new"){
		 ?>
		{{ Form::open(array('url'=>'projects','files'=>true, 'class'=>'uniForm')) }}
		<?php }else { ?>
		{{ Form::model($project,array('action' => array('ProjectsController@update', $project->id), 'method' => 'PUT', 'class'=>'uniForm')) }}	
		<?php } ?>
			<fieldset class="inlineLabels">
				<div class="ctrlHolder" id="div_id_name">
					{{ Form::label('name', 'Nombre' , array('class'=>'requiredField' )) }}
					{{ Form::text('name', null, array('class'=>'textInput textinput', 'placeholder'=>'Nombre del proyecto')) }}
				</div>
				<div class="ctrlHolder" id="div_id_name">
					{{ Form::label('startDate', 'Fecha de inicio', array('class'=>'requiredField' )) }}
					{{ Form::text('startDate', null, array('type' => 'text', 'class' => 'textInput textinput datepicker input-block-level','placeholder' => 'Fecha inicio', 'id' => 'startDate')) }}
				</div>
				<div class="ctrlHolder" id="div_id_name">
					{{ Form::label('endDate', 'Fecha de fin', array('class'=>'requiredField' )) }}
					{{ Form::text('endDate', null, array('type' => 'text', 'class' => 'textInput textinput datepicker input-block-level','placeholder' => 'Fecha de fin', 'id' => 'endDate')) }}
				</div>
				<div class="ctrlHolder" id="div_id_name">
					{{ Form::label('budgetEstimated', 'Presupuesto estimado', array('class'=>'requiredField' )) }}
					{{ Form::text('budgetEstimated', null, array('class'=>'textInput textinput', 'placeholder'=>'Presupuesto estimado del proyecto')) }}
				</div>

				<div class="ctrlHolder" id="div_id_name">
					{{ Form::label('observation', 'Observaciones', array('class'=>'requiredField' )) }}
					{{ Form::textArea('observation', null, array('class'=>'textInput textinput', 'placeholder'=>'Observaciones del proyecto')) }}
				</div>

				{{ Form::hidden('organizationid', $organization->id) }}

				@if(Auth::user()->rol=='Administrator')
				<div class="buttonHolder">

					{{ HTML::link('/organization/name/' . $organization->auxName . '/projects/',  'Cancelar', array('class'=>"btn btn-danger btn-sm")  ) }} 

					{{ Form::submit('Guardar  ', array('class'=>'btn btn-primary'))}}
				</div>
				@endif

			</fieldset>
		{{ Form::close() }}
	</div>


<script type="text/javascript">
$(document).ready(function() {

	$('#startDate').datepicker({
		clearBtn: true,
		calendarWeeks: true,
		autoclose: true,
		todayHighlight: true,
		format: "yyyy-mm-dd"
	});

	$('#endDate').datepicker({
		clearBtn: true,
		calendarWeeks: true,
		autoclose: true,
		todayHighlight: true,
		format: "yyyy-mm-dd"
	});
} );
</script>