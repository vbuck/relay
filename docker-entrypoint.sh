#!/usr/bin/env sh

set -e
HOST="0.0.0.0"
PORT="8888"
ROOT=$(pwd)
ROUTER=$(pwd)/web/router.php

[[ ! -d vendor ]] && composer install
[[ ! -z "$1" ]] && HOST="$1"
[[ ! -z "$2" ]] && PORT="$2"
[[ ! -z "$3" ]] && ROOT="$3"
[[ ! -z "$4" ]] && ROUTER="$4"

vendor/bin/webserver start $HOST:$PORT $ROOT $ROUTER &
tail -f /dev/null
