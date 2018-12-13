//https://laracasts.com/discuss/channels/general-discussion/step-by-step-guide-to-installing-socketio-and-broadcasting-events-with-laravel-51

var server = require('https').Server();

var io = require('socket.io')(server);

var Redis = require('ioredis');

var redis = new Redis();

// redis.psubscribe('*', function(err, count) {});
// redis.on('pmessage', function(subscribed, channel, message) {
//     console.log(channel);
//     message = JSON.parse(message);
//     io.emit(channel + ':' + message.event, message.data);
// });

redis.subscribe('communications');
redis.on('message', function(channel, message) {
	message = JSON.parse(message);
	if(message.event == 'NewRecipient'){
		console.log("new recipient "+message.data.userId);

		// channel_name.userid.socketid:typename
		io.emit(channel + '.' + message.data.userId + '.' + message.data.socketId + ':' + message.event, message.data);  
	}
	if(message.event == 'NewMessage'){
		console.log("new message for user "+message.data.userId);

		// channel_name.userid.socketid:typename
		io.emit(channel + '.' + message.data.userId + '.' + message.data.socketId + ':' + message.event, message.data);  
	}
	
});


server.listen(3000, function(){
    console.log('Listening on Port 3000');
});

redis.on("error", function (err) {
    console.log(err);
});