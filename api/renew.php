<?
require_once "Store.php";

$szBaseDirectory = "/home/hitel00000/Music/";
$szBaseDirectory = preg_replace("/\/$/", "", $szBaseDirectory);

try
{
	$oDb	= StoreSelector::Select('sqlite');
	//$oDb -> Truncate();
	DirectoryListing( "" );
	$oDb -> Close();
}
catch( StoreException $e )
{
	echo $e -> getMessage();
}

function DirectoryListing( $szDir )
{
	global $szBaseDirectory;
	$szDirectory	= "$szBaseDirectory/$szDir";
	$szDirectory	= preg_replace("/\/$/", "", $szDirectory);
	echo "Setting.. '$szDirectory'\n";

	$oHandle	= opendir( $szDirectory );
	if( $oHandle === FALSE )
	{
		return;
	}

	$aPath	= array();
	while( $f = readdir($oHandle) )
	{
		if( $f[0] != '.' )
		{
			$aPath[]	= $f;
		}
	}

	closedir($oHandle);

	global $oDb;
	foreach( $aPath as $szPath )
	{
		$szFullPath	= "$szDirectory/$szPath";
		if( is_dir($szFullPath) )
		{
			DirectoryListing( ($szDir=="")?$szPath:"$szDir/$szPath" );
			$oDb -> Set(0, "D:$szDir", $szPath);
		}
		else if( is_file($szFullPath) )
		{
			$oDb -> Set(0, $szDir, $szPath);
		}
	}
}

