//https://laracasts.com/discuss/channels/general-discussion/step-by-step-guide-to-installing-socketio-and-broadcasting-events-with-laravel-51
//
var server = require('http').Server();

var io = require('socket.io')(server);

var Redis = require('ioredis');

var redis = new Redis();
redis.subscribe('test-channel');
redis.on('message', function(channel, message) {
	message = JSON.parse(message);
	console.log(message.data.username);
	io.emit(channel + ':' + message.event, message.data); //test-channer:UserSignedUp
});

var redis2 = new Redis();
redis2.subscribe('communications');
redis2.on('message', function(channel, message) {
	message = JSON.parse(message);
	if(message.event == 'NewMessage'){
		io.emit(channel + ':' + message.event, message.data);  // communications:NewMessage
	}
	
});

//server.listen(3000);
server.listen(3000, function(){
    console.log('Listening on Port 3000');
});