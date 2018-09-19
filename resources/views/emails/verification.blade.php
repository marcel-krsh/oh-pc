<html>
    <head></head>
    <body>
        Please verify your email address by clicking the Verify link below<br>
	    <a href="{{ url('register/verify/'.$user->email_token) }}">Verify</a>
    </body>
</html>