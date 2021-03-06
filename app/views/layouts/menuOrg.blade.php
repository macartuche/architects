	{{ HTML::link('#', $organization->name, array('class'=>'drop megamenu-top-header','id'=>'organization')) }}
	<div id="options" class="drop8columns dropcontent pull-left-450" style="left: auto; display: none; max-height: 639px;">
		<h3 class="col_6">{{ $organization->name }}</h3>  
		<div class="col_6"> 
			<ul class="project-menu-horizontal-list">
				<li> 
					<a href="/users/dashboard" title="Ver todas las iteraciones">
						<i class="topmenu-icon icon-globe"> </i>
						Escritorio
					</a>
				</li> 
				@if(Auth::user()->rol=='Administrator')
				<li>
					<a href="/organization/name/{{ $organization->auxName }}/projects" title="See the list of projects.">
						<i class=" icon-briefcase"></i> 
						Proyectos
					</a>
				</li>
				@endif
				
				<li>
					<a href="/messages" title="Lista de mensajes">
					<i class="glyphicon glyphicon-envelope"></i> 
						Mensajes 
					<span class="badge">{{Helper::messagesNumber()}}</span>
					</a>
			
				</li>
				
			
				@if(Auth::user()->rol=='Administrator')
				<li>
					<a href="/organization/members/{{ $organization->auxName }}/all_members" title="Get a listing of all members of the organization.">
						<i class="topmenu-icon icon-glyph icon-group"></i> 
						Miembros 
					</a>
				</li>
				@endif
				
				
				<li>
					<a href="/materials" title="Lista de materiales en la organización">
						<i class="topmenu-icon icon-glyph icon-material"></i> 
						Material 
					</a>
				</li>
				
				
				<li>
					<a href="/personalType" title="Lista de tipo de personal en la organización">
						<i class="topmenu-icon icon-glyph icon-group"></i> 
						Tipo de personal 
					</a>
				</li>
				 
				@if(Auth::user()->rol=='Administrator')
				<li>
					<a href="/organization/edit">
						<i class="topmenu-icon icon-glyph icon-edit"></i> 
						Editar organización</a>
				</li>
				@endif
			</ul>
		</div>

		<h3 class="col_8">Proyectos</h3> 
		<div class="col_8">
			<ul> 
			@if(Auth::user()->rol=='Administrator')
		    	@foreach ($organization->projects as $project)
		    	<li class="project-menu-iteration-list-item ">
					<a id="proyectID" class="organization-project-link" href="/projects/{{ $project->id }}" >
						{{ $project->name }}
					</a>
				</li>
				@endforeach 
			@else
				@foreach (Auth::user()->projects() as $project)
				<li class="project-menu-iteration-list-item ">
					<a id="proyectID" class="organization-project-link" href="/projects/{{ $project->id }}" >
						{{ $project->name }}
					</a>
				</li>
				@endforeach 
			@endif
			</ul>
		</div>   
	</div>