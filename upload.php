<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$conn = new mysqli("localhost", "root", "", "tugas-231044");

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi gagal"
    ]);
    exit();
}

if (!isset($_POST['title'])) {
    $_POST['title'] = "Tanpa Judul";
}

if (!isset($_FILES['thumbnail']) || !isset($_FILES['video'])) {
    echo json_encode([
        "status" => "error",
        "message" => "File tidak lengkap"
    ]);
    exit();
}

// ================= FOLDER =================
$thumbnailDir = "thumbnail/";
$videoDir     = "video/";

if (!is_dir($thumbnailDir)) mkdir($thumbnailDir, 0777, true);
if (!is_dir($videoDir)) mkdir($videoDir, 0777, true);

// ================= FUNGSI RENAME =================
function generateUniqueName($dir, $filename) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $name = pathinfo($filename, PATHINFO_FILENAME);

    $newName = $filename;
    $i = 1;

    while (file_exists($dir . $newName)) {
        $newName = $name . "($i)." . $ext;
        $i++;
    }

    return $newName;
}

// ================= NAMA FILE =================
$thumbnailName = generateUniqueName($thumbnailDir, basename($_FILES['thumbnail']['name']));
$videoName     = generateUniqueName($videoDir, basename($_FILES['video']['name']));

// ================= PATH =================
$thumbnailPath = $thumbnailDir . $thumbnailName;
$videoPath     = $videoDir . $videoName;

// ================= UPLOAD =================
$uploadThumb = move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnailPath);
$uploadVideo = move_uploaded_file($_FILES['video']['tmp_name'], $videoPath);

if (!$uploadThumb || !$uploadVideo) {
    echo json_encode([
        "status" => "error",
        "message" => "Upload gagal"
    ]);
    exit();
}

// ================= SIMPAN DB =================
$stmt = $conn->prepare("INSERT INTO youtube (title, thumbnail, video) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $_POST['title'], $thumbnailName, $videoName);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Upload berhasil"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>