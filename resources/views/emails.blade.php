<!-- resources/views/emails.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password Email</title>
</head>

<body>
    <h1>Forget Password Email</h1>
    <p>You have requested to reset your password. Please click the link below:</p>
    <a href="{{ $data['url'] }}">Reset Password</a>
</body>

</html>
