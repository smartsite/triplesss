// Registration & validation

window.onload = function () {

	var step = 0;
	var navButtons = document.querySelectorAll(".nav .button");
	var screens =  document.querySelectorAll(".registration-screen");
	var textFields = document.querySelectorAll(".registration-screen input");
	var statusList = document.querySelectorAll(".registration-screen ul li div");
	var updateButton = document.getElementById("update");
	var tick = {};
	var badUserNames = ['admin', 'fuck', 'shit', 'cunt', 'cock', 'porn'];

	var validateForm = function (frm) {
	    var valid = false;
	    return valid;
	}


	var checkTicks = function(list){
		// check if all ticks for current screen		
		var tickCount = list.length;
		list.forEach(function(b){
			if(b.classList.contains("tick")){ tickCount--}
		})	
		return tickCount || 0;		
	}


	var setStatusList = function(status, item){
		if(status == "tick"){
			statusList[item].classList.remove("cross");
			statusList[item].classList.add("tick");
			statusList[item].innerHTML = "&#x2713;";
		}
		if(status == "cross"){
			statusList[item].classList.remove("tick");
			statusList[item].classList.add("cross");
			statusList[item].innerText = "X";
		}
	}

	//var stepSelector = "#screen-" + step;

	var checkPassword = function(t){
		var mediumRegex = new RegExp("^(((?=.*[a-z])(?=.*[A-Z]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[0-9])))(?=.{6,})");
		var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
		var pwdToCheck = t.srcElement.value;	
				
		if(strongRegex.test(pwdToCheck)){
			setStatusList("tick", 3);	
			tick.password = true;
		}else{
			setStatusList("cross", 3);
			tick.password = false;		
		}		
	}

	var checkEmail = function(t){
		var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		var emailToCheck = t.srcElement.value;		
		
		if(re.test(emailToCheck)){
			setStatusList("tick", 4);	
			tick.email = true;
		}else{
			setStatusList("cross", 4);
			tick.email = false;		
		}
	}

	var nameLength = {};

	var checkNameFieldLength = function(t){
		return function(){			
			nameLength[t.srcElement.id] = t.srcElement.value.length;
			return nameLength;	
		}		
	}

	var checkNames = function(t){
		var nameFieldsLengths = checkNameFieldLength(t);
		if(nameFieldsLengths().firstName && nameFieldsLengths().lastName){
			setStatusList("tick", 5);	
			tick.nameLength = true;
		} else {
			setStatusList("cross", 5);	
			tick.nameLength = false;
		}
	}

	var checkBadNames = function(userName){
		var allowed = true;
		badUserNames.map(function(n){			
			if(userName.toLowerCase().indexOf(n) > -1 ) {
				allowed = false;
			}
		})
		return allowed;
	}

	var checkAddress = function(t){
		var nameFieldsLengths = checkNameFieldLength(t);
		var allValid;
		var postcode = document.getElementById('postcode').value;

		if(nameFieldsLengths().address > 3 && nameFieldsLengths().suburb > 2 && nameFieldsLengths().postcode == 4 && !isNaN(postcode) ){
			setStatusList("tick", 6);	
			tick.nameLength = true;
		}else{
			setStatusList("cross", 6);	
			tick.nameLength = false;
		}		
	}
	
	var checkUserName = function(t){
		
		var nameToCheck = t.srcElement.value;
		var alphaNumeric = /^[a-z0-9]+$/i;	
		
		if(nameToCheck.length < 6){
			setStatusList("cross", 1);	
			tick.nameLength = false;
		}else{
			setStatusList("tick", 1);
			tick.nameLength = true;			
		}

		if(alphaNumeric.test(nameToCheck)){
			setStatusList("tick", 2);
			tick.isNameAlpha = true;	
		}else{
			setStatusList("cross", 2);
			tick.isNameAlpha = false;	
		}
		

		// last check, do DB lookup
		if(checkBadNames(nameToCheck) === false){
			setStatusList("cross", 0);
			tick.nameAvailable = false;
		} else {
			apiPost("api/user/exists",  {username: nameToCheck})
			.then(function(d){
				d.json().then(function(e){
					if(e.status == "false"){					
						setStatusList("tick", 0);
						tick.nameAvailable = true;					
					}else{
						setStatusList("cross", 0);
						tick.nameAvailable = false;
					}
					// need to do this async as well as sync!
					enableScreenNextButton();
				})
			});			
		}					
	}

	var hideScreens = function(){
		screens.forEach(function(s){
			s.classList.add("hide-right");
		})
	}
	

	var nextScreen = function(){
		//hideScreens ();
		step++;
		navButtons[1].classList.add("hide");	

		if(step > screens.length - 1){
			step = screens.length - 1;
		}
		console.log("step " + step);
		screens[step].classList.remove("hide-right");
		if(step > 0){
			screens[step - 1].classList.add("hide-left");
			screens[step - 1].classList.remove("hide-right");
			navButtons[0].classList.remove("hide");
		}
		
		if(step >= screens.length -  1){
			navButtons[1].classList.add("hide");
		}
	}

	var prevScreen = function(){
		//hideScreens ();
		step--;
		screens[step + 1].classList.add("hide-right");
		if(step < 0){
			step = 0;				
		}
		//console.log("step " + step);
		if(step > 0){			
			screens[step].classList.remove("hide-left");
			screens[step].classList.remove("hide-right");			
		}

		if(step == 0){
			navButtons[0].classList.add("hide");
			navButtons[1].classList.remove("hide");
			screens[step].classList.remove("hide-right");
			screens[step].classList.remove("hide-left");	
		}
		
		screens[step + 1].classList.remove("hide-left");		
	}

	var validateFields = function(){
		// final check we have all ticks
		var status = "";
		var tickDivs = document.querySelectorAll('.registration-screen ul li div');
		var haveTicks = document.querySelectorAll('.registration-screen ul li div.tick');
		if(tickDivs.length != haveTicks.length){
			status = "Please check all fields are correct.";
		} 
		return status;
	}

	var enableScreenNextButton = function(){	
		var list = document.querySelectorAll("#screen-" + step +  " ul li div");	
		if(checkTicks(list)){
			navButtons[1].classList.add("hide");				
		} else {
			navButtons[1].classList.remove("hide");				
		}		
	}

	var createUser = function(userName, password, email){		
		apiPost("api/user/create",  {username: userName, password: password, email: email })
		.then(function(d){
			d.json().then(function(e){
				if(e.status == 'true') {
					console.log("Created user");
					var userFields = document.querySelectorAll('input, select');
					userDetails = {};
					for(var i=0; i < userFields.length; i++){
						var input = userFields[i];
						var id = input.id;
						var val = input.value;
						userDetails[id] = val;
					}

					var ud = JSON.stringify(userDetails);
					console.log(ud);

					// We can update all the fields after... there's enough info to register  
					apiPost("api/user/update", ud)
					.then(function(d){
						d.json().then(function(e){
							if(e.status == 'true') {
								nextScreen();
							} else {
								document.querySelectorAll("#user_error").innerHTML("<p>" + e.error + "</p>")
							}
						});
					});
				}
			});
		});
		return true;
	}

	var updateUser = function(data){
		// adds details to a user (key / values)
		return true;
	}

	var doRegistration = function(){
		var success = false;

		return success;
	}

	var validUser = function() {
		var urlParams = new URLSearchParams(window.location.search);
		var token = urlParams.get('token');
		var email = urlParams.get('email');

		apiPost("api/user/validate",  {email: email, token: token })
		.then(function(d){
			d.json().then(function(e){
			
			}
		});	

	}

	
	updateButton.addEventListener("click", function(f){
		if(validateFields() == ""){
			var userName = document.querySelectorAll("#userName")[0].value;
			var password = document.querySelectorAll("#password")[0].value;
			var email = document.querySelectorAll("#email")[0].value;
			createUser(userName, password, email);
		
		}else{
			// show error message in a status bar somewhere
			alert(validateFields());
		}
	});


	textFields.forEach(function(t){
		
		// Input field validation
		t.addEventListener("keyup", function(f){			
			
			switch (f.target.id) {
	    		case "userName":
		        checkUserName(f);
				break;
				
				case "password":
		        checkPassword(f);
				break;
				
				case "email":
		        checkEmail(f);
				break;
				
				case "firstName":
				checkNames(f);				
				break;
				
				case "lastName":
				checkNames(f);				
				break;
				
				case "address":
				checkAddress(f);				
				break;
				
				case "suburb":
				checkAddress(f);				
				break;
				
				case "postcode":
				checkAddress(f);				
		        break;
			}
			
			enableScreenNextButton();

		})		 

	});


	navButtons.forEach(function(b){		
		b.addEventListener("click", function(e){					
			if(e.target.classList.contains("next")){
				nextScreen();				
			}
			if(e.target.classList.contains("prev")){
				prevScreen();
			}				
		})
	});
	
}	