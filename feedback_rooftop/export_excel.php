<?php
include 'db.php';

$from = $_GET['from']; 
$to = $_GET['to'];

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Rooftop_Feedback.xls");

echo "<style>
/* Warna rating */
.red {background:#f87171}
.yellow {background:#fde047}
.green {background:#4ade80}

/* Header & period */
.header-title {font-size:18px; font-weight:bold; text-align:center; border:none;}
.header-period {font-size:14px; font-weight:bold; text-align:center; border:none;}

/* Table report */
table {border-collapse: collapse;}
table th, table td {padding:5px;}
</style>";

/* Header */
echo "<table>
<tr><td colspan='9' class='header-title'>Roof Top Pool Bar Feedback Report</td></tr>
<tr><td colspan='9' class='header-period'>Period: $from to $to</td></tr>
<br>
</table>";

/* Table report */
echo "<table border='1'>
<tr>
<th>Date</th><th>Guest</th><th>Room</th>
<th>Swimming</th><th>Food</th><th>Beverage</th><th>WiFi</th><th>Music</th><th>Avg</th>
</tr>";

$q = mysqli_query($conn, "
SELECT * FROM feedback
WHERE created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
");

function cell($v){
 if($v<=4) return "<td class='red'>$v</td>";
 if($v<=7) return "<td class='yellow'>$v</td>";
 return "<td class='green'>$v</td>";
}

// Total per kategori
$totalSwimming = 0;
$totalFood = 0;
$totalBeverage = 0;
$totalWifi = 0;
$totalMusic = 0;
$totalAverage = 0;
$rowCount = 0;

while($r = mysqli_fetch_assoc($q)){
    echo "<tr>
    <td>{$r['created_at']}</td>
    <td>{$r['guest_name']}</td>
    <td>{$r['room_number']}</td>
    ".cell($r['swimming'])."
    ".cell($r['food'])."
    ".cell($r['beverage'])."
    ".cell($r['wifi'])."
    ".cell($r['music'])."
    <td><b>{$r['average']}</b></td>
    </tr>";

    $totalSwimming += $r['swimming'];
    $totalFood += $r['food'];
    $totalBeverage += $r['beverage'];
    $totalWifi += $r['wifi'];
    $totalMusic += $r['music'];
    $totalAverage += $r['average'];
    $rowCount++;
}

// Hitung rata-rata per kategori
$avgSwimming = $rowCount > 0 ? round($totalSwimming / $rowCount, 2) : 0;
$avgFood = $rowCount > 0 ? round($totalFood / $rowCount, 2) : 0;
$avgBeverage = $rowCount > 0 ? round($totalBeverage / $rowCount, 2) : 0;
$avgWifi = $rowCount > 0 ? round($totalWifi / $rowCount, 2) : 0;
$avgMusic = $rowCount > 0 ? round($totalMusic / $rowCount, 2) : 0;
$grandAverage = $rowCount > 0 ? round($totalAverage / $rowCount, 2) : 0;

// Row total average kategori
echo "<tr>
<td colspan='3' style='text-align:right; font-weight:bold;'>Total Average</td>
".cell($avgSwimming)."
".cell($avgFood)."
".cell($avgBeverage)."
".cell($avgWifi)."
".cell($avgMusic)."
<td><b>$grandAverage</b></td>
</tr>";

echo "</table>";
?>
