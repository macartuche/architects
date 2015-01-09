<ul id="messages">
	<li id="message_1"><a onclick="$('#message_1').fadeOut(); return false;" href="#"><small>Limpiar</small></a>{{ $message }}</li>
</ul>
<div class="project-body-header">
	<span class="iteration-board-link">
		<a href="/projects/project/k-gestion/iteration/117871/board">
			<i class="icon-th"></i>Pizarra scrum
		</a>
	</span>
	<h1 id="tour-iteration-name">
		{{ $iteration->name }} 
		<span class="iteration-title-date">
			{{ $iteration->start }} - {{ $iteration->end }}
		</span>
	</h1>
	<div id="iteration_stats" class="project-body-header-stats">
		<div class="stats-bubble">
			Historias
			<h4>{{ $countIssues }}</h4> 
		</div>
		<div class="stats-bubble">
			Total Puntos
			<h4>{{ $totalPoints }}</h4> 
		</div>
		<div class="stats-bubble">
			Puntos en progreso
			<h4>0</h4> 
		</div>
		<div class="stats-bubble">
			Puntos completados
			<h4>0</h4> 
		</div>	
		<div class="stats-bubble">
			Dias restantes
			<h4>2</h4> 
		</div>
	</div>
	<div style="" id="burnup_chart">
		<div style="" id="iterationBurndown" class="noData">
			No hay suficientes datos
		</div>
		<br>
		<img src="https://d11uy15xvlvge3.cloudfront.net/static/v105/scrumdo/images/burndown.png">
	</div>	
</div>
<div class="container wide_body" id="body">
	<div style="margin-left:auto; margin-right:auto; width:100%;">
		<div id="story_form" class="story_form" style="margin-left:auto; margin-right:auto">
			<ul id="createdStories">
			</ul>
			<div id="addStoryFormOnProgress" class="hidden">Guardando historia.  Por favor espere...</div> 
			{{ Form::open(array('url'=>'issue','class'=>'uniForm', 'id'=>'addStoryForm')) }}
			<textarea id="summary" rows="1" cols="50" name="summary" maxlength="5000"></textarea>
			<button id="add_button" type="submit" class="btn">Agregar historia</button>
			<div class="iteration-app">
				@include('layouts.issue.form')
			</div>
			{{ Form::close() }}	
		</div>
		<img src="https://d11uy15xvlvge3.cloudfront.net/static/v105/scrumdo/images/ajax-loader.gif" id="loadingIcon">
		<h1>Historia</h1>
		<ul id="tour-story-list" class="story-list ui-sortable" style="">
			@foreach ($issues as $issue)
			<li class="story-view superboard-story story_block gripper-status-1" story_id="863903" id="superboard_story_863903" rank="499999">
				<div class="story-checkbox-holder" style="display: none;">
					<input type="checkbox" class="story-checkbox">
				</div>
				<div class="block_story_body story-border">
					<div class="story-content">
						<span class="story-icons">
							<a href="#" class="moveIterationIcon" story_id="863903">
								<i class="icon-glyph icon-share" title="Move to another iteration."></i>
							</a> 
							<a href="#" class="story-status-button">
								<i class="icon-glyph icon-tag" title="Change story status."></i>
							</a>    
							<a href="#" class="edit-story-button">
								<i class="icon-glyph icon-edit" title="Edit story"></i>
							</a>  
						</span>
						<h1 class="formatted_story_text story-summary">
							<span style="color:#555555;" class="story_number">#{{ $issue->id }}</span>
							<p>{{ $issue->summary }}</p>
						</h1>
						<div class="formatted_story_text story_detail">
							<p>{{ $issue->detail }}</p>
						</div>
					</div>	
					<div class="story_footer">    
						<a class="status-text label-task status-background-1 status-foreground-1" 
						href="#">
						{{ $issue->currentState }}
					</a>
					<span class="tasks-holder">
						<span>
							<a class="open-tasks-link show_tasks_link" href="#">
								Tareas
							</a>
						</span>
					</span> |
					<span class="comments-holder">
						<a class="comments-link" href="#">
							0&nbsp;Comments
						</a>
					</span> 
				</div>
			</div>
		</li>
		@endforeach
	</ul>
</div>
</div>
<script type="text/javascript">

$(document).ready(function() { 
	$("#summary").keypress(function() { 
		$("#story_details").show( "slow" );
	});	

	$(".add_category_link").click(function() {
		$( this ).css('display', 'none');
		$("#categoryid").css( "display","none" );
		$(".category_name").css( "display","block" ); 
	});
});
</script>