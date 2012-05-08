<?
require_once "Store.php";

try
{
	$oDb	=& StoreSelector::Select('sqlite');
	//$oDb -> Create();
	//$oDb -> Set( "1", "asdf/fjwien/fjieno한글/한글한글 /한글 - 또 한글/", "asdfasonivn/ajsidnfwl/asdoifn");
	//$oDb -> Set( "2", "aa", "asdfasonivn/ajsidnfwl/asdoif");
	//$oResult = $oDb -> FindData( array('context' => 'a', 'trail' => 'fn', 'notexact' => true) );
	//$oResult = $oDb -> GetAll();
	//var_dump($oResult);
	//$oResult = $oDb -> GetAllContext();
	$oResult = $oDb -> FindData( array('context' => 'Other') );
	//$oResult = $oDb -> Count();
	var_dump($oResult);
}
catch( Exception $e )
{
	echo $e -> getMessage();
}

