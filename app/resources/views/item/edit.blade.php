<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>物品編集</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body class="antialiased">
        <p>現在の物品名：{{ $item -> name}}</p>
        <div>
            <form method="POST" action="/items/{{ $item->id }}">
                @csrf
                変更後の物品名<input type="text" name="name">
        </br>
                <input type="submit">
            </form>
        </div>
    </body>
</html>
