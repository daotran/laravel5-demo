
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Create a new Article</title>
        <link rel="stylesheet" href="{{asset('//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css')}}">
    </head>
    <body>
        <div class="container">
            <h1>Add more Article</h1>
            <div class="secure">Create a new article</div>
            {!! Form::open(array('url' => 'articles/store', 'method' => 'POST')) !!}
            <div class="control-group">
                <div class="controls">
                    {!! Form::label('name', "Name:") !!}
                    {!! Form::text('name','',array('id'=>'','class'=>'form-control span6','placeholder' => 'Please Enter Article Name')) !!}
                    @if ($errors->has('name'))<p style="color:red;">{!!$errors->first('name')!!}</p>@endif
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    {!! Form::label('author', "Author:") !!}
                    {!! Form::text('author','',array('id'=>'','class'=>'form-control span6','placeholder' => 'Please Enter Author Name')) !!}
                    @if ($errors->has('name'))<p style="color:red;">{!!$errors->first('author')!!}</p>@endif
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    {!! Form::label('created_at', 'Created Date') !!}
                    {!! Form::input('date', 'created_at', date("Y-m-d"), array('id' => '', 'class' => 'form-control span6', 'placeholder' => 'Please Enter Created Date')) !!}
                    @if ($errors->has('created_at'))<p style="color: red;">{!! $errors->first('created_at') !!}</p>@endif
                </div>
            </div>

            <div class="success"></div><br>
            {!! Form::submit('Add New') !!}

            {!! Form::close() !!}
        </div>
    </body>
</html>