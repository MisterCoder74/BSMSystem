<?php
ob_start();
// Prevent direct access to includes directory
header("HTTP/1.0 403 Forbidden");
die("Access Forbidden");
?>
