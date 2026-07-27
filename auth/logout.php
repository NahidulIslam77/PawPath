<?php
session_start();




// clear session variables
$_SESSION = [];

// destroy session
session_destroy();

// delete session cookie (optional but correct)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// ✅ FIXED ABSOLUTE PATH
header("Location: /PawPath/auth/login.php");
exit();
?>