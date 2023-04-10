<?php

$array = [
    ['alias' => 'jp2', 'id' => '', 'path' => '/52566/'],
];

$defaultHostOpts = [
    "v" => "2",
    "aid" => "0",
    "net" => "ws",
    "type" => "none",
    "tls" => "tls",
    // 节点备注
    "ps" => '',
    // 节点域名
    "host" => '',
    "path" => '/swoole',
    // 节点域名
    "add" => '',
    "port" => 443,
    "id" => '',
];
$list = [];
foreach ($array as $item) {
    $alias = $item['alias'] ?? '';
    echo "alias: $alias" . PHP_EOL;
    $host = $item['alias'] . '.ulifeai.com';
    $name = parseName($host);
    $item['ps'] = $name;
    $item['host'] = $host;
    $item['add'] = $host;
    $c = array_merge($defaultHostOpts, $item);
    $list[] = 'vmess://' . base64_encode(json_encode($c, JSON_UNESCAPED_UNICODE));
}

$str = implode(PHP_EOL, array_values($list));
$base64 = base64_encode($str);
echo $base64 . PHP_EOL;
file_put_contents('v2ray.txt', $base64);
// 复查
foreach (explode(PHP_EOL, base64_decode(file_get_contents('v2ray.txt'))) as $line) {
//    var_dump(base64_decode(str_replace('vmess://', '', $line)));
    $v = json_decode(base64_decode(str_replace('vmess://', '', $line)), true);
    $str = "{$v['tls']}://{$v['host']}:{$v['port']}{$v['path']}::{$v['id']}";
    echo $str . PHP_EOL;
}

function parseName(string $host): string
{
    $host2 = explode('.', $host)[0];
    if (strpos($host2, 'us') === 0) {
        $name = '美国🇺🇸-' . substr($host2, 2);
    } elseif (strpos($host2, 'hk') === 0) {
        $name = '香港🇭🇰-' . substr($host2, 2);
    } elseif (strpos($host2, 'jp') === 0) {
        $name = '日本🇯🇵-' . substr($host2, 2);
    } elseif (strpos($host2, 'sg') === 0 || strpos($host2, 'ap') === 0) {
        $name = '新加坡🇸🇬-' . substr($host2, 2);
    } elseif (strpos($host2, 'uk') === 0) {
        $name = '英国🇬🇧-' . substr($host2, 2);
    } elseif (strpos($host2, 'eu') === 0) {
        $name = '欧洲-' . substr($host2, 2);
    } elseif (strpos($host2, 'in') === 0) {
        $name = '印度🇮🇳-' . substr($host2, 2);
    } elseif (strpos($host2, 'au') === 0) {
        $name = '澳洲🇦🇺-' . substr($host2, 2);
    } elseif (strpos($host2, 'ca') === 0) {
        $name = '加拿大🇨🇦-' . substr($host2, 2);
    } elseif (strpos($host2, 'cn') === 0) {
        $name = '中国🇨🇳-' . substr($host2, 2);
    } else {
        $name = $host2;
    }
    return $name;
}

