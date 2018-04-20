<?php include "Common.php"; ?>
<?php PageWriter::sessionVerify(); ?>
<?php
	$session = $_SESSION["Session"];
	$userLoggedIn = $session->user;
	$orderCurrent = $userLoggedIn->orderCurrent;
	$orderCurrent->promotion = null;
	header("Location: OrderCurrent.php");
?>