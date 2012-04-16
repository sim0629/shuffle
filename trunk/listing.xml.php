<?
require_once "config.php";
if( IsDev() )
{
    header('Content-Type: application/xml');
    echo file_get_contents(LISTING_LOCAL_PATH);
}
else
{
    header('Content-Type: application/xml');
    echo file_get_contents(LISTING_LOCAL_PATH);
}
?>
