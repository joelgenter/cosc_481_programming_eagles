
function createChart(chartData){
	console.log(chartData)
	var ctx = $("#myChart");
	var colour=[];
	var theLabels =[]
	for(i =0; i<21; i++){
		colour[i] = 'rgba(54, 162, 235, 1)';
		theLabels[i] = "Lambda"
	}
	
	//var chartInstance = new Chart(ctx, {
    //type: 'line',
    //data: [0.19,1.4,-2.0],
	//backgroundColor: 'rgba(54, 162, 235, 1)',
    //options: {
      //  responsive: false
    //}
	//});
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
	$('#user').text(userData[4])
	$('#description').text(userData[3])
}

function generateResults(resultsFile){
	$.ajax({url: 'getResultInfo.php', method: 'POST', 
			data: {results: resultsFile},
			success: function(userData){console.log(userData); updateHtml(JSON.parse(userData)[0]);}});	
	
	$.ajax({url: 'getResults.php', method: 'POST', 
			data: {results: resultsFile},
			success: function(simData){console.log(simData);createChart(JSON.parse(simData))}});		
	
}