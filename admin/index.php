<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting...</title>
    <script>
        window.location.href = "eval/404error.php";
        setTimeout(function() {

            if (window.location.pathname.includes("index.html")) {
                document.body.innerHTML = "<h1>Page Not Found</h1><p>The page you requested does not exist.</p>";
            }
        }, 5000);
    </script>
</head>
<body>
    <p>If you are not redirected automatically, <a href="student_login.php">click here</a>.</p>
</body>
</html>
<?php

$validRoutes = ['/home', '/about', '/contact'];
$requestedUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (!in_array($requestedUrl, $validRoutes)) {
    include('404error.php');
    exit; 
}


?>