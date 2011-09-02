<?

// API MAIN CODE
require_once "Util.php";
require_once "Responsor.php";

$oAction;
try
{
	$oAction = ResponsorSelector::Select();
	$oAction -> ReadParam();
	$oAction -> VerifyParam();
}
catch(Exception $e)
{
	// pikachu!
	echo $e -> getMessage();
	exit;
}

$oAction -> Response();

