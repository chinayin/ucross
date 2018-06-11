#!/bin/bash

echo 'Gen gfwlist2pac ..' && cd ../gfwlist2pac && sh gen_ucross.sh && \
echo 'Copy ..' && cp -f dist/ucross.txt ../ucross-dist/ucross.txt && echo 'Copy Success' && \
cd ../ucross-dist && \
echo 'Upload ..' && \
echo 'git add' && \
git add gfwlist.txt && \
git add ucross.txt && \
echo 'git commit' && \
git commit -m 'update' && \
echo 'git push' && \
git push && \
echo 'Success'