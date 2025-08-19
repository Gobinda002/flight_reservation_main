<?php
session_start();

// Destroy all session data
session_destroy();

// Redirect back to index.php
header("Location: ../index.php");
exit();
?>
