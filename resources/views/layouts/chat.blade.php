<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .list-group{
            overflow-y: scroll;
            height: 200px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row" id="app">
            <div class="offset-4 col-4 offset-sm-1 col-sm-10">
                <li class="list-group-item active">Chat Room <span class="badge  badge-pill badge-danger">@{{ numberOfUsers }}</span> </li>
                <div class="badge badge-pill badge-primary">@{{ typing }}</div>
                <ul class="list-group" v-chat-scroll>
                  <message
                  v-for="value,index in chat.message"
                  :key=value.index
                  :color= chat.color[index]
                  :user = chat.user[index]
                  :time = chat.time[index]
                  >
                    @{{ value }}
                  </message>
                </ul>
                  <input type="text" class="form-control" placeholder="Type your message here..." v-model='message' @keyup.enter='send'>
                  <br>
                  <a href='' class="btn btn-warning btn-sm" @click.prevent='deleteSession'>Delete Chats</a>
            </div>
        </div>
    </div>

    <script >
      const app = new Vue({
    el: '#app',
    data:{
      message:'',
      chat:{
        message:[],
        user:[],
        color:[],
        time:[]
      },
      typing:'',
      numberOfUsers:0
    },
    watch:{
      message(){
        Echo.private('chat')
            .whisper('typing', {
                name: this.message
            });
      }
    },
    methods:{
      send(){
        
        if (this.message.length != 0) {
          console.log(this.message);
          //console.log(this.chat);
          

          this.chat.message.push(this.message);
          this.chat.color.push('success');
          this.chat.user.push('Me');
          this.chat.time.push(this.getTime());

          
          axios.post('/send', {
            message : this.message,
                    chat:this.chat
            })
            .then(response => {
              console.log(response);
              this.message = ''
            })
            .catch(error => {
              console.log(error);
            });
        }
      },
      getTime(){
        let time = new Date();
        return time.getHours()+':'+time.getMinutes();
      },
        getOldMessages(){
            axios.post('/getOldMessage')
                  .then(response => {
                    console.log(response);
                    if (response.data != '' && response.data != '    ') {
                        this.chat = response.data;
                        console.log('Loaded old chats');
                    }
                  })
                  .catch(error => {
                    console.log(error);
                  });
        },
        deleteSession(){
            axios.post('/deleteSession')
            .then(response=> this.$toaster.success('I deleted your chat history.') );
        }
    },
    mounted(){
        this.getOldMessages();
      Echo.private('chat')
          .listen('ChatEvent', (e) => {
            this.chat.message.push(e.message);
            this.chat.user.push(e.user);
            this.chat.color.push('warning');
            this.chat.time.push(this.getTime());
                axios.post('/saveToSession',{
                    chat : this.chat
                })
                      .then(response => {
                      })
                      .catch(error => {
                        console.log(error);
                      });
              // console.log(e);
          })
          .listenForWhisper('typing', (e) => {
            if (e.name != '') {
                this.typing = 'thinking...'
            }else{
              this.typing = ''
            }
          })

          Echo.join(`chat`)
              .here((users) => {
                this.numberOfUsers = users.length;
              })
              .joining((user) => {
                this.numberOfUsers += 1;
                // console.log(user);
                this.$toaster.success(user.name+' has joined the chat room');
              })
              .leaving((user) => {
                this.numberOfUsers -= 1;
                this.$toaster.warning(user.name+' has left the chat room');
              });
    }
});

    </script>
</body>
</html>