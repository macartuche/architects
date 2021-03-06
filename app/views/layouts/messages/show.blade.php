    <div class="col-md-6">
        <h1>{{$thread->subject}}</h1>

        @foreach($thread->messages as $message)
            <div class="media">
                <a class="pull-left" href="/messages/{{$thread->id}}">
                    <img src="//www.gravatar.com/avatar/{{$message->user->email}}?s=64" alt="{{$message->user->name}}" class="img-circle">
                </a>
                <div class="media-body">
                    <h5 class="media-heading">{{$message->user->name}} {{$message->user->lastname}}</h5>
                    <p>{{$message->body}}</p>
                    <!--<div class="text-muted"><small>Publicado {{$message->created_at->diffForHumans()}}</small></div>-->
                </div>
            </div>
        @endforeach

        <h2>Nuevo mensaje</h2>
        {{Form::open(['route' => ['messages.update', $thread->id], 'method' => 'PUT'])}}
        <!-- Message Form Input -->
        <div class="form-group">
            {{ Form::textarea('message', null, ['class' => 'form-control']) }}
        </div>

        @if($users->count() > 0)
        <div class="form-group" style="overflow:hidden">
            @foreach($users as $user)
                <label style="float:left;" title="{{$user->name}} {{$user->lastname}}"><input type="checkbox" name="recipients[]" value="{{$user->id}}">{{$user->name}}</label>
            @endforeach
        </div>
        @endif

        <!-- Submit Form Input -->
        <div class="form-group">
            {{ HTML::link('messages/',  'Cancelar', array('class'=>"btn btn-danger btn-sm")  ) }} 
            {{ Form::submit('Enviar', ['class' => 'btn btn-primary form-control']) }}
        </div>
        {{Form::close()}}
    </div>

