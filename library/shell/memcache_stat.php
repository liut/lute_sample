<?PHP

/* vim: set expandtab tabstop=4 shiftwidth=4: */

// $Id$

error_reporting(E_ALL ^ E_NOTICE);

if (!ini_get('display_errors')) {
	ini_set('display_errors', 1);
}

$memcache = new Memcache;
$host = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '127.0.0.1';
$port = isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : 11211;
echo 'host: ', $host, ':', $port, "\n";
$memcache->addServer($host, $port);
$arr = $memcache->getStats();
if($arr) {
        print_r($arr);
} else {
        echo 'fail', "\n";
}
