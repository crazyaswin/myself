<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin-login.html");
    exit;
}

$file = "messages.json";
$data = [];

if (file_exists($file)) {
    $data = json_decode(file_get_contents($file), true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>

    <style>
        body {
            font-family: Arial;
            background: #111;
            color: white;
            padding: 20px;
        }
        h2 { text-align: center; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #1b1b1b;
        }

        th, td {
            padding: 12px;
            border: 1px solid #333;
        }

        th {
            background: #222;
            cursor: pointer;
        }

        .logout {
            float: right;
            background: #ff4444;
            color: white;
            padding: 10px;
            text-decoration: none;
        }

        .search-box {
            width: 300px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .btn {
            padding: 10px 15px;
            background: #0f62fe;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
        }

        .delete-btn {
            background: #e11;
            padding: 7px 10px;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }
    </style>

    <script>
        function searchTable() {
            let input = document.getElementById("search").value.toLowerCase();
            let rows = document.querySelectorAll("table tbody tr");

            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(input) ? "" : "none";
            });
        }

        function deleteMessage(index) {
            if (!confirm("Are you sure you want to delete this message?")) return;

            fetch("delete.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "id=" + index
            })
            .then(() => location.reload());
        }

        function exportExcel() {
            window.location.href = "export.php";
        }
    </script>

</head>
<body>

<a href="logout.php" class="logout">Logout</a>
<h2>Admin Panel</h2>

<input type="text" id="search" class="search-box" placeholder="Search messages..." onkeyup="searchTable()">
<button class="btn" onclick="exportExcel()">Export to Excel</button>

<table>
    <thead>
        <tr>
            <th onclick="sortTable(0)">Name</th>
            <th onclick="sortTable(1)">Email</th>
            <th onclick="sortTable(2)">Message</th>
            <th onclick="sortTable(3)">Time</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>

    <?php foreach ($data as $index => $row): ?>
    <tr>
        <td><?= htmlspecialchars($row["name"]) ?></td>
        <td><?= htmlspecialchars($row["email"]) ?></td>
        <td><?= nl2br(htmlspecialchars($row["message"])) ?></td>
        <td><?= $row["time"] ?></td>
        <td><span class="delete-btn" onclick="deleteMessage(<?= $index ?>)">Delete</span></td>
    </tr>
    <?php endforeach; ?>

    </tbody>
</table>

<script>
function sortTable(n) {
    let table = document.querySelector("table");
    let switching = true, shouldSwitch, rows, x, y;
    let dir = "asc", switchcount = 0;

    while (switching) {
        switching = false;
        rows = table.rows;

        for (let i = 1; i < rows.length - 1; i++) {
            shouldSwitch = false;

            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];

            if (dir === "asc") {
                if (x.innerText.toLowerCase() > y.innerText.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            } else {
                if (x.innerText.toLowerCase() < y.innerText.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            }
        }

        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount++;
        }
        else if (switchcount === 0 && dir === "asc") {
            dir = "desc";
            switching = true;
        }
    }
}
</script>

</body>
</html>
