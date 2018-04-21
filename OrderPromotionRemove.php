<?php include "Common.php"; ?>
<?php Session::verify(); ?>
<?php
	$session = $_SESSION["Session"];
	$userLoggedIn = $session->user;
	$orderCurrent = $userLoggedIn->orderCurrent;
	$orderCurrent->promotion = null;
	header("Location: OrderCurrent.php");
?>
