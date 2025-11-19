<?php
$pass = $_POST["pass"];

if ($pass === "aswinvs2003") {
    session_start();
    $_SESSION["admin"] = true;
    header("Location: admin-panel.php");
} else {
    echo "Incorrect Password!";
}
?>
