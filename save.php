<?php
header("Content-Type: application/json");

// -------------- SECURITY & LIMITS --------------
$MAX_FILE_SIZE_MB = 5; // Prevent huge messages.json
$RATE_LIMIT_SECONDS = 10; // Prevent spam (10 sec per IP)
$DATA_FILE = "messages.json";
$RATE_FILE = "rate_limit.json";

// -------------- READ JSON INPUT --------------
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["name"]) || !isset($data["email"]) || !isset($data["message"])) {
    echo json_encode(["status" => "error", "msg" => "Invalid input"]);
    exit;
}

// -------------- SANITIZATION --------------
$name = htmlspecialchars(trim($data["name"]));
$email = filter_var(trim($data["email"]), FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars(trim($data["message"]));

if ($name === "" || $email === "" || $message === "") {
    echo json_encode(["status" => "error", "msg" => "All fields required"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "msg" => "Invalid email format"]);
    exit;
}

// -------------- RATE LIMITING (ANTI-SPAM) --------------
$ip = $_SERVER["REMOTE_ADDR"];

if (!file_exists($RATE_FILE)) file_put_contents($RATE_FILE, json_encode([]));

$rateData = json_decode(file_get_contents($RATE_FILE), true);

$currentTime = time();

if (isset($rateData[$ip]) && ($currentTime - $rateData[$ip]) < $RATE_LIMIT_SECONDS) {
    echo json_encode(["status" => "error", "msg" => "Wait before sending again"]);
    exit;
}

// Update rate limit time
$rateData[$ip] = $currentTime;
file_put_contents($RATE_FILE, json_encode($rateData, JSON_PRETTY_PRINT));


// -------------- CHECK FILE SIZE BEFORE WRITING --------------
if (file_exists($DATA_FILE)) {
    $sizeMB = filesize($DATA_FILE) / (1024 * 1024);
    if ($sizeMB > $MAX_FILE_SIZE_MB) {
        echo json_encode(["status" => "error", "msg" => "Storage limit reached"]);
        exit;
    }
} else {
    file_put_contents($DATA_FILE, json_encode([])); // create new
}

// -------------- CREATE NEW ENTRY --------------
$entry = [
    "name" => $name,
    "email" => $email,
    "message" => $message,
    "ip" => $ip,
    "user_agent" => $_SERVER["HTTP_USER_AGENT"] ?? "Unknown",
    "time" => date("Y-m-d H:i:s")
];

// -------------- SAVE WITH FILE LOCK (AVOIDS CORRUPTION) --------------
$existing = json_decode(file_get_contents($DATA_FILE), true);
$existing[] = $entry;

$fp = fopen($DATA_FILE, "w");
flock($fp, LOCK_EX); // lock file
fwrite($fp, json_encode($existing, JSON_PRETTY_PRINT));
flock($fp, LOCK_UN);
fclose($fp);

// -------------- FINAL RESPONSE --------------
echo json_encode(["status" => "success", "msg" => "Message saved successfully!"]);
?>
