#!/bin/bash

source .env
dist_directory="dist"

if [ -z "$ROUTE_DOMAIN" ]; then
  (echo "The ROUTE_DOMAIN variable is not set in .env file." && exit 1)
fi

echo 'Starting build process...'

if ! php v2ray_to_file.php || ! php v2ray_to_clashx_proxy.php; then
  (echo "Failed to execute one or more PHP scripts." && exit 1)
fi

rm -rf $dist_directory
mkdir -p $dist_directory

cp -rf uufly.txt uufly.yaml clashx-uufly.yaml clash-rules $dist_directory/
if [ $? -ne 0 ]; then
  (echo "Failed to copy files" && exit 1)
fi

if [[ "$(uname)" == "Darwin" ]]; then
    sed -i '' "s/ucross.netlify.app/$ROUTE_DOMAIN/g" $dist_directory/uufly.yaml
else
    sed -i "s/ucross.netlify.app/$ROUTE_DOMAIN/g" $dist_directory/uufly.yaml
fi

echo 'Build successful.'