<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting...</title>
    <script>
        // Attempt to redirect to student_login.php
        window.location.href = "student/student_login.php";

        // If redirection fails (e.g., student_login.php does not exist), display an error message
        setTimeout(function() {
            // Check if the current page is still index.html
            if (window.location.pathname.includes("index.html")) {
                document.body.innerHTML = "<h1>Page Not Found</h1><p>The page you requested does not exist.</p>";
            }
        }, 5000); // Wait for 5 seconds before displaying the error
    </script>
</head>
<body>
    <p>If you are not redirected automatically, <a href="student_login.php">click here</a>.</p>
</body>
</html>
<?php
// Assuming you have a list of valid routes defined somewhere in your application
$validRoutes = ['/home', '/about', '/contact'];

// Get the requested URL
$requestedUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Check if the requested URL is among the valid routes
if (!in_array($requestedUrl, $validRoutes)) {
    // Display the custom error page
    include('404error.php');
    exit; // Stop further execution
}

// Handle other routes and page logic here...

?>