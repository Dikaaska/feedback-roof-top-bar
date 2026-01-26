<?php
$conn = mysqli_connect("localhost", "root", "", "rooftop");

if (!$conn) {
    die("DB Connection Failed");
}
