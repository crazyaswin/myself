<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=messages.xls");

$file = "messages.json";
$data = json_decode(file_get_contents($file), true);

echo "Name\tEmail\tMessage\tTime\n";

foreach ($data as $row) {
    echo $row["name"] . "\t" . $row["email"] . "\t" . str_replace("\n", " ", $row["message"]) . "\t" . $row["time"] . "\n";
}
?>
