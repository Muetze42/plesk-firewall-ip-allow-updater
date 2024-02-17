<?php /** @noinspection PhpUnhandledExceptionInspection */

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

/** @noinspection PhpUnhandledExceptionInspection */
function getConnection($config): SSH2
{
    $key = PublicKeyLoader::load(file_get_contents(__DIR__ . '/../private.ppk'), $config['pkk-pass']);

    $ssh = new SSH2('localhost');
    if (!$ssh->login($config['user'], $key)) {
        throw new \Exception('Login failed');
    }

    return $ssh;
}

require_once __DIR__ . '/vendor/autoload.php';

$config = json_decode(file_get_contents(__DIR__ . '/../config.json'), true);
$ipTableFile = __DIR__ . $config['target'];
$ipTable = file_exists($ipTableFile) ? json_decode(file_get_contents($ipTableFile), true) : [];

$cache = null;
$cacheFile = __DIR__ . '/cache.txt';
if (file_exists($cacheFile)) {
    $cache = file_get_contents(trim($cacheFile));
}

$ipTable = array_values($ipTable);
$ips = array_merge($ipTable, $config['always-allowed-ips']);
$ips = array_unique($ips);
$ips = array_map('trim', $ips);
sort($ips);

$value = implode(',', $ips);

if (md5($value) . md5(json_encode($ipTable)) === $cache) {
    exit();
}
$rulesFile = __DIR__ . '/rules.json';
$ssh = getConnection($config);
$ssh->exec(sprintf('plesk ext firewall --export > %s', $rulesFile));
$rules = json_decode(file_get_contents($rulesFile), true);

foreach ($rules as $key => $rule) {
    if (!empty($rule['name']) && in_array($rule['name'], $config['firewall-names'])) {
        $ssh->exec(sprintf('plesk ext firewall --set-rule -from=%s -id=%s', $value, $rule['id']));
    }
}

$ssh->exec('plesk ext firewall --apply');

$ssh = getConnection($config);
$ssh->exec('plesk ext firewall --confirm');

file_put_contents($cacheFile, md5($value) . md5(json_encode($ipTable)));
unlink($rulesFile);
