
<!DOCTYPE html>
<html lang="en">

<body>
	
<p>Dear {{ $name }}</p>
<p>Your account has been created, please verify your account by clicking this link</p>
<p><a href="{{ route('verify',$email_verification_token) }}">
	{{ route('verify',$email_verification_token) }}
</a></p>

<p>Thanks</p>

</body>

</html> 