function createSimulationsList(queue, status){
	//generates list group
	$('#simulationsList').append("<!-- List group --> <ul class='list-group'>");
	for(var num in queue){
		$('#simulationsList').append("<div name='simulationRow' class=' panel-body list-group-item'> 			\
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
				</div>"	+ ((status == "admin")?
				"<div class = 'col-lg-2 text-center'>											\
					<button type='button' id='simUp"+num+"' name='simUp'						\
					class='" + ((num==0)? 'disabled' : 'enabled') +" btn btn-default btn-xs'>	\
						<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>	\
					</button>																	\
					<button type='button' id='simDown"+num+"' name='simDown' 					\
					class='" + ((num==queue.length-1)? 'disabled' : 'enabled') +" btn btn-default btn-xs'>		\
						<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>	\
					</button>						\
					<button type='button' name = 'simDelete' id='simDelete"+num+"' class='btn btn-default btn-xs'>	\
						<span class='glyphicon glyphicon-trash' aria-hidden='true'></span>		\
					</button>																	\
				</div>" : "") + "																		\
			</div>		\
			"+((num==0)? '<div class = "progress"><div id = "progressBar" 						\
							   class = "progress-bar progress-bar-success progress-bar-striped active" \
							   style="width:1%"><span class="text-black" id="barMessage"> </span> </div></div>':'' )+" \
		</div>");
	}
	$('#simulationsList').append("</div></ul>");
	$(function(){
		$('[name="simUp"]').click(function(){
			incrementSim(this.id[this.id.length-1],queue);
		});
		$('[name="simDown"]').click(function(){
			decrementSim(this.id[this.id.length-1],queue);
		});
		$('[name="simDelete"]').click(function(){
			$('#confirmDelete').attr('name',(this.id));
			$('#alertModal').modal('show');
		});
	});
	updateBar();
}

function incrementSim(simNumber, data){
	if(simNumber>0){
		$.ajax({url: 'modifyQueue.php', method: 'POST',
			data: {functionName:'increment', arguments: [(parseInt(simNumber)), parseInt(simNumber)-1]},
			success: function(mesg){ updateList();}});
	}
}

function decrementSim(simNumber, data){
	if (simNumber==data.length-1)
		alert("Cannot decrement the last item in the queue");
	else{
			$.ajax({url: 'modifyQueue.php', method: 'POST',
			data: {functionName:'increment', arguments: [(parseInt(simNumber)+1), parseInt(simNumber)]},
			success: function(mesg){ updateList();}});
	}
}

function deleteSim(simNumber, data){
	console.log(simNumber);
	//$.ajax({url: 'modifyQueue.php', method: 'POST',
	//  data: {functionName:'delete', arguments: (parseInt(simNumber))},
	//	success: function(mesg){ updateList();}});
}

/** 'updates' list by deleting the list and creating a new one.
  *
  */
function updateList(){
	$('[name="simulationRow"]').remove();
	generateSimulationsList();
}

function updateBar(){
	console.log($('#progressBar').parent().children()[0].style.width);
	$.ajax({url: 'getSimulationStatus.php', method: 'POST',
			data: {fileLocation:'./Gromacs/' },
			success: function(percent){
				var message ="";
				if(percent<5)
					message = "Initializing";
				else if(percent<25)
					message = "Performing equilibrium simulation";
				else if(percent<80)
					message = "Simulating molecule";
				else if(percent<100)
					message = "Performing free energy calculations";
				else
					message ="Error: Reticulating Splines";
				updateBarValues(percent,message)
			},
			failure: function(mesg){cnsole.log(mesg)}});
}

/** Changes the progress bar completion percent to the passed amount
  *  	and the message to the passed string.
  */
function updateBarValues(amount, string){
	$('#progressBar').css("width",amount+'%');
	$('#barMessage').css("color",'black');
	$('#barMessage').text(string);
}

/** Calls getSimulations and passes it a created function that parses the data.
  * 	Once the data has been parsed it sends the info to createSimulationsList()
  */
function generateSimulationsList(status){
	getSimulations(function(obj){
		var results =[];
		var completedSimulations = JSON.parse(obj);
			for(var i of completedSimulations.results)
				if (i[5]>=0)
					results.push(i);

		createSimulationsList(results, status);
	})
}

/** Gets simulation data from the sql server by calling the appropriate php file.
  *	After Gathering data calls the passed function with the data.
  */
function getSimulations(func){
	$.ajax({url: 'getSimulations.php', method: 'POST',
			success: function(obj){func(obj);}});
}
