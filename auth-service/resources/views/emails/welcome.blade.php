<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
</head>
<body>
    <h2>Welcome to the site {{ $name }}</h2>
    <p>Please verify your email address to activate your account.</p>
    <p>Your email is: {{ $email }}</p>
    "Click here to verify your email: <a href='{{ url("$verification_link") }}'>Verify Email</a>"
    <p>Thank you</p>
</body>
</html>
