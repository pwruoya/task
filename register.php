<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <form method="POST" action="register_action.php">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        Role: 
        <select name="role">
            <option value="intern">Intern</option>
            <option value="supervisor">Supervisor</option>
        </select><br>
        <input type="submit" value="Register">
        <br>
        <a href="login.php">Already have an Account?</a>
    </form>
</body>
</html>
