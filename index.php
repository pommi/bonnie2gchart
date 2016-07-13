<?php

require_once 'inc/functions.php';
include_once 'inc/labels.php';

$data = parse_bonnie_csv($_REQUEST['data']);

if (!isset($_REQUEST['t']) || empty($_REQUEST['t'])) {
	echo <<<EOT
<!DOCTYPE html>
<html>
<head><title>Bonnie to Google Chart</title></head>
<body>
<h1>Bonnie to Google Chart</h1>
<form action="." method="post">
<p>
<select name="t">

EOT;

	foreach ($types as $key => $type) {
		printf('<option value="%s">%s</option>', $key, $type['name']);
	}

	echo <<<EOT
</select>
</p>
<textarea name="data" cols="100" rows="10" placeholder="1.97,1.97,hostname,1,....."></textarea>
<input type="submit" name="Submit">
</body>
</html>
EOT;
	exit;
}

echo <<<EOT
<html>
  <head>
	<title>bonnie2gchart - {$types[$_REQUEST['t']]['name']}</title>
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

foreach($types[$_REQUEST['t']]['types'] as $label)
	echo addRow($data[$label], $labels[$label]);

echo <<<EOT
        var options = {
          title: '{$types[$_REQUEST['t']]['title']}',
          vAxis: {title: '{$types[$_REQUEST['t']]['name']}',  titleTextStyle: {color: 'red'}}
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
