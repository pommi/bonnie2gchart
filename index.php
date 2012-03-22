<?php

require_once 'inc/functions.php';
include_once 'inc/labels.php';

$file = 'bonnie.csv';
$data = parse_bonnie_csv($file);

if (!isset($_GET['t']) || empty($_GET['t'])) {
	echo <<<EOT
<!DOCTYPE html>
<html>
<head><title>Bonnie to Google Chart</title></head>
<body>
<h1>Bonnie to Google Chart</h1>
<ul>

EOT;

	foreach ($types as $key => $type) {
		printf('<li><a href="?t=%s">%s</a></li>', $key, $type['name']);
	}

	echo <<<EOT
</ul>
</body>
</html>
EOT;
	exit;
}

echo <<<EOT
<html>
  <head>
	<title>bonnie2gchart - {$types[$_GET['t']]['name']}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Tests');

EOT;

foreach ($data['name'] as $val) {
	printf("        data.addColumn('number', '%s')\n", $val);
}

foreach($types[$_GET['t']]['types'] as $label)
	echo addRow($data[$label], $labels[$label]);

echo <<<EOT
        var options = {
          title: '{$types[$_GET['t']]['title']}',
          vAxis: {title: '{$types[$_GET['t']]['name']}',  titleTextStyle: {color: 'red'}}
        };

        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <a href=".">« index</a>
    <div id="chart_div" style="width: 900px; height: 500px;"></div>
  </body>
</html>

EOT;
