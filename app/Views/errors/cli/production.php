<?php
// On the CLI, it's better to enable error reporting
// instead of just using the exception template.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the error handling file
include __DIR__ . '/error_exception.php';
