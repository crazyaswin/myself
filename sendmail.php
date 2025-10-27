<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "madoften0@gmail.com";  // Receiver email
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $message = htmlspecialchars($_POST["message"]);

    $subject = "New Message from Elite Platform";
    $body = "You received a new message:\n\nName: $name\nEmail: $email\n\nMessage:\n$message";
    $headers = "From: $email";

    if (mail($to, $subject, $body, $headers)) {
        echo "Message sent successfully!";
    } else {
        echo "Sorry, message not sent. Check server email settings.";
    }
}
?>
