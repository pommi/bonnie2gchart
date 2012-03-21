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
	case 'blockio':
	default:
		$title = 'kB/sec (higher is better)';
		$vtitle = 'Block IO';
		$types = array('outblk', 'outrw', 'inblk');
		break;
	case 'metadata':
		$title = 'files/sec (higher is better)';
		$vtitle = 'File metadata';
		$types = array('sc', 'sd', 'rc', 'rd');
		break;
	case 'metadata-read':
		$title = 'files/sec (higher is better)';
		$vtitle = 'File metadata (read)';
		$types = array('sr', 'rr');
		break;
	case 'blockio-cpu':
		$title = 'CPU usage in % (lower is better)';
		$vtitle = 'Block IO CPU';
		$types = array('outblkcpu', 'outrwcpu', 'inblkcpu', 'seekcpu');
		break;
	case 'metadata-cpu':
		$title = 'CPU usage in % (lower is better)';
		$vtitle = 'Seq and Random CPU';
		$types = array('sccpu', 'srcpu', 'sdcpu', 'rccpu', 'rrcpu', 'rdcpu');
		break;
	case 'blockio-latency':
		$title = 'milliseconds (lower is better)';
		$vtitle = 'Block IO Latency';
		$types = array('latoutblk', 'latoutrw', 'latinblk');
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
