
function createBarChart(chartData){
	var ctx = $("#myChart");
	var colour=[];
	var theLabels =[]
	for(i =0; i<20; i++){
		colour[i] = (chartData[i]<0)? 'rgba(235, 162, 54, 1)':
									  'rgba(54, 162, 235, 1)';
		theLabels[i] = "Lambda_"+i/20
	}
	
	var ctx = document.getElementById("myChart");
	
	var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: theLabels,
        datasets: [{
            label: 'Free Energy',
            data: chartData,
            backgroundColor: colour,
            borderWidth: 1
        }]
    },
    options: {
        scales: {
			xAxes: [{
			ticks: {
				autoSkip: true,
				maxTicksLimit: 20
					},
			scaleLabel: {
				display: true,
				labelString: 'Lamba Values'
					}
			}],
            yAxes: [{
                ticks: {
                    beginAtZero:true
                },
				scaleLabel: {
					display: true,
					labelString: 'KJ/mol'
				}
            }]
        }
    }
});
}

function determineYAxis(data){
	var yAxis;
	switch (data){
		case 'pressure':
			yAxis = 'bar'
			$('#type').text('Pressure(bar) of Molecule')
			break;
		case 'density':
			yAxis = 'kg/m^3'
			$('#type').text('Density(kg/m^3) of Molecule')
			break;
		case 'potential':
			yAxis = 'kJ/mol'
			$('#type').text('Potetial Energy(kJ/mol) of Molecule')
			break;
		case 'temperature':
			yAxis = 'K'
			$('#type').text('Temperture(K) of Molecule')
			break;
		case 'backBone':
			yAxis = 'nm'
			$('#type').text('Backbone(nm) of Molecule')
			break;
		case 'crystalBackBone':
			yAxis = 'nm'
			$('#type').text('Crystal Backbone(nm) of Molecule')
			break;
		default:
			yAxis = ''
			break;
	}
	return yAxis;
}

function determineFile(data){
	var file;
	switch (data){
		case 'pressure':
			file = 'md_pressure.xvg'
			break;
		case 'density':
			file = 'md_density.xvg'
			break;
		case 'potential':
			file = 'md_potential.xvg'
			break;
		case 'temperature':
			file = 'md_temperature.xvg'
			break;
		case 'backBone':
			file = 'rmsd_backbone.xvg'
			break;
		case 'crystalBackBone':
			file = 'rmsd_backbone_crystal.xvg'
			break;
		default:
			file = ''
			break;
	}
	return file;
}

function createChart(chartPoints,chartData,dataType){
	var yAxis = determineYAxis(dataType);
	var xAxis = (chartPoints[chartPoints.length-1]<41) ? 'ns':'ps' 
	
	var ctx = $("#myChart");
	
	var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartPoints,
        datasets: [{
            label: dataType,
			fill: false,
			borderColor: 'rgba(54, 162, 235, 1)',
            data: chartData,
            borderWidth: 1
        }]
    },
    options: {
        scales: {
			xAxes: [{
			ticks: {
				autoSkip: true,
				maxTicksLimit: 20
					},
			scaleLabel: {
				display: true,
				labelString: xAxis
					}
			}],
            yAxes: [{
				scaleLabel: {
					display: true,
					labelString: yAxis
					}
            }]
        }
    }
});
}

function updateHtml(userData){
	
	$('#title').text(userData[0])
	$('#user').text("Submitted by: "+userData[4])
	$('#description').text(userData[8])
}

function generateResults(simId,data){
	//fixes html in tree
	
	$.ajax({url: 'getResultInfo.php', method: 'POST', 
			data: {id: simId},
			success: function(userData){updateHtml(JSON.parse(userData)[0]);}});
	//determines graph
	if(data == 'freeEnergy'){
		
		var resultsFile = 'results/sim'+simId+'/bar.xvg'
		$.ajax({url: 'getResults.php', method: 'POST', 
			data: {results: resultsFile } ,
			success: function(simData){createBarChart(JSON.parse(simData))}
			});	
	}
	
	else{
		var resultsFile = 'results/sim'+simId+'/'+determineFile(data);
		$.ajax({url: 'getMDData.php', method: 'POST', 
			data: {results: resultsFile } ,
			success: function(simData){
				createChart(JSON.parse(simData)['dataPoints'],JSON.parse(simData)['data'],data)
				}
			});	
	}
}