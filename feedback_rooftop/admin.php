<?php
include 'db.php';

$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to'] ?? date('Y-m-d');

/* ===== PAGINATION ===== */
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
if(!in_array($limit,[10,25,50])) $limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

/* ===== SEARCH ===== */
$search = $_GET['search'] ?? '';
$searchSql = $search ? "AND (guest_name LIKE '%$search%' OR room_number LIKE '%$search%')" : "";

/* ===== SORTING ===== */
$allowedSort = ['created_at','guest_name','room_number','swimming','food','beverage','wifi','music','average','note'];
$sort = $_GET['sort'] ?? 'created_at';
$dir  = $_GET['dir'] ?? 'DESC';

if(!in_array($sort,$allowedSort)) $sort = 'created_at';
$dir = $dir === 'ASC' ? 'ASC' : 'DESC';

/* ===== KPI ===== */
$avg = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT 
AVG(swimming) swimming,
AVG(food) food,
AVG(beverage) beverage,
AVG(wifi) wifi,
AVG(music) music
FROM feedback_rooftop_table
WHERE created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
"));

$overall = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT AVG(average) overall_avg, COUNT(*) total
FROM feedback_rooftop_table
WHERE created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
"));

$totalData = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total FROM feedback_rooftop_table
WHERE created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
$searchSql
"));

$totalPage = ceil($totalData['total'] / $limit);

