<?

// 매칭이 되어야 함
define("MUSIC_URL", "/music/");
define("MUSIC_LOCAL_PATH", "/home/hitel00000/Music/");

define("LISTING_FILENAME", "listing_{$_SERVER['REMOTE_ADDR']}.xml");
define("LISTING_LOCAL_PATH", "/tmp/" . LISTING_FILENAME);
define("LISTING_URL", LISTING_FILENAME);

