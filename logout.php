<?php
/*
	Module Logout
	Logout handler for platform users.
	Revision 00001
	By James "Carbon" leon Neo
*/
require_once 'index.php';
ob_clean();
Common\API\EntranceAPI::logout();
?>
