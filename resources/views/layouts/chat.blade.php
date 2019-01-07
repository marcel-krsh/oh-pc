<!-- resources/views/chat.blade.php -->

@extends('layouts.app')

@section('content')
<script>
        // initial values
   
    var uid = "{{Auth::user()->id}}";
    var sid = "{{Auth::user()->socket_id}}";
</script>
<div class="container" id="chatapp">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Chats</div>

                <div class="panel-body">
                    <chat-messages :messages="messages"></chat-messages>
                </div>
                <div class="panel-footer">
                    <chat-form
                        v-on:messagesent="addMessage"
                        :user="{{ Auth::user() }}"
                    ></chat-form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
new Vue({
    el: '#chatapp',

    data: {
        messages: []
    },

    created() {

        this.fetchMessages();

        // Echo.join('chat');
        Echo.channel('chat.'+uid+'.'+sid)
              .listen('MessageSent', (e) => {
                this.messages.push({
                  message: e.message.message,
                  user: e.user
                });
            console.log("receiving message");
            console.log(e.user);
              });
    },

    methods: {
        fetchMessages() {
            axios.get('/chat/messages').then(response => {
                this.messages = response.data;
            });
        },

        addMessage(message) {
            this.messages.push(message);

            axios.post('/chat/messages', message).then(response => {
              console.log(response.data);
            });
        }
    }
});
</script>
@endsection