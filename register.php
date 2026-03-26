<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f2f2f2;
        }
        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 400px;
        }
        form input, form button {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            box-sizing: border-box;
        }
        form button {
            background-color: green;
            color: white;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: darkgreen;
        }
        .message {
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>

<form action="process/register_process.php" method="POST">
    <h2 style="text-align:center;">Register</h2>
   <input type="text" name="id_number" placeholder="ID Number" required>
    <input type="text" name="last_name" placeholder="Last Name" required>
    <input type="text" name="first_name" placeholder="First Name" required>
    <input type="text" name="middle_name" placeholder="Middle Name">
    <input type="text" name="course" placeholder="Course" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="text" name="address" placeholder="Address">
    <button type="submit">Register</button>
</form>

</body>
</html>