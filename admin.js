var admin = 'admin';
var standard = 'standard';
var pending = 'pending';
var userCount = 0;

//------------------------------------------------------------------------------
// Removes the user that the selected is next to from Users in ProteinSim, then
// removes the user from the userList HTML object.

    function remUser(username){
			// var appCache = window.applicationCache;
			// appCache.update(); //this will attempt to update the users cache and changes the application cache status to 'UPDATEREADY'.
			// if (appCache.status == window.applicationCache.UPDATEREADY) {
			// 	appCache.swapCache(); //replaces the old cache with the new one.
			// }
      // Get username
      // Remove from database
			$.ajax({
				data: 'username=' + username,
				url: 'removeUser.php',
				method: 'POST',
				// If successful, standard message
				success: function(message) {
					console.log(message);
				}
			});
      // Remove user from the userList
      document.getElementById('row'+username).remove();
    }

//------------------------------------------------------------------------------
// Updates the user type with option selected in dropdown menu, then change
// the HTML code to reflect said change.

		function updateType(dropType,username){
			// var appCache = window.applicationCache;
			// appCache.update(); //this will attempt to update the users cache and changes the application cache status to 'UPDATEREADY'.
			// if (appCache.status == window.applicationCache.UPDATEREADY) {
			//   appCache.swapCache(); //replaces the old cache with the new one.
			// }
			// Get username
			console.log(username)
			// Update the database
			$.ajax({
				data: 'username=' + username + '&userType=' + dropType,
				url: 'updateUsers.php',
				method: 'POST',
				// If successful, standard message
				success: function(message) {
					console.log(message);
				}
			});
			// Update HTML to show new dropType value for said username
			document.getElementById('typeButton'+username).innerHTML =
						dropType +
						'<span class="caret"></span>';
		}

//------------------------------------------------------------------------------
// Fuction is used to convert node into string.

		function nodeToString ( node ) {
			var tmpNode = document.createElement( "div" );
			tmpNode.appendChild( node.cloneNode( true ) );
			var str = tmpNode.innerHTML;
			tmpNode = node = null; // prevent memory leaks in IE
			return str;
		}


function updateHtml(userData, email){
	for(var user of userData){
    console.log(user);
		console.log((user.email+"  "+email))
		{$(usersList).append(
					'<div id="row'+user.username+'" class="list-group-item list-group-item-action userList">' +
						'<div class = "row">' +
							'<div class = "col-lg-2 col-sm-4" id = "username' + userCount + '">' +
								user.username +
							'</div>' +
                '<div class="col-lg-2 col-sm-4" id = "'+user.email+'">' + user.firstName + " " + user.lastName + '</div>'+
                '<div class="col-lg-2 col-sm-4" id = "'+user.email+'">' + user.email + '</div>'+

                '<div class = "col-lg-2 col-sm-4">' +
									'<div class="dropdown">'+
										'<button style="width: 8em;" '+
										'class="'+((email== user.email)? 'disabled': '')+' btn btn-secondary dropdown-toggle" '+
										'id="typeButton'+user.username+'" type="button" data-toggle="dropdown">' + user.type +
										'<span class="caret"></span></button>' +
										'<ul id="typelist'+userCount+'" class="dropdown-menu">' +
											'<li><a href="#" id="A'+userCount+'" onclick="updateType(admin,\''+user.username+'\')">admin</a></li>'+
											'<li><a href="#" id="S'+userCount+'" onclick="updateType(standard,\''+user.username+'\')">standard</a></li>'+
											'<li><a href="#" id="P'+userCount+'" onclick="updateType(pending,\''+user.username+'\')">pending</a></li>'+
											'</ul>' +
									'</div>' +
								'</div>' +
							'<div class = "col-lg-2 col-sm-4">' +
								'<i id="remove'+user.username+'" '+
								'onclick="'+((email== user.email)? 'cantDelete()': 'remUser(\''+user.username+'\') ')+'" '+
								'class="'+((email== user.email)? 'disabled': '')+' fa fa-remove"></i>' +
							'</div>' +
						'</div>' +
					'</div>');


				userCount++;
		}
	}
}

function cantDelete(){
	alert("you can't delete yourself!")
}

function generateUsers(email){
	//fixes html in tree
	$.ajax({url: 'getUsers.php', method: 'POST',
			success: function(userData){updateHtml(JSON.parse(userData),email);}});
}
