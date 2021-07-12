
function doValidate() {
	console.log('Validating...');
	try {
		pw = document.getElementById('id_1723').value;
		addr = document.getElementById('addr').value;
		console.log("Validating password="+pw+" address="+addr);
		if (pw == null || pw == "" ||addr == null || addr == "") {
			alert("Both fields must be filled out");
			return false;
		}
		if(addr.indexOf('@')<0){
			alert("Invalid email address");
			return false;
		}
		return true;
	} catch(e) {
	return false;
	}
	return false;
}
