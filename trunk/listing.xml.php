<?
require_once "config.php";
header('Content-Type: application/xml');
echo file_get_contents(LISTING_LOCAL_PATH);
?>
