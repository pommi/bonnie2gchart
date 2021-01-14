google.load("visualization", "1", {packages:["corechart"]});


// utility method for parsing the csv
function parseBonnieCsv(csvData) {
	var timeRegex = /^(\d+)([mun]s)$/;
	var col103 = ['name', 'sz', 'outch', 'outchcpu', 'outblk', 'outblkcpu', 'outrw', 'outrwcpu', 'inch', 'inchcpu', 'inblk', 'inblkcpu', 'seek', 'seekcpu', 'files', 'sc', 'sccpu', 'sr', 'srcpu', 'sd', 'sdcpu', 'rc', 'rccpu', 'rr', 'rrcpu', 'rd', 'rdcpu'];
	var col196 = ['vera', 'verb', 'name', 'conc', 'stz', 'sz', 'tta', 'outch', 'outchcpu', 'outblk', 'outblkcpu', 'outrw', 'outrwcpu', 'inch', 'inchcpu', 'inblk', 'inblkcpu', 'seek', 'seekcpu', 'ttb', 'ttc', 'ttd', 'tte', 'ttf', 'sc', 'sccpu', 'sr', 'srcpu', 'sd', 'sdcpu', 'rc', 'rccpu', 'rr', 'rrcpu', 'rd', 'rdcpu', 'latoutch', 'latoutblk', 'latoutrw', 'latinch', 'latinblk', 'latrand', 'latsc', 'latsr', 'latsd', 'latrc', 'latrr', 'latrd'];
	var col198 = ['vera', 'verb', 'name', 'conc', 'stz', 'sz', 'tta', 'seeks', 'seek_proc_count', 'outch', 'outchcpu', 'outblk', 'outblkcpu', 'outrw', 'outrwcpu', 'inch', 'inchcpu', 'inblk', 'inblkcpu', 'seek', 'seekcpu', 'ttb', 'ttc', 'ttd', 'tte', 'ttf', 'sc', 'sccpu', 'sr', 'srcpu', 'sd', 'sdcpu', 'rc', 'rccpu', 'rr', 'rrcpu', 'rd', 'rdcpu', 'latoutch', 'latoutblk', 'latoutrw', 'latinch', 'latinblk', 'latrand', 'latsc', 'latsr', 'latsd', 'latrc', 'latrr', 'latrd'];
	var colNames = [];
	var csv = {};

	var lineNumber = 0;
	csvData.split('\n').forEach(function(line) {
		lineNumber++;
		var cols = line.split(',');
		if (cols.length <= 1) {
			return;
		}
		if (cols.length != col103.length && cols.length != col196.length && cols.length != col198.length) {
			msg = 'Column count mistmatch:<br />';
			msg += 'v1.03 expects ' + col103.length + ' columns<br />';
			msg += 'v1.96 expects ' + col196.length + ' columns<br />';
			msg += 'v1.98 expects ' + col198.length + ' columns<br />';
			msg += 'Line ' + lineNumber + ' has ' + cols.length + ' columns';
			throw new Error(msg);
		}
		if (cols.length == col103.length) {
			colNames = col103;
		} else if (cols.length == col196.length) {
			colNames = col196;
		} else {
			colNames = col198;
		}

		// parse the columns into a key/value structure
		var parsedLine = {};
		colNames.forEach(function(name, i) {
			var field = cols[i];
			if (matches = field.match(timeRegex)) {
				switch (matches[2]) {
					case 'ms':
						field = parseFloat(matches[1]);
						break;
					case 'us':
						field = parseFloat(matches[1]) / 1000;
						break;
					case 'ns':
						field = parseFloat(matches[1]) / 1000000;
						break;
				}
			}
			parsedLine[name] = field;
		});
		// merge in the parsed data, and fill in any missing columns
		col196.forEach(function(name) {
			csv[name] = csv[name] || [];
			csv[name].push(parsedLine[name] || '');
		});
	});
	return csv;
}


// draw the chart into the div
function drawChart() {
	// reset previous output
	var errorField = document.getElementById('error');
	var chartField = document.getElementById('chartDiv');
	errorField.innerHTML = '';
	chartField.innerHTML = '';
	// load new inputs
	var formType = document.getElementById('graphType');
	var type = formType.value;
	var formData = document.getElementById('data');
	// parse data
	try {
		var data = parseBonnieCsv(formData.value);
	} catch(e) {
		var msg = e.message;
		errorField.innerHTML = msg;
		return;
	}
	if (!data['name']) {
		// no valid data was loaded
		return;
	}

	// prepare the graph
	var table = new google.visualization.DataTable();
	table.addColumn('string', 'Tests');
	data['name'].forEach(function(name) {
		table.addColumn('number', name);
	});
	chartTypes[type]['types'].forEach(function(label) {
		var row = [columnLabels[label]];
		row = row.concat(data[label].map(function(d) {
			return parseFloat(d)
		}));
		table.addRows([row]);
	});
	var options = {
		title: chartTypes[type]['title'],
		vAxis: {title: chartTypes[type]['name'],
		        titleTextStyle: {color: 'red'}}
	};

	// draw the charg
	var chart = new google.visualization.BarChart(document.getElementById('chartDiv'));
	chart.draw(table, options);
}


// deeplink handling
function updateDeeplink() {
	var formType = document.getElementById('graphType');
	var type = encodeURIComponent(formType.value);
	var formData = document.getElementById('data');
	var data = encodeURIComponent(formData.value);
	data = data.replace(/%2C/g, ',');	// commas don't need quoting
	var link = '?t=' + type + '&data='  + data;
	history.replaceState(null, '', link);
}

function loadDeeplink() {
	var queryString = window.location.search.replace("?","").split("&").reduce(function(o,term){ var param = term.split("="); o[param[0]]=decodeURIComponent(param[1]); return o;},{})
	var formData = document.getElementById('data');
	if (queryString['data']) {
		formData.value = queryString['data'];
	}
	var formType = document.getElementById('graphType');
	if (queryString['t']) {
		formType.value = queryString['t'];
	}
}


// page load
document.addEventListener("DOMContentLoaded", function(e) {
	// add the chart types
	var selectBox = document.getElementById('graphType');
	for (key in chartTypes) {
		var type = chartTypes[key];
		var option = document.createElement('option');
		var optionText = document.createTextNode(type['name']);
		option.setAttribute('value', key);
		option.appendChild(optionText);
		selectBox.appendChild(option);
	}

	// subscribe to the submit button
	var submitButton = document.getElementById('submit');
	submitButton.addEventListener('click', drawChart);

	// subscribe to the select field
	var typeSelect = document.getElementById('graphType');
	typeSelect.addEventListener('change', drawChart);
	typeSelect.addEventListener('change', updateDeeplink);

	// subscribe to the data field
	var dataField = document.getElementById('data');
	dataField.addEventListener('change', updateDeeplink);

	// load a previous deeplink
	loadDeeplink();
	drawChart();
});
