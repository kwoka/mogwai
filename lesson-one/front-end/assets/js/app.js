'use strict';

/**
 * JS Script for login page
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
(function() {
	/**
	 * Capture the form submit
	 */
	document.getElementById('login-form').onsubmit = function(e) {
		// stop the form from being submitted
		e.preventDefault();

		// get the form body
		var xhr = new XMLHttpRequest();
		xhr.open('POST', 'http://mogwai.sveltefox.com/lesson-one/public/login', true);
		xhr.onload = function() {
			var responseMessage = '';
			var success = false;
	     	// parse the response to JSON format
	     	var responseJson = JSON.parse(xhr.responseText);
	     	// success case
	     	if (xhr.status == 200 && responseJson.returnCode == 0) {
	     		success = true;
        		responseMessage = 'Successfully logged in as user: ' + responseJson.userId;
	     	} else {
	     		// error case
	     		responseMessage = responseJson.error;
	     	}
	     	// conditionally set the class to green or red based on sucess or failure
		    document.getElementById('alert-message').className = (success ? 'alert-success' : 'alert-error');
		    document.getElementById('alert-message').innerHTML = responseMessage;
		};
		
		// submit the request
		xhr.send(new FormData(this));
	};
})();
