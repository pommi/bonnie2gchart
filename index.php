<?php

require_once 'inc/functions.php';
include_once 'inc/labels.php';

$file = 'bonnie.csv';
$data = parse_bonnie_csv($file);

$types = array(
	'blockio' => 'Block IO',
	'metadata' => 'File metadata',
	'metadata-read' => 'File metadata (read)',
	'blockio-cpu' => 'Block IO CPU',
	'metadata-cpu' => 'Seq and Random CPU',
	'blockio-latency' => 'Block IO Latency',
	'metadata-latency' => 'File metadata Latency',
	'metadata-read-latency' => 'File metadata (read) Latency',
);

if (!isset($_GET['t']) || empty($_GET['t'])) {
	echo "<html>\n<body>\n<title>Bonnie to Google Chart</title>\n<h1>Bonnie to Google Chart</h1>\n<ul>";
	foreach ($types as $key => $type) {
		printf('<li><a href="?t=%s">%s</a></li>', $key, $type);
	}
	echo "</ul>\n</body>\n</html>\n";
}

echo <<<EOT
<html>
  <head>
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

$vtitle = $types[$_GET['t']];
switch ($_GET['t']) {
	case 'blockio':
		$title = 'kB/sec (higher is better)';
		$types = array('outblk', 'outrw', 'inblk');
		break;
	case 'metadata':
		$title = 'files/sec (higher is better)';
		$types = array('sc', 'sd', 'rc', 'rd');
		break;
	case 'metadata-read':
		$title = 'files/sec (higher is better)';
		$types = array('sr', 'rr');
		break;
	case 'blockio-cpu':
		$title = 'CPU usage in % (lower is better)';
		$types = array('outblkcpu', 'outrwcpu', 'inblkcpu', 'seekcpu');
		break;
	case 'metadata-cpu':
		$title = 'CPU usage in % (lower is better)';
		$types = array('sccpu', 'srcpu', 'sdcpu', 'rccpu', 'rrcpu', 'rdcpu');
		break;
	case 'blockio-latency':
		$title = 'milliseconds (lower is better)';
		$types = array('latoutblk', 'latoutrw', 'latinblk');
		break;
	case 'metadata-latency':
		$title = 'milliseconds (lower is better)';
		$types = array('latsc', 'latsd', 'latrc', 'latrd');
		break;
	case 'metadata-read-latency':
		$title = 'milliseconds (lower is better)';
		$types = array('latsr', 'latrr');
		break;
}

foreach($types as $label)
	echo addRow($data[$label], $labels[$label]);

echo <<<EOT
        var options = {
          title: '{$title}',
          vAxis: {title: '{$vtitle}',  titleTextStyle: {color: 'red'}}
        };

        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="chart_div" style="width: 900px; height: 500px;"></div>
  </body>
</html>

EOT;
