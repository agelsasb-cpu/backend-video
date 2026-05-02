<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "tugas-231044");

if ($conn->connect_error) {
    echo json_encode(["status" => "error"]);
    exit();
}

$id = $_POST['id'];

// ambil data dulu
$q = $conn->query("SELECT * FROM youtube WHERE id='$id'");
$data = $q->fetch_assoc();

// hapus file
if ($data) {
    if (file_exists("thumbnail/" . $data['thumbnail'])) {
        unlink("thumbnail/" . $data['thumbnail']);
    }

    if (file_exists("video/" . $data['video'])) {
        unlink("video/" . $data['video']);
    }

    // hapus dari database
    $conn->query("DELETE FROM youtube WHERE id='$id'");
}

echo json_encode(["status" => "success"]);
?>