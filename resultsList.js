
function createResultsList(results, status){
	//generates list group
	console.log(results[0])
	$('#resultsList').append("<!-- List group --> <ul class='list-group'>")
	for(var simulation of results){
		$('#resultsList').append("<a href='resultPage.php?id="+simulation[6]+"' class='list-group-item list-group-item-action'> 		\
			<div class = 'row'>   															\
				<div class = 'col-lg-6'>  													\
					"+simulation[0]+"		   												\
				</div>  																	\														\
				<div class = 'col-lg-4'>													\
					"+simulation[4]+"														\
				</div>																		\																\
				<div class = 'col-lg-2'>													\
					"+simulation[3]+"														\
				</div>																		\															\
			</div>")
	}
	$('#resultsList').append("</ul>");
}

function generateResultsList(){
	
	getSimulations(function(obj){
		var results =[];
		var completedSimulations = JSON.parse(obj)
			for(var i of completedSimulations.results)
				if (i[5]<0) //arbitrary '5' because i[5] stores positiion in queue
					results.push(i);
		
		createResultsList(results);
	})
}

function getSimulations(func){
	$.ajax({url: 'getSimulations.php', method: 'POST', 
			success: function(obj){func(obj);}});	
}