<?php
session_start();
session_unset();
session_destroy();
header("Location: /ccs_sitin/login.php");
exit();
?>