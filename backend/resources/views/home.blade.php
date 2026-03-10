<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL-designer</title>
    @vite(['src/css/app.css'])
</head>
<body>
<header class="header">
    <img class="logo" src="{{ Vite::asset('src/icons/logo.svg') }}" alt="sql-designer">
    <div class="flex-items">
        @if(Auth::check())
            <a class="btn btn-secondary" href="/diagrams">View Diagrams</a>
            <a class="btn btn-secondary" href="/logout">Logout</a>
        @else
            <a class="btn btn-secondary" href="/register">Register</a>
            <a class="btn btn-secondary" href="/login">Login</a>
        @endif
    </div>
</header>

<div class="centered-container text-center">
    <div class="text-center">
        <h2>Design, Visualize, and Manage Your MySQL Database Schemas</h2>
        <p>Create, edit, and manage your MySQL schemas with our easy-to-use graphical interface.</p>
        <p>Start designing your database schemas and streamline your database development process.</p>
    </div>
</div>

</body>
</html>
