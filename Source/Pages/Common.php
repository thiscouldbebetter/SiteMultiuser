<?php

$configuration = include("../Configuration.php");

include("../Classes/MathHelper.php");
include("../Classes/Notification.php");
include("../Classes/PageWriter.php");
include("../Classes/PersistenceClientMySQL.php");
include("../Classes/Session.php");
include("../Classes/User.php");
include("../Classes/WebClient.php");

Session::start($configuration);

?>
