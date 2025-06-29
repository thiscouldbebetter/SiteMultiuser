<?php

class Configuration
{
	public function __construct()
	{
		$this->appDirectory = "Site/Source";
		$this->databaseServerName = "localhost";
		$this->databaseUsername = "web";
		$this->databasePassword = "[redacted]";
		$this->databaseName = "Site";
		$this->emailAddressHelp = "help@site.test";
		$this->emailAddressNotify = "notify@site.test";
		$this->emailEnabled = false;
		$this->errorReportingEnabled = true;
		$this->siteTitle = "Multiuser Site";

		$this->applyToEnvironment();
	}

	public function applyToEnvironment()
	{
		$errorReportingEnabled = $this->errorReportingEnabled;
		error_reporting($errorReportingEnabled ? 1 : 0);

		if ($errorReportingEnabled)
		{
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		}

		$documentRoot = $_SERVER["DOCUMENT_ROOT"] . "/";
		$appDirectory = $this->appDirectory;
		$appRoot = $documentRoot . $appDirectory . "/";
		$classRoot = $appRoot . "Classes/";
		$includePaths = $appRoot . ":" . $classRoot;
		set_include_path($includePaths);
	}
}

return new Configuration();

?>
