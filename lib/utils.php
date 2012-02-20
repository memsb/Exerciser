<?php
/**
 * 	Generates a unique ID number to be the users API Key with some added salt
 * 	@return String of hexidecimal
 *	@namespace Exerciser
 */
function generateAPIKey(){
	return uniqid('apikey_');
}

?>
