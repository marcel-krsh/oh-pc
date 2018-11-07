<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Ohio Housing Finance Agency - Blight Management</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>
            window.Laravel = { csrfToken: '{{ csrf_token() }}' }
        </script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.17/vue.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>

    </head>
    <body>
        <div id="app">
            <h1>New Users</h1>
            <example></example>
        
           
            <div>
                <ul id="names">
                    <li v-for="user in users">@{{ user }}</li>
                </ul>
            </div>
        </div>

        <script  src="{{ mix('js/app.js') }}"></script>

        <script>
          //  var socket = io('http://192.168.100.100:3000');
            // load all components
          //  Vue.component('example', require('./components/Example.vue')); 

          //  new Vue({
          //      el: '#app',

            //   data: {
            //         users: [ 'JohnDoe' ]
            //     },

            //     ready: function() {
            //         socket.on('test-channel:UserSignedUp', function(data){
            //             console.log("socket message received");
            //             this.users.push(data.username);
            //         }.bind(this));
            //     }
            // });

        </script>
    </body>
</html>
