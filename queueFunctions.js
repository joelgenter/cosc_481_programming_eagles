var barUpdater;
function createSimulationsList(queue, status){
	//generates list group
	$('#simulationsList').append("<!-- List group --> <ul class='list-group'>");
	if(queue.length>0){
	for(var num in queue){
		$('#simulationsList').append("<div name='simulationRow' class=' panel-body list-group-item'> 			\
			<div class = 'row'>   																\
				<div class = 'col-lg-3'>  														\
					"+queue[num][0]+"		   													\
				</div>  																		\
				<div class = 'col-lg-2'>														\
					"+queue[num][1]+"															\
				</div>																			\
				<div class = 'col-lg-2'>														\
					"+queue[num][4]+"															\
				</div>																			\
				<div class = 'col-lg-3'>														\
					"+queue[num][2]+"															\																		\															\
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
				</div>" : "") + "																\
			</div>		\
			"+((num==0)? '</br><div class = "progress"><div id = "partProgress" 						\
								class = "progress-bar progress-bar-info progress-bar-striped active" \
								style="width:1%"><span class="text-black" id="barMessage"> </span> </div>  </div>\
							<div class = "progress"><div id = "fullProgress" \
								class = "progress-bar progress-bar-success progress-bar-striped active"  \
								style="width:1%"><span class="text-black" id="fullMessage"> </span> </div>\
							</div> \
						  </div>' : '' )+ "\
		</div>");
	}
	$('#simulationsList').append("</div></ul>");
	$(function(){
		$('[name="simUp"]').click(function(){
			if(this.id[this.id.length-1]==1){
				$('#alertModal').modal('show')
				$('#modalTitle').text('Confirm Move')
				$('#modalText').text('If this simulation is moved, all progress will be lost!')
				$('#confirmMove').attr('class','btn btn-default')
				$('#confirmDelete').attr('class','btn btn-default hide')
			}
			else if(!$(this).hasClass('disabled'))
				incrementSim(this.id[this.id.length-1],status)
		});
		$('[name="simDown"]').click(function(){
			
			if(this.id[this.id.length-1]==0){
				$('#alertModal').modal('show')
				$('#modalTitle').text('Confirm Move')
				$('#modalText').text('If this simulation is moved, all progress will be lost!')
				$('#confirmMove').attr('class','btn btn-default')
				$('#confirmDelete').attr('class','btn btn-default hide')
			}
			else if(!$(this).hasClass('disabled'))
				decrementSim(this.id[this.id.length-1],status)
		});
		$('[name="simDelete"]').click(function(){
			$('#confirmDelete').attr('name',(this.id));
			$('#alertModal').modal('show')
			$('#modalTitle').text('Confirm Delete')
			$('#modalText').text('Are you sure you want to delete this simulation?')
			$('#confirmDelete').attr('class','btn btn-default')
			$('#confirmMove').attr('class','btn btn-default hide')
		});
	});
	updateBar(queue[0][6],queue[0][7],status)
	barUpdater=setInterval(function(){updateBar(queue[0][6],queue[0][7],status)},10000);
	}
}

function incrementSim(simNumber, data){		
	if(simNumber>0){
		$.ajax({url: 'modifyQueue.php', method: 'POST', 
			data: {functionName:'increment', arguments: [(parseInt(simNumber)), parseInt(simNumber)-1]},
			success: function(mesg){ updateList(data);}});
	}
}

function decrementSim(simNumber, data){
	if (simNumber==data.length-1){
		
	}
	else{
			$.ajax({url: 'modifyQueue.php', method: 'POST', 
			data: {functionName:'increment', arguments: [(parseInt(simNumber)+1), parseInt(simNumber)]},
			success: function(mesg){ updateList(data);}});
	}
}

function deleteSim(simNumber, data){
	$.ajax({url: 'modifyQueue.php', method: 'POST', 
	  data: {functionName:'delete', arguments: (parseInt(simNumber))},
	  success: function(mesg){updateList(data);}});					
}

/** 'updates' list by deleting the list and creating a new one.
  *
  */
function updateList(status){
	clearInterval(barUpdater);
	$('[name="simulationRow"]').remove();
	generateSimulationsList(status);
}

function updateBar(folderPath, simDuration,status){
	var oldPercent = parseInt($('#fullProgress').parent().children()[0].style.width.substring(0,$('#fullProgress').parent().children()[0].style.width.length-1))-1
	$.ajax({url: 'getSimulationStatus.php', method: 'POST', 
			data: {fileLocation: folderPath, duration: simDuration },
			success: function(percent){
				if(Math.round(percent)<oldPercent){
					console.log("An error has occured: New Percentage: "+percent+"  Old Percentage: "+oldPercent)
					updateList(status)
				}
				var partial =1
				var message ="";
				if(percent<=1){
					partial = 0
					percent = 0
					message = "Initializing"
				}
				else if(percent<5){
					partial = Math.round(1000-(200*(5 - percent)))/10
					message = "Performing energy minimization: "+ partial +"%"
				}
				else if(percent<25){
					partial =   Math.round(1000-50*(25 - percent))/10
					message = "Performing equilibrium: "+ partial +"%"
				}
				else if(percent<80){
					partial = Math.round(1000-1000*(80 - percent)/55)/10
					message = "Simulating molecule: "+partial+"%"
				}
				else if(percent<=100){
					partial = Math.round(1000-50*(100 - percent))/10
					message = "Performing free energy calculations: "+ partial +"%"
				}
				else{
					message ="Error: Reticulating Splines"
					console.log(percent)
				}
				updateBarValues(percent, partial, message)
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				 //alert("Status: " + textStatus);
				console.log(XMLHttpRequest)
				 //alert("Error: " + errorThrown); 
				}
			});
}

/** Changes the progress bar completion percent to the passed amount
  *  	and the message to the passed string.
  */
function updateBarValues(amount1, amount2, string){
	$('#fullProgress').css("width",amount1+'%');
	$('#partProgress').css("width",amount2+'%');
	$('#barMessage').text(string);
	//$('#fullMessage').css("color",'black');
	$('#fullMessage').text("Overall: "+Math.round(amount1*10)/10+"%");
}

/** Calls getSimulations and passes it a created function that parses the data.
  * 	Once the data has been parsed it sends the info to createSimulationsList()
  */
function generateSimulationsList(status){
	getSimulations(function(obj){
		var results =[];
		var completedSimulations = JSON.parse(obj)
			for(var i of completedSimulations.results)
				if (i[5]>=0)
					results.push(i);
		
		createSimulationsList(results,status);
	})
}

/** Gets simulation data from the sql server by calling the appropriate php file.
  *	After Gathering data calls the passed function with the data. 
  */
function getSimulations(func){
	$.ajax({url: 'getSimulations.php', method: 'POST', 
			success: function(obj){func(obj);}});	
}