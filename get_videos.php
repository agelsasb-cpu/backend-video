<?php
// ================= CORS =================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// ================= HANDLE PRE-FLIGHT REQUEST =================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ================= DATABASE =================
$conn = new mysqli("localhost", "root", "", "tugas-231044");

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database gagal"
    ]);
    exit();
}

// ================= QUERY (🔥 TERBARU DI ATAS) =================
$result = $conn->query("SELECT * FROM youtube ORDER BY id DESC");

$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// ================= OUTPUT =================
echo json_encode($data);
?>