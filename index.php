<?php

$file = 'bonnie.csv';

function parse_bonnie_csv($file) {
	if (($handle = fopen("$file", "r")) !== FALSE) {
		# bonnie++ 1.96 column names
		$col_names = array('vera', 'verb', 'name', 'conc', 'stz', 'sz', 'tta', 'outch', 'outchcpu', 'outblk', 'outblkcpu', 'outrw', 'outrwcpu', 'inch', 'inchcpu', 'inblk', 'inblkcpu', 'seek', 'seekcpu', 'ttb', 'ttc', 'ttd', 'tte', 'ttf', 'sc', 'sccpu', 'sr', 'srcpu', 'sd', 'sdcpu', 'rc', 'rccpu', 'rr', 'rrcpu', 'rd', 'rdcpu', 'latoutch', 'latoutblk', 'latoutrw', 'latinch', 'latinblk', 'latrand', 'latsc', 'latsr', 'latsd', 'latrc', 'latrr', 'latrd');

		while (($line = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if (count($col_names) != count($line))
				die('not enough columns');

			foreach (array_combine($col_names, $line) as $k=>$v) {
				$csv[$k][] = $v;
			}
		}

		fclose($handle);
	}

	return $csv;
}

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

function addRow($rows, $desc) {
	return sprintf("data.addRows([['%s', %s]]);\n", $desc, join($rows, ', '));
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
