<?php

function parse_bonnie_csv($file) {
	if (($handle = fopen("$file", "r")) !== FALSE) {
		# bonnie++ 1.03 column names
		$col_103 = array('vera', 'verb', 'name', 'conc', 'stz', 'sz', 'tta', 'outch', 'outchcpu', 'outblk', 'outblkcpu', 'outrw', 'outrwcpu', 'inch', 'inchcpu', 'inblk', 'inblkcpu', 'seek', 'seekcpu', 'files', 'sc', 'sccpu', 'sr', 'srcpu', 'sd', 'sdcpu', 'rc', 'rccpu', 'rr', 'rrcpu', 'rd', 'rdcpu');
		# bonnie++ 1.96 column names
		$col_196 = array('vera', 'verb', 'name', 'conc', 'stz', 'sz', 'tta', 'outch', 'outchcpu', 'outblk', 'outblkcpu', 'outrw', 'outrwcpu', 'inch', 'inchcpu', 'inblk', 'inblkcpu', 'seek', 'seekcpu', 'ttb', 'ttc', 'ttd', 'tte', 'ttf', 'sc', 'sccpu', 'sr', 'srcpu', 'sd', 'sdcpu', 'rc', 'rccpu', 'rr', 'rrcpu', 'rd', 'rdcpu', 'latoutch', 'latoutblk', 'latoutrw', 'latinch', 'latinblk', 'latrand', 'latsc', 'latsr', 'latsd', 'latrc', 'latrr', 'latrd');

		while (($line = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if (count($col_103) != count($line) && count($col_196) != count($line))
				die('not enough columns');

			if (count($col_103) == count($line)) {
				$col_names = $col_103;
			} elseif (count($col_196) == count($line)) {
				$col_names = $col_196;
			}

			foreach (array_combine($col_names, $line) as $k=>$v) {

				if (preg_match('/^\++$/', $v))
					$v = 0;

				if (preg_match('/^(\d+)([mu]s)$/', $v, $matches)) {
					switch ($matches[2]) {
						case 'ms':
							$v = $matches[1];
							break;
						case 'us':
							$v = $matches[1] / 1000;
							break;
						case 'ns':
							$v = $matches[1] / 1000000;
							break;
						default:
							$v = $v;
							break;
					}
				}

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
