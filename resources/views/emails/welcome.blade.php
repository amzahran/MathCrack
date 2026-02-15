<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>Welcome {{ $user->firstname ?? $user->name }}</h2>

    <p>
        Your account has been created successfully.
    </p>

    <p>
        Thank you for joining us.
    </p>
</body>
</html>
