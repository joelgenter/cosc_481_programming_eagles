
function createSimulationsList(queue){
	//generates list group
	$('#simulationsList').append("<!-- List group --> <ul class='list-group'>")
	for(var num in queue){
		$('#simulationsList').append("<div name='simulationRow' class='list-group-item list-group-item-action'> 			\
			<div class = 'row'>   																\
				<div class = 'col-lg-2'>  														\
					"+queue[num][0]+"		   													\
				</div>  																		\
				<div class = 'col-lg-2'>														\
					"+queue[num][1]+"															\
				</div>																			\
				<div class = 'col-lg-2'>														\
					"+queue[num][4]+"															\
				</div>																			\
				<div class = 'col-lg-2'>														\
					"+queue[num][2]+"															\
				</div>																			\
				<div class = 'col-lg-2'>														\
					"+queue[num][3]+"															\
				</div>																			\
				<div class = 'col-lg-2 text-center'>											\
					<button type='button' id='simUp"+num+"' name='simUp' class='btn btn-default btn-xs'>			\
						<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>	\
					</button>																	\
					<button type='button' id='simDown"+num+"' name='simDown' class='btn btn-default btn-xs'>		\
						<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>	\
					</button>																	\
					<button type='button' id='simDelete"+num+"' name='simDelete' class='btn btn-default btn-xs'>	\
						<span class='glyphicon glyphicon-trash' aria-hidden='true'></span>		\
					</button>																	\
				</div>																			\
			</div>																				\
		</div>	");
	}
	$('#simulationsList').append("</div></ul>");
	$(function(){
		$('[name="simUp"]').click(function(){
			incrementSim(this.id[this.id.length-1],queue)
			updateList();
		});
		$('[name="simDown"]').click(function(){
			decrementSim(this.id[this.id.length-1],queue)
			updateList();
		});
		$('[name="simDelete"]').click(function(){
			deleteSim(this.id[this.id.length-1],queue)
			updateList();
		});
});
}

function incrementSim(simNumber, asdf){		
	$.ajax({url: 'modifyQueue.php', method: 'POST', 
			data: {functionName:'increment', arguments: [(parseInt(simNumber)+1), simNumber]},
			success: function(mesg){ }});
			
	
}

function decrementSim(simNumber, data){
	if (simNumber==data.length-1)
		alert("Cannot decrement the last item in the queue")
	else{
			$.ajax({url: 'modifyQueue.php', method: 'POST', 
			data: {functionName:'increment', arguments: [(parseInt(simNumber)+2), parseInt(simNumber)+1]},
			success: function(mesg){ }});
	}
}

function deleteSim(simNumber, data){
	$.ajax({url: 'modifyQueue.php', method: 'POST', 
			data: {functionName:'delete', arguments: (parseInt(simNumber)+1)},
			success: function(mesg){ console.log(mesg)}});
}

function updateList(){
	$('[name="simulationRow"]').remove();
	generateSimulationsList();
}

function generateSimulationsList(){
	
	getSimulations(function(obj){
		var results =[];
		var completedSimulations = JSON.parse(obj)
			for(var i of completedSimulations.results)
				if (i[5]>=0)
					results.push(i);
		
		createSimulationsList(results);
	})
}

function getSimulations(func){
	$.ajax({url: 'getSimulations.php', method: 'POST', 
			success: function(obj){func(obj);}});	
}

