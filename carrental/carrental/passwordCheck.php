<?php
require_once("includes/config.php");

// Check if the passwords match
if (!empty($_POST["password1"]) && !empty($_POST["password2"])) {
    $password1 = $_POST["password"];
    $password2 = $_POST["confirmpassword"];

    if ($password1 === $password2) {
        echo "<span style='color:green'>Passwords match!</span>";
    } else {
        echo "<span style='color:red'>Passwords do not match!</span>";
    }
}
?>
