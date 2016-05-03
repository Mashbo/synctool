#!/usr/bin/env bash
set -o errexit -o nounset
echo "select * from remote" | mysql local
file /tmp/files/a/file
