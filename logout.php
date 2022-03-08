<?php 

// This pagge is only used to logout a user, and in the process 
// avoid using javascript and ajax requests.

session_start();
session_unset();
session_destroy();
header('Location: login.php');

?>