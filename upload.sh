#!/bin/bash

echo 'Upload ..'
echo 'git add' && \
git add gfwlist.txt && \
git add ucross.txt && \
echo 'git commit' && \
git commit -m 'update' && \
echo 'git push' && \
git push && \
echo 'Success'