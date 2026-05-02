<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "tugas-231044");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB gagal"]);
    exit();
}

$id = $_POST['id'] ?? null;
$title = $_POST['title'] ?? "Tanpa Judul";

if (!$id) {
    echo json_encode(["status" => "error", "message" => "ID kosong"]);
    exit();
}

// ================= AMBIL DATA LAMA =================
$q = $conn->query("SELECT * FROM youtube WHERE id='$id'");
$old = $q->fetch_assoc();

if (!$old) {
    echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"]);
    exit();
}

$thumbnailDir = "thumbnail/";
$videoDir     = "video/";

$thumbnailName = $old['thumbnail'];
$videoName     = $old['video'];

// ================= UPDATE THUMBNAIL =================
if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['name'] != "") {

    $newThumb = time() . "_" . basename($_FILES['thumbnail']['name']);
    $uploadPath = $thumbnailDir . $newThumb;

    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $uploadPath)) {

        if (file_exists($thumbnailDir . $old['thumbnail'])) {
            unlink($thumbnailDir . $old['thumbnail']);
        }

        $thumbnailName = $newThumb;
    }
}

// ================= UPDATE VIDEO =================
if (isset($_FILES['video']) && $_FILES['video']['name'] != "") {

    $newVideo = time() . "_" . basename($_FILES['video']['name']);
    $uploadPath = $videoDir . $newVideo;

    if (move_uploaded_file($_FILES['video']['tmp_name'], $uploadPath)) {

        if (file_exists($videoDir . $old['video'])) {
            unlink($videoDir . $old['video']);
        }

        $videoName = $newVideo;
    }
}

// ================= UPDATE DATABASE =================
$update = $conn->query("UPDATE youtube SET 
    title='$title',
    thumbnail='$thumbnailName',
    video='$videoName'
    WHERE id='$id'
");

if ($update) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}
?>