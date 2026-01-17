<?php
include "../config/database.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Roof_Top_Feedback_Report.xls");

/* FILTER */
$from  = $_GET['from'] ?? date('Y-m-01');
$to    = $_GET['to'] ?? date('Y-m-d');

/* QUERY */
$sql = "SELECT * FROM feedback 
        WHERE created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
        ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

/* FUNCTION WARNA */
function cellColor($val) {
    if ($val == 'Good') return '#2ECC71';
    if ($val == 'Average') return '#F1C40F';
    if ($val == 'Bad') return '#E74C3C';
    return '#ffffff';
}
?>

<!-- TITLE -->
<table>
<tr>
    <td colspan="7" style="
        color:black;
        text-align:center;
        font-size:18px;
        font-weight:bold;
        padding:15px;
    ">
        Roof Top Pool Bar Feedback Report
    </td>
</tr>
<tr>
    <td colspan="7" style="
        text-align:center;
        font-size:12px;
        font-weight:bold;
        padding:8px;
    ">
        Period: <?= $from ?> to <?= $to ?>
    </td>
</tr>
</table>

<br>

<!-- TABLE DATA -->
<table border="1" cellpadding="8" cellspacing="0" width="100%">
<tr style="
    background:#004990;
    color:white;
    font-weight:bold;
    text-align:center;
">
    <th>Date</th>
    <th>Guest Name</th>
    <th>Room</th>
    <th>Food</th>
    <th>Pool</th>
    <th>Music</th>
    <th>WiFi</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $row['created_at'] ?></td>
    <td><?= $row['guest_name'] ?: '-' ?></td>
    <td><?= $row['room_number'] ?: '-' ?></td>

    <td style="background:<?= cellColor($row['food']) ?>; text-align:center;">
        <?= $row['food'] ?>
    </td>
    <td style="background:<?= cellColor($row['pool']) ?>; text-align:center;">
        <?= $row['pool'] ?>
    </td>
    <td style="background:<?= cellColor($row['music']) ?>; text-align:center;">
        <?= $row['music'] ?>
    </td>
    <td style="background:<?= cellColor($row['wifi']) ?>; text-align:center;">
        <?= $row['wifi'] ?>
    </td>
</tr>
<?php endwhile; ?>

</table>
