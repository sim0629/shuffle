<?
$json   = $_POST['json'];
$json   = json_decode($json);
if( empty($json) )
{
    echo "리스트 없음";
    exit;
}

require_once "config.php";
createFile($json);
exit;

function createFile( $json )
{
	$file_handle = fopen(LISTING_LOCAL_PATH, 'w') or die("$filename 안 만들어짐 --");

	fwrite($file_handle, "<?xml version=\"1.0\"?>");
	fwrite($file_handle, "<playlist version=\"1\">\n <trackList>\n");
    foreach( $json as $elem )
    {
        $xml    = <<<XML_START
    <track>
        <title>{$elem->title}</title>
        <location>{$elem->loc}</location>
    </track>
XML_START;
        fwrite($file_handle, $xml);
    }
	fwrite($file_handle, " </trackList>\n</playlist>");

	fclose($file_handle);
}
?>
