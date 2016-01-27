<?php>
<html>
    <head>
        <title>View articles</title>
    </head>
    <body>
        <ul>
            @foreach($articles as $article)
            <li>Name : {{$article->name}} | Author : {{$article->author}} | {{$article->created_at}}</li>
            @endforeach
        </ul>
    </body>
</html>
