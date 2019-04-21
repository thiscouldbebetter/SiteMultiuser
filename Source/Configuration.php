<?php

class Configuration
{
	public function __construct()
	{
		$this->appDirectory = "Store/Source";
		$this->databaseServerName = "localhost";
		$this->databaseUsername = "web";
		$this->databasePassword = "[redacted]";
		$this->databaseName = "Store";
		$this->emailAddressHelp = "help@onlinestore.test";
		$this->emailAddressNotify = "notify@onlinestore.test";
		$this->emailEnabled = false;
		$this->errorReportingEnabled = true;
		$this->paymentClientConfig = "{ \"type\": \"Square\", \"accessToken\": \"[redacted]\", \"applicationID\": \"[redacted]\", \"locationID\": \"[redacted]\"}";
		$this->siteTitle = "Online Store";

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
