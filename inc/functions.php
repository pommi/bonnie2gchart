<?php

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

function addRow($rows, $desc) {
	return sprintf("data.addRows([['%s', %s]]);\n", $desc, join($rows, ', '));
}
