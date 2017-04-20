
function createChart(chartData){
	console.log(chartData)
	var ctx = $("#myChart");
	var colour=[];
	var theLabels =[]
	for(i =0; i<20; i++){
		colour[i] = 'rgba(54, 162, 235, 1)';
		theLabels[i] = "Lambda"
	}

	var ctx = document.getElementById("myChart");

	var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: theLabels,
        datasets: [{
            label: '# of Votes',
            data: chartData,
            backgroundColor: colour,
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
}

function updateHtml(userData){
	$('#title').text(userData[0])
	$('#user').text("Submitted by: "+userData[4])
	$('#description').text(userData[3])
}

function generateResults(simId){
	//sets where location is
	var resultsFile = 'results/sim'+simId+'/bar.xvg'
	console.log(resultsFile);
	$.ajax({url: 'getResultInfo.php', method: 'POST',
			data: {id: simId},
			success: function(userData){updateHtml(JSON.parse(userData)[0]);}});

	$.ajax({url: 'getResults.php', method: 'POST',
			data: {results: resultsFile } ,
			success: function(simData){createChart(JSON.parse(simData))}
			});

}
