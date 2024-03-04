#!/bin/bash

echo 'Start ..' && \
echo 'Build ..' && \
php v2ray_to_file.php && php v2ray_to_clashx_proxy.php && \
echo 'Upload ..' && \
git add uufly.txt uufly.yaml clashx-uufly.yaml && \
git commit -m 'update' && git push && \
echo 'Success'