<?php
include 'db.php';

$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to'] ?? date('Y-m-d');

/* === QUERY AVG PER KATEGORI === */
$avg = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT 
        AVG(swimming) AS swimming,
        AVG(food) AS food,
        AVG(beverage) AS beverage,
        AVG(wifi) AS wifi,
        AVG(music) AS music
    FROM feedback
    WHERE created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
"));
$overall = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT 
        AVG(average) AS overall_avg,
        COUNT(*) AS total
    FROM feedback
    WHERE created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
"));

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Feedback</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<style>
body{
    background:linear-gradient(135deg,#0b1f29,#1f3b4d);
    font-family:Arial;
    color:#fff;
    padding:40px;
    min-height:100vh;
}

.container{
    max-width:1200px;
    margin:auto;
    background:#ffffff;
    color:#0b1f29;
    padding:40px;
    border-radius:24px;
    box-shadow:0 20px 60px rgba(0,0,0,.35);
}

h1,h2{text-align:center}

.filter{
    display:flex;
    gap:12px;
    margin-bottom:30px;
}
.filter input, .filter button, .filter a{
    padding:12px 16px;
    border-radius:12px;
    border:none;
}
.filter input[type="date"]{
    background:#f8fafc;
    color:#0b1f29;
    border:1px solid #cbd5e1;
    border-radius:12px;
    padding:10px 14px;
}

.filter input[type="date"]::-webkit-calendar-picker-indicator{
    filter:invert(0.3);
    cursor:pointer;
}

.filter input[type="date"]:focus{
    outline:none;
    border-color:#a855f7;
    box-shadow:0 0 0 3px rgba(168,85,247,.25);
}

.filter button{
    background:#a855f7;
    color:#fff;
}
.filter a{
    background:#22c55e;
    color:#fff;
    text-decoration:none;
}
.kpi{
    text-align:center;
    background:linear-gradient(135deg,#a855f7,#6366f1);
    color:#fff;
    padding:30px;
    border-radius:24px;
    margin-bottom:40px;
    box-shadow:0 15px 40px rgba(0,0,0,.25);
}
.kpi h3{
    margin:0;
    opacity:.9;
}
.kpi .score{
    font-size:56px;
    font-weight:bold;
    margin:10px 0;
}
.kpi small{
    display:block;
    margin-top:8px;
    opacity:.85;
}


canvas{
    margin:40px 0;
}

table{
    width:100%;
    border-collapse:collapse;
}
th,td{
    border:1px solid #e2e8f0;
    padding:10px;
    text-align:center;
}
th{
    background:#f1f5f9;
}
</style>
</head>

<body>

<div class="container">
<h1>Roof Top Pool Bar Feedback Report</h1>

<form class="filter" id="filterForm">
    From <input type="date" id="from" name="from" value="<?=$from?>">
    To <input type="date" id="to" name="to" value="<?=$to?>">
    <button type="submit">Filter</button>
    <a href="export_excel.php?from=<?=$from?>&to=<?=$to?>">📤 Export Excel</a>
</form>

<script>
document.getElementById('filterForm').addEventListener('submit', function(e){
    const from = document.getElementById('from').value;
    const to   = document.getElementById('to').value;

    if(from && to && from > to){
        alert('⚠️ From date cannot be later than To date');
        e.preventDefault();
    }
});

if($from > $to){
    die('Invalid date range');
}

</script>


<div class="kpi">
    <h3>Overall Guest Satisfaction</h3>
    <div class="score"><?=round($overall['overall_avg'],1)?> / 10</div>
    <small>
        Based on <?=$overall['total']?> feedbacks<br>
        Period: <?=date('d M Y',strtotime($from))?> – <?=date('d M Y',strtotime($to))?>
    </small>
</div>


<!-- ===== BAR CHART ===== -->
<canvas id="feedbackChart" height="120"></canvas>

<script>
const ctx = document.getElementById('feedbackChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Swimming','Food','Beverage','WiFi','Music'],
        datasets: [{
            data: [
                <?=round($avg['swimming'],1)?>,
                <?=round($avg['food'],1)?>,
                <?=round($avg['beverage'],1)?>,
                <?=round($avg['wifi'],1)?>,
                <?=round($avg['music'],1)?>
            ],
            backgroundColor: [
                '#60a5fa',
                '#34d399',
                '#fbbf24',
                '#a78bfa',
                '#fb7185'
            ],
            borderRadius: 16
        }]
    },
    options: {
        plugins: {
            legend: { display:false },
            datalabels: {
                anchor:'end',
                align:'top',
                color:'#0b1f29',
                font:{ weight:'bold', size:14 },
                formatter:(v)=>v.toFixed(1)
            }
        },
        scales: {
            y: {
                beginAtZero:true,
                ticks:{ precision:0 }
            }
        }
    },
    plugins:[ChartDataLabels]
});
</script>

<br>
<h2>Detail Feedback</h2>
<!-- ===== TABLE ===== -->
<table>
<tr>
<th>Date</th>
<th>Guest</th>
<th>Room</th>
<th>Swimming</th>
<th>Food</th>
<th>Beverage</th>
<th>WiFi</th>
<th>Music</th>
<th>Avg</th>
</tr>

<?php
$q = mysqli_query($conn,"
SELECT * FROM feedback
WHERE created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
ORDER BY created_at DESC
");

while($r=mysqli_fetch_assoc($q)){
echo "<tr>
<td>{$r['created_at']}</td>
<td>{$r['guest_name']}</td>
<td>{$r['room_number']}</td>
<td>{$r['swimming']}</td>
<td>{$r['food']}</td>
<td>{$r['beverage']}</td>
<td>{$r['wifi']}</td>
<td>{$r['music']}</td>
<td><b>{$r['average']}</b></td>
</tr>";
}
?>
</table>

</div>
</body>
</html>
