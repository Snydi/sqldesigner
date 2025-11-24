<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="yandex-verification" content="f1dc63d6385660f1"/>
    <title>Snydiagram</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
</head>
<body>
<header class="header">
    <img class="logo" src="/logo.png" alt="logo">
    <div class="flex-items">
        <?php if(Auth::check()): ?>
            <a class="btn btn-secondary" href="/diagrams">View Diagrams</a>
            <a class="btn btn-secondary" href="/logout">Logout</a>
        <?php else: ?>
            <a class="btn btn-secondary" href="/register">Register</a>
            <a class="btn btn-secondary" href="/login">Login</a>
        <?php endif; ?>
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
<?php /**PATH /var/www/html/resources/views/home.blade.php ENDPATH**/ ?>