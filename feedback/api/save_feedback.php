<?php
header("Content-Type: text/plain");

include "../config/database.php";

/* AMAN: tidak ada NOTICE */
$guest_name  = $_POST['guest_name']  ?? '';
$room_number = $_POST['room_number'] ?? '';
$food  = $_POST['food']  ?? '';
$pool  = $_POST['pool']  ?? '';
$music = $_POST['music'] ?? '';
$wifi  = $_POST['wifi'] ?? '';

if (!$food || !$pool || !$music || !$wifi) {
    http_response_code(400);
    echo "INCOMPLETE";
    exit;
}

$stmt = $conn->prepare(
  "INSERT INTO feedback
  (guest_name, room_number, food, pool, music, wifi)
  VALUES (?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    http_response_code(500);
    echo "PREPARE FAILED";
    exit;
}

$stmt->bind_param(
  "ssssss",
  $guest_name,
  $room_number,
  $food,
  $pool,
  $music,
  $wifi
);

if ($stmt->execute()) {
    echo "OK";
} else {
    http_response_code(500);
    echo "DB ERROR";
}
