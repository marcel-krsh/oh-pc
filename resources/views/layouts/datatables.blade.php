<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel DataTables Tutorial</title>
        <script src="https://js.pusher.com/4.3/pusher.min.js{{ asset_version() }}"></script>
          <script>

            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = true;

            var pusher = new Pusher('6e69117f494c249535b6', {
              cluster: 'us2',
              forceTLS: true
            });

            var channel = pusher.subscribe('my-channel');
            channel.bind('my-event', function(data) {
              alert(JSON.stringify(data));
            });
          </script>
        <!-- Bootstrap CSS -->
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css{{ asset_version() }}" rel="stylesheet">
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css{{ asset_version() }}">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js{{ asset_version() }}"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js{{ asset_version() }}"></script>
        <![endif]-->
        <style>
            body {
                padding-top: 40px;
            }
        </style>
    </head>
    <body>
        <h1>Pusher Test</h1>
          <p>
            Try publishing an event to channel <code>my-channel</code>
            with event name <code>my-event</code>.
          </p>
        <div class="container">
            @yield('content')
        </div>

        <!-- jQuery -->
        <script src="//code.jquery.com/jquery.js{{ asset_version() }}"></script>
        <!-- DataTables -->
        <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js{{ asset_version() }}"></script>
        <!-- Bootstrap JavaScript -->
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js{{ asset_version() }}"></script>
        <!-- App scripts -->
        @stack('scripts')
    </body>
</html>
