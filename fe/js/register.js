// Registration & validation


var validateForm = function (frm) {
    var valid = false;
    return valid;
}

var checkPassword = (t) => {
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

var checkEmail = (t) => {
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

var checkNameFieldLength = (t) => {
    return function(){			
        nameLength[t.srcElement.id] = t.srcElement.value.length;
        return nameLength;	
    }		
}

var checkNames = (t) => {
    var nameFieldsLengths = checkNameFieldLength(t);
    if(nameFieldsLengths().firstName && nameFieldsLengths().lastName){
        setStatusList("tick", 5);	
        tick.nameLength = true;
    } else {
        setStatusList("cross", 5);	
        tick.nameLength = false;
    }
}

var checkBadNames = (userName) => {
    var allowed = true;
    badUserNames.map(function(n){			
        if(userName.toLowerCase().indexOf(n) > -1 ) {
            allowed = false;
        }
    })
    return allowed;
}

var checkAddress = (t) => {
    var nameFieldsLengths = checkNameFieldLength(t);
    var allValid;
    var postcode = document.getElementById('postcode').value;

    if(nameFieldsLengths().address > 3 && nameFieldsLengths().suburb > 2 && nameFieldsLengths().postcode == 4 && !isNaN(postcode) ){
        return true;
    }else{
        return false;
    }		
}

  


window.onload = function () {

    /*
    var step = 0;
	
	var textFields = document.querySelectorAll(".registration-screen input");
	var updateButton = document.getElementById("update");
	var badUserNames = ['admin', 'fuck', 'shit', 'cunt', 'cock', 'porn'];
	
	updateButton.addEventListener("click", function(f){
		if(validateFields() == ""){
			var username = document.querySelectorAll("#username")[0].value;
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
    
    */	
	
}	