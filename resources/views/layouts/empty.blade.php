<!DOCTYPE html>
<html lang="en" dir="ltr" id="parentHTML" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Empty Layout</title>
    @yield('head')
</head>
<body class="uk-height-1-1">

@yield('header')

<div class="uk-vertical-align uk-text-center uk-height-1-1">
    <div class="uk-vertical-align-middle uk-margin-top">

        @yield('content')

    </div>
</div>
</body>
</html>