function sortLink($label,$col,$sort,$dir){
    $newDir = ($sort==$col && $dir=='ASC') ? 'DESC' : 'ASC';
    return "<a href='?".$_SERVER['QUERY_STRING']."&sort=$col&dir=$newDir'>$label</a>";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Feedback</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<style>
body{
background:linear-gradient(135deg,#0b1f29,#1f3b4d);
font-family:Arial;
padding:40px;
color:#fff;
}
.container{
max-width:1200px;
margin:auto;
background:#fff;
color:#0b1f29;
padding:40px;
border-radius:24px;
}
.filter{
display:flex;
gap:12px;
margin-bottom:30px;
}
.filter input,.filter button,.filter a{
padding:10px 14px;
border-radius:10px;
border:none;
}
.filter button{background:#a855f7;color:#fff}
.filter a{background:#22c55e;color:#fff;text-decoration:none}

.kpi{
background:linear-gradient(135deg,#a855f7,#6366f1);
color:#fff;
padding:30px;
border-radius:20px;
text-align:center;
margin-bottom:40px;
}

.table-wrap{
max-height:420px;
overflow:auto;
border:1px solid #e5e7eb;
border-radius:12px;
width: 100%;
}

table{
width:100%;
min-width: 100%;
border-collapse:collapse;
}
th,td{
border:1px solid #e2e8f0;
padding:8px;
text-align:center;
}
th{
background:#f1f5f9;
position:sticky;
top:0;
z-index:5;
cursor:pointer;
}
tr:hover td{background:#f8fafc}

a{
padding:6px 10px;
border-radius:6px;
background:#f1f5f9;
text-decoration:none;
color:#0b1f29;
margin:0 2px;
}
.active{
background:#a855f7;
color:#fff;
}

.table-toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:14px;
    gap:12px;
    flex-wrap:wrap;
}

/* LEFT SIDE */
.toolbar-left{
    display:flex;
    align-items:center;
    gap:8px;
    background:#f8fafc;
    padding:8px 14px;
    border-radius:12px;
    box-shadow:0 2px 6px rgba(0,0,0,.05);
}

.toolbar-left label,
.toolbar-left span{
    font-size:14px;
    color:#475569;
}

/* SELECT */
.toolbar-left select{
    padding:6px 12px;
    border-radius:8px;
    border:1px solid #c7d2fe;
    background:white;
    font-weight:bold;
    cursor:pointer;
}

/* RIGHT SIDE */
.toolbar-right input{
    padding:10px 14px;
    min-width:220px;
    border-radius:12px;
    border:1px solid #c7d2fe;
    background:#f8fafc;
    outline:none;
    transition:.2s;
    font-size:14px;
}

.toolbar-right input:focus{
    background:white;
    border-color:#6366f1;
    box-shadow:0 0 0 3px rgba(99,102,241,.2);
}

.pagination a{
    padding:8px 14px;
    border-radius:999px;
}
.toolbar-right input:not(:placeholder-shown){
    background:#eef2ff;
}

</style>

<!-- ===== AUTO REFRESH (30 DETIK) ===== -->
<script>
setTimeout(()=>{
    const url = new URL(window.location.href);
    url.searchParams.set('refresh',Date.now());
    window.location.href = url.toString();
},30000);
</script>

</head>
<body>

<div class="container">

<h1 style="text-align:center">Roof Top Pool Bar Feedback Report</h1>

<form class="filter">
From <input type="date" name="from" value="<?=$from?>">
To <input type="date" name="to" value="<?=$to?>">
<button>Filter</button>
<a href="export_excel.php?from=<?=$from?>&to=<?=$to?>">📤 Export Excel</a>
</form>

<div class="kpi">
<h3>Overall Guest Satisfaction</h3>
<div style="font-size:56px;font-weight:bold">
<?=round($overall['overall_avg'],1)?> / 10
</div>
<small>
Based on <?=$overall['total']?> feedbacks<br>
<?=date('d M Y',strtotime($from))?> – <?=date('d M Y',strtotime($to))?>
</small>
</div>

<canvas id="feedbackChart" height="120"></canvas>

<script>
new Chart(document.getElementById('feedbackChart'),{
type:'bar',
data:{
labels:['Swimming','Food','Beverage','WiFi','Music'],
datasets:[{
data:[
<?=round($avg['swimming'],1)?>,
<?=round($avg['food'],1)?>,
<?=round($avg['beverage'],1)?>,
<?=round($avg['wifi'],1)?>,
<?=round($avg['music'],1)?>
],
backgroundColor:['#60a5fa','#34d399','#fbbf24','#a78bfa','#fb7185'],
borderRadius:16
}]
},
options:{
layout:{padding:{top:30}},
plugins:{legend:{display:false},datalabels:{anchor:'end',align:'top',clamp:true}},
scales:{y:{beginAtZero:true,max:10}}
},
plugins:[ChartDataLabels]
});
</script>

<br>
<h2 style="margin:30px 0;text-align:center">Detail Feedback</h2>

<form method="get" class="table-toolbar">
<input type="hidden" name="from" value="<?=$from?>">
<input type="hidden" name="to" value="<?=$to?>">
<div class="toolbar-left">
    <label>Show</label>
    <select name="limit" onchange="this.form.submit()">
        <option <?=$limit==10?'selected':''?>>10</option>
        <option <?=$limit==25?'selected':''?>>25</option>
        <option <?=$limit==50?'selected':''?>>50</option>
    </select>
    <span>rows</span>
</div>

<div class="toolbar-right">
    <input 
        type="text" 
        name="search" 
        placeholder="🔍 Search guest or room..."
        value="<?=$search?>"
    >
</div>


<div class="table-wrap">
<table>
<tr>
<th><?=sortLink('Date','created_at',$sort,$dir)?></th>
<th><?=sortLink('Guest','guest_name',$sort,$dir)?></th>
<th><?=sortLink('Room','room_number',$sort,$dir)?></th>
<th><?=sortLink('Swimming','swimming',$sort,$dir)?></th>
<th><?=sortLink('Food','food',$sort,$dir)?></th>
<th><?=sortLink('Beverage','beverage',$sort,$dir)?></th>
<th><?=sortLink('WiFi','wifi',$sort,$dir)?></th>
<th><?=sortLink('Music','music',$sort,$dir)?></th>
<th><?=sortLink('Avg','average',$sort,$dir)?></th>
<th><?=sortLink('Note','note',$sort,$dir)?></th>
</tr>

<?php
$q=mysqli_query($conn,"
SELECT * FROM feedback_rooftop_table
WHERE created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
$searchSql
ORDER BY $sort $dir
LIMIT $limit OFFSET $offset
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
<td>{$r['note']}</td>
</tr>";
}
?>
</table>
</div>
</form>

<div style="margin-top:20px;text-align:center">
<?php
for($i=1;$i<=$totalPage;$i++){
$cls = $i==$page?'class="active"':'';
echo "<a $cls href='?from=$from&to=$to&limit=$limit&search=$search&sort=$sort&dir=$dir&page=$i'>$i</a>";
}
?>
</div>

</div>
</body>
</html>
