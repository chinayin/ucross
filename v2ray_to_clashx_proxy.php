<?php

$v2rayFile = 'uufly.txt';
$clashFile = 'clashx-uufly.yaml';

$r = [
    'proxies' => [],
];
$proxies = [];
foreach (explode(PHP_EOL, base64_decode(file_get_contents($v2rayFile))) as $line) {
    $proxy = null;
    if (str_starts_with($line, 'vmess://')) {
        $v = json_decode(base64_decode(str_replace('vmess://', '', $line)), true);
        $proxy = createVmessConfig($v);
    } elseif (str_starts_with($line, 'trojan://')) {
        $v = parse_url($line);
        $proxy = createTrojanConfig($v);
    }
    if (!empty($proxy)) {
        $proxies[] = $proxy;
    }
}
$r['proxies'] = $proxies;
$rrr = yaml_emit($r, YAML_UTF8_ENCODING, YAML_CRLN_BREAK);
yaml_emit_file($clashFile, $r, YAML_UTF8_ENCODING);


function createVmessConfig(array $v): array
{
    $proxy = [
        'name' => $v['ps'],
        'server' => $v['host'],
        'port' => $v['port'],
        'type' => 'vmess',
        'uuid' => $v['id'],
        'alterId' => (string)$v['aid'],
        'cipher' => 'auto',
        'tls' => $v['tls'] === 'tls',
        'network' => $v['net'],
    ];
    if ($proxy['network'] === 'ws') {
        $proxy['ws-opts'] = [
            'path' => $v['path'],
            'headers' => ['Host' => $v['host']]
        ];
    }
    return $proxy;
}

function createTrojanConfig(array $v): array
{
    parse_str($v['query'], $params);
    return [
        'name' => urldecode($v['fragment']),
        'type' => 'trojan',
        'server' => $v['host'],
        'port' => $v['port'],
        'password' => $v['user'],
        'sni' => $params['sni'],
        'udp' => true,
        'alpn' => ['h2', 'http/1.1'],
    ];
}

function str_starts_with($haystack, $needle)
{
    return strncmp($haystack, $needle, strlen($needle)) === 0;
}