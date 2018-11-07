var server = require('http').Server();

var io = require('socket.io')(server);

var Redis = require('ioredis');

var redis = new Redis();

redis.subscribe('test-channel');

redis.on('message', function(channel, message) {
	
	message = JSON.parse(message);

	console.log(message.data.username);
	//
	 
	io.emit(channel + ':' + message.event, message.data); //test-channer:UserSignedUp

});

server.listen(3000);