<?php

class PageWriter
{
	public static function displayStatusMessage($statusMessage)
	{
		echo "<pre>" . wordwrap($statusMessage) . "</pre>";
	}

	public static function elementHeadWrite($pageTitle)
	{
		$configuration = $_SESSION["Configuration"];
		$siteTitle = $configuration->siteTitle;
		echo("<title>" . $siteTitle . " - " . $pageTitle . "</title>");
		echo("<link rel='stylesheet' href='Style.css'>");
	}

	public static function footerWrite()
	{
		echo("<script id='scriptFooter' type='text/javascript' src='Footer.js'></script>");
	}

	public static function headerWrite()
	{
		echo("<script id='scriptHeader' type='text/javascript' src='Header.js'></script>");
	}
}

?>
