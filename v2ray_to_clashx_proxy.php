<?php

$r = [
    'proxies' => [],
];

$proxies = [];
foreach (explode(PHP_EOL, base64_decode(file_get_contents('v2ray.txt'))) as $line) {
    $v = json_decode(base64_decode(str_replace('vmess://', '', $line)), true);
    $proxy = [
        'name' => $v['ps'],
        'server' => $v['host'],
        'port' => $v['port'],
        'type' => 'vmess',
        'uuid' => $v['id'],
        'alterId' => (string)$v['aid'],
        'cipher' => 'auto',
        'tls' => $v['tls'] === 'tls' ? true : false,
        'network' => $v['net'],
    ];
    if ($proxy['network'] === 'ws') {
        $proxy['ws-opts'] = [
            'path' => $v['path'],
            'headers' => ['Host' => $v['host']]
        ];
    }
    $proxies[] = $proxy;
}
$r['proxies'] = $proxies;
$rrr = yaml_emit($r,YAML_UTF8_ENCODING,YAML_CRLN_BREAK);
yaml_emit_file('clashx-ucross.yaml', $r, YAML_UTF8_ENCODING);
