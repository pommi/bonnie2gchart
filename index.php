<?php

require_once 'inc/functions.php';
include_once 'inc/labels.php';

$file = 'bonnie.csv';
$data = parse_bonnie_csv($file);

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

switch ($_GET['t']) {
	case 'metadata':
		$title = 'files/sec';
		$vtitle = 'File metadata';
		$types = array('sc', 'sd', 'rc', 'rd');
		break;
	case 'metadata-read':
		$title = 'files/sec';
		$vtitle = 'File metadata (read)';
		$types = array('sr', 'rr');
		break;
	default:
		$title = 'kB/sec';
		$vtitle = 'Block IO';
		$types = array('outblk', 'outrw', 'inblk');
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
