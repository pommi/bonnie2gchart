<?php

require_once 'inc/functions.php';

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

echo addRow($data['outblk'], 'Seq Block Output');
echo addRow($data['outrw'], 'Block Rewrite');
echo addRow($data['inblk'], 'Block Input');

$title = 'kB/sec';
$vtitle = 'Bonnie++ Absolute block IO';

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
