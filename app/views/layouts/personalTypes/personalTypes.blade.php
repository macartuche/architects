<div id="body" class="container">
	<a href="/personalType/create" style="text-decoration:none; vertical-align:middle" 
	class="btn btn-success pull-right">
	<i class="icon-plus-sign"></i>
	Nuevo
	</a>
	<h1>Tipo de personal</h1>
		<br>
		<table id="personalTypes" class="table table-bordered table-striped">
			<tbody>
				<tr>
					<th style="width:180px;">Nombre</th>
					<th style="width:180px;">Descripcion</th>
					<th style="width:180px;">Código</th>
					<th style="width:100px;"></th>
				</tr>
				@foreach($personalTypes as $personalType)
				<tr>
					<td>{{$personalType->name}}</td>
					<td>{{$personalType->description}}</td>
					<td>{{$personalType->code}}</td>
					<td>
						{{HTML::link('personalType/' . $personalType->id . '/edit', 'Editar', array('class'=>"btn btn-medium btn-info") )}}
						&nbsp;
						{{ Form::open(array('url' => 'personalType/' . $personalType->id, 'class' => 'pull-right')) }}
							{{ Form::hidden('_method', 'DELETE') }}
							{{ Form::submit('Eliminar', array('class' => 'btn btn-danger btn-medium')) }}
						{{ Form::close() }}	
					</td>
					
				</tr>
				@endforeach
			</tbody>
		</table>
</div>