<?php
include "../config/database.php";

/* =========================
   GET FILTER
========================= */
$from  = $_GET['from'] ?? date('Y-m-01');
$to    = $_GET['to'] ?? date('Y-m-d');
$point = $_GET['point'] ?? 'overall';

$fromDB = $from . " 00:00:00";
$toDB   = $to   . " 23:59:59";

/* =========================
   MAP POINT TO COLUMN
========================= */
$map = [
    'food'  => 'food',
    'pool'  => 'pool',
    'music' => 'music',
    'wifi'  => 'wifi'
];


/* =========================
   SQL QUERY
========================= */
if ($point === 'overall') {

    $sql = "
        SELECT
            SUM(food='Good')
          + SUM(pool='Good')
          + SUM(music='Good')
          + SUM(wifi='Good') AS good,

            SUM(food='Average')
          + SUM(pool='Average')
          + SUM(music='Average')
          + SUM(wifi='Average') AS average,

            SUM(food='Bad')
          + SUM(pool='Bad')
          + SUM(music='Bad')
          + SUM(wifi='Bad') AS bad
        FROM feedback
        WHERE created_at BETWEEN '$fromDB' AND '$toDB'
    ";

}
 else {

    $col = $map[$point];

    $sql = "
        SELECT
            SUM($col='Good') AS good,
            SUM($col='Average') AS average,
            SUM($col='Bad') AS bad
        FROM feedback
        WHERE created_at BETWEEN '$fromDB' AND '$toDB'
    ";
}


$result = mysqli_query($conn, $sql);
if (!$result) {
    die("SQL ERROR: " . mysqli_error($conn) . "<br><br>" . $sql);
}
$data = mysqli_fetch_assoc($result);


/* SAFE DEFAULT */
$good    = (int)($data['good'] ?? 0);
$average = (int)($data['average'] ?? 0);
$bad     = (int)($data['bad'] ?? 0);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="/icon_hic.png">
<title>Roof Top Pool Bar Feedback Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f5f6fa;
    padding: 30px;
}

.toolbar {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 30px;
}

.filter-form {
    display: flex;
    gap: 15px;
    align-items: flex-end;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

label {
    font-size: 13px;
    color: #555;
}

input, select {
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
}

.btn-filter {
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    background: #ddd;
    font-weight: 600;
    cursor: pointer;
}

.btn-filter:hover {
    background: #ccc;
}

.btn-export {
    padding: 12px 22px;
    background: #004990;
    color: #fff;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
}

.btn-export:hover {
    background: #003366;
}

canvas {
    background: #fff;
    padding: 20px;
    border-radius: 14px;
}
.header{text-align:center;margin-bottom:25px}
.header h2{margin-bottom:5px}
</style>
</head>

<body>
<header class="header">
    <h2>Roof Top Pool Bar Feedback Dashboard</h2>
</header>


<div class="toolbar">
<form method="GET" action="dashboard.php" id="filterForm" class="filter-form">

    <div class="field">
        <label>From</label>
        <input type="date" name="from" value="<?= $from ?>">
    </div>

    <div class="field">
        <label>To</label>
        <input type="date" name="to" value="<?= $to ?>">
    </div>

    <div class="field">
        <label>Point</label>
        <select name="point" id="pointSelect">
            <option value="overall" <?= $point=='overall'?'selected':'' ?>>Overall</option>
            <option value="food" <?= $point=='food'?'selected':'' ?>>Food</option>
            <option value="pool" <?= $point=='pool'?'selected':'' ?>>Pool</option>
            <option value="music" <?= $point=='music'?'selected':'' ?>>Music</option>
            <option value="wifi" <?= $point=='wifi'?'selected':'' ?>>WiFi</option>
        </select>
    </div>

    <button type="submit" class="btn-filter">Filter</button>
</form>

<a href="export_excel.php?from=<?= $from ?>&to=<?= $to ?>&point=<?= $point ?>" class="btn-export">
📊 Export Excel
</a>
</div>

<canvas id="feedbackChart" height="120"></canvas>

<script>
const ctx = document.getElementById("feedbackChart");

window.myChart = new Chart(ctx, {
    type: "bar",
    data: {
        labels: ["Good", "Average", "Bad"],
        datasets: [{
            label: "Feedback Result",
            data: [<?= $good ?>, <?= $average ?>, <?= $bad ?>],
            backgroundColor: ["#2ecc71","#f1c40f","#e74c3c"]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    }
});

/* AUTO SUBMIT */
document.getElementById("pointSelect").addEventListener("change", function () {
    document.getElementById("filterForm").submit();
});
</script>

</body>
</html>
