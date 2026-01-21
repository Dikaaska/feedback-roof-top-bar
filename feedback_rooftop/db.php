<?php
$conn = mysqli_connect("localhost", "root", "", "feedback_rooftop");

if (!$conn) {
    die("DB Connection Failed");
}
