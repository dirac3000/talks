/*!
 * Scripts for Talks v1.0 
 * Copyright 2014 Alvaro Moran
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */


/* this is to make the talks table clickable */
$('.talks-table > tr[href]').click(function() {
    // row was clicked
	window.location.href = $(this).attr('href');
    return false;
});

