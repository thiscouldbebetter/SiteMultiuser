<html>
<body>

<?php
	$filenameToRead = "Test.txt";
	$fileToRead = fopen($filenameToRead, "r");
	if ($fileToRead == null)
	{
		die("Unable to open file!");
	}
	$fileSizeInBytes = filesize($filenameToRead);
	$fileContents = fread($fileToRead, $fileSizeInBytes);
	echo $fileContents;
	fclose($fileToRead);
?>

</body>
</html>