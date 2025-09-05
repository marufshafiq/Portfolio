<?php
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $message = htmlspecialchars($_POST["message"]);
    $date = date("Y-m-d H:i:s");

    $entry = "$date | $name | $email | $message\n";
    file_put_contents("messages.txt", $entry, FILE_APPEND);

    header("Location: thankyou.html");
    exit();
}
?>