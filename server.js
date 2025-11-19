const express = require("express");
const fs = require("fs");
const XLSX = require("xlsx");
const cors = require("cors");

const app = express();
app.use(express.json());
app.use(cors());

const FILE = "submissions.xlsx";

// Ensure Excel file exists
function ensureExcelFile() {
    if (!fs.existsSync(FILE)) {
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet([]);
        XLSX.utils.book_append_sheet(wb, ws, "Contacts");
        XLSX.writeFile(wb, FILE);
    }
}
ensureExcelFile();

app.post("/submit", (req, res) => {
    try {
        const { name, email, message } = req.body;

        const wb = XLSX.readFile(FILE);
        const ws = wb.Sheets["Contacts"];

        const data = XLSX.utils.sheet_to_json(ws);
        data.push({
            Name: name,
            Email: email,
            Message: message,
            Time: new Date().toLocaleString()
        });

        const newWS = XLSX.utils.json_to_sheet(data);
        wb.Sheets["Contacts"] = newWS;
        XLSX.writeFile(wb, FILE);

        res.send("Message saved successfully ✔️");
    } catch (err) {
        console.error(err);
        res.status(500).send("Failed to save message");
    }
});

app.listen(5000, () => console.log("Server running on http://localhost:5000"));

/*

command to git push:
git add server.js
git commit -m "Implement contact form submission handling with Excel storage"
git push origin main
 */