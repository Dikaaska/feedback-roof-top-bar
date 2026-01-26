<?php
include 'db.php';

$guest = $_POST['guest_name'] ?: null;
$room  = $_POST['room_number'] ?: null;

$swimming = isset($_POST['swimming']) ? (int)$_POST['swimming'] : 0;
$food     = isset($_POST['food']) ? (int)$_POST['food'] : 0;
$beverage = isset($_POST['beverage']) ? (int)$_POST['beverage'] : 0;
$wifi     = isset($_POST['wifi']) ? (int)$_POST['wifi'] : 0;
$music    = isset($_POST['music']) ? (int)$_POST['music'] : 0;

$note = $_POST['note'] ?: null;





$average = round(($swimming+$food+$beverage+$wifi+$music)/5,1);

$scores = [$swimming,$food,$beverage,$wifi,$music];

foreach($scores as $s){
    if($s < 1 || $s > 10){
        die('Invalid feedback data');
    }
}



$stmt = $conn->prepare("
INSERT INTO feedback_rooftop_table
(guest_name, room_number, swimming, food, beverage, wifi, music, average, note)
VALUES (?,?,?,?,?,?,?,?,?)
");

$stmt->bind_param(
    "ssiiiiids",
    $guest,$room,$swimming,$food,$beverage,$wifi,$music,$average,$note
);

$stmt->execute();
header("Location: thankyou.php");
