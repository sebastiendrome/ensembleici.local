function checkEmail(email) {		
	var filter = /^[a-zA-Z0-9_\.\-]{2,}\@([a-zA-Z0-9\-])+\.[a-zA-Z0-9]{2,4}$/;
	var sortie = true;
	if (!filter.test(email)) {
		alert('Email incorrect');
		sortie= false;
	}
	return sortie;
}