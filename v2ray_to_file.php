<?php

const PATH_PREFIX = '/crm##alias##';
const V2RAY_FILE = 'uufly.txt';
const DOMAIN = 'xxx.com';

$array = [
    // 新加坡
    // 日本
    // 韩国
    // 美国
    // 英国
    // 澳洲
    // 印度
    // 香港
//    ['alias' => 'au08', 'id' => '', 'type' => 'vmess', 'path' => '/swoole'],
//    ['alias' => 'au10', 'id' => '', 'type' => 'trojan', 'server' => ''],
];

$list = [];
foreach ($array as $item) {
    $type = $item['type'] ?? 'vmess';
    if ($type === 'vmess') {
        $list[] = createVmessConfig($item);
    } elseif ($type === 'trojan') {
        $list[] = createTrojanConfig($item);
    }
}

$str = implode(PHP_EOL, array_values($list));
$base64 = base64_encode($str);
echo $base64 . PHP_EOL;
file_put_contents(V2RAY_FILE, $base64);
// 复查
foreach (explode(PHP_EOL, base64_decode(file_get_contents(V2RAY_FILE))) as $line) {
//    var_dump(base64_decode(str_replace('vmess://', '', $line)));
    if (str_starts_with($line, 'vmess://')) {
        $v = json_decode(base64_decode(str_replace('vmess://', '', $line)), true);
        $str = "vmess://{$v['host']}:{$v['port']}{$v['path']}::{$v['id']}";
    } elseif (str_starts_with($line, 'trojan://')) {
        $v = parse_url($line);
        $str = "{$v['scheme']}://{$v['host']}:{$v['port']}{$v['path']}::{$v['user']}";
    }
    echo $str . PHP_EOL;
}

function createVmessConfig(array $item): string
{
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
        // 多路复用
        "mux" => 1,
    ];
    $alias = $item['alias'] ?? '';
    $host = "{$item['alias']}." . DOMAIN;
    $name = parseHostName($host, $item['ai'] ?? false);
    echo "alias: $alias, name: $name" . PHP_EOL;
    $item['ps'] = $name;
    $item['host'] = $host;
    $item['add'] = $host;
    // path自动生成的特殊处理
    if (!empty(PATH_PREFIX) && empty($item['path'])) {
        $item['path'] = str_replace(['##alias##'], [$alias], PATH_PREFIX);
    }
    $c = array_merge($defaultHostOpts, $item);
    return 'vmess://' . base64_encode(json_encode($c, JSON_UNESCAPED_UNICODE));
}

function createTrojanConfig(array $item): string
{
    $defaultHostOpts = [
        // 节点域名
        "host" => '',
        "port" => 12308,
        "id" => '',
    ];
    $params = [
        'sni' => '',
        'mux' => 1,
        'alpn' => 'h2,http/1.1'
    ];
    $alias = $item['alias'] ?? '';
    $host = "{$item['alias']}." . DOMAIN;
    $name = parseHostName($host, $item['ai'] ?? false);
    echo "alias: $alias, name: $name" . PHP_EOL;
    $c = array_merge($defaultHostOpts, $item);
    $params['sni'] = $host;
    return "trojan://{$c['id']}@{$c['server']}:{$c['port']}/?" . http_build_query($params) . "#" . urlencode($name);
}

function parseHostName(string $domain, bool $isAI): string
{
    $emojiAI = '🤖';
    $emojiCountry = [
        'us' => ['name' => '美国', 'emoji' => '🇺🇸'],
        'hk' => ['name' => '香港', 'emoji' => '🇭🇰'],
        'jp' => ['name' => '日本', 'emoji' => '🇯🇵'],
        'kr' => ['name' => '韩国', 'emoji' => '🇰🇷'],
        'sg' => ['name' => '新加坡', 'emoji' => '🇸🇬'],
        'ap' => ['name' => '新加坡', 'emoji' => '🇸🇬'],
        'uk' => ['name' => '英国', 'emoji' => '🇬🇧'],
        'in' => ['name' => '印度', 'emoji' => '🇮🇳'],
        'au' => ['name' => '澳大利亚', 'emoji' => '🇦🇺'],
        'ca' => ['name' => '加拿大', 'emoji' => '🇨🇦'],
        'cn' => ['name' => '中国', 'emoji' => '🇨🇳'],
        'eu' => ['name' => '欧洲', 'emoji' => ''],
    ];
    $host = explode('.', $domain)[0];
    $code = substr($host, 0, 2);
    $name = $host;
    if (array_key_exists($code, $emojiCountry)) {
        $c = $emojiCountry[$code];
        $name = "{$c['name']}{$c['emoji']}-" . substr($host, 2);
    }
    if ($isAI) {
        $name .= " {$emojiAI}";
    }
    return $name;
}

function str_starts_with($haystack, $needle)
{
    return strncmp($haystack, $needle, strlen($needle)) === 0;
}
