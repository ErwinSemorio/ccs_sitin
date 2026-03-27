<?php
session_start();        // ✅ Starts the session
session_unset();        // ✅ Clears all session variables
session_destroy();      // ✅ Destroys the session
header("Location: /ccs_sitin/login.php");  // ✅ Redirects to login
exit();                 // ✅ Stops script execution
?>