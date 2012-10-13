<?php

function parse_bonnie_csv($file) {
	if (($handle = fopen("$file", "r")) !== FALSE) {
		# bonnie++ 1.03 column names
		$col_103 = array('name', 'sz', 'outch', 'outchcpu', 'outblk', 'outblkcpu', 'outrw', 'outrwcpu', 'inch', 'inchcpu', 'inblk', 'inblkcpu', 'seek', 'seekcpu', 'files', 'sc', 'sccpu', 'sr', 'srcpu', 'sd', 'sdcpu', 'rc', 'rccpu', 'rr', 'rrcpu', 'rd', 'rdcpu');
		# bonnie++ 1.96 column names
		$col_196 = array('vera', 'verb', 'name', 'conc', 'stz', 'sz', 'tta', 'outch', 'outchcpu', 'outblk', 'outblkcpu', 'outrw', 'outrwcpu', 'inch', 'inchcpu', 'inblk', 'inblkcpu', 'seek', 'seekcpu', 'ttb', 'ttc', 'ttd', 'tte', 'ttf', 'sc', 'sccpu', 'sr', 'srcpu', 'sd', 'sdcpu', 'rc', 'rccpu', 'rr', 'rrcpu', 'rd', 'rdcpu', 'latoutch', 'latoutblk', 'latoutrw', 'latinch', 'latinblk', 'latrand', 'latsc', 'latsr', 'latsd', 'latrc', 'latrr', 'latrd');

		$col_103_count  = count($col_103);
                $col_196_count  = count($col_196);

		$i = 0;
		while (($line = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$csv_count	= count($line);
			$i++;

			// Ignore any blank lines
			if ($csv_count <= 1) continue;

			if ($col_103_count != $csv_count && $col_196_count != $csv_count)
			{
				// Let the user know what went wrong
				$msg = "Column count mismatch:<br />";
				$msg.= "v1.03 expects $col_103_count columns<br />";
				$msg.= "v1.96 expects $col_196_count columns<br />";
				$msg.= "Line $i of the CSV file has $csv_count columns";
				die($msg);
			}

			if (count($col_103) == count($line)) {
				$col_names = $col_103;
			} elseif (count($col_196) == count($line)) {
				$col_names = $col_196;
			}
			$combined = array_combine($col_names, $line);
			if (count($col_103) == count($line)) {
				$combined = array_merge(array_fill_keys($col_196, ''), $combined);
			}

			foreach ($combined as $k=>$v) {

				if (empty($v) || preg_match('/^\++$/', $v))
					$v = 'null';

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
