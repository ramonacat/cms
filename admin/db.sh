#!/usr/bin/env bash

while getopts "p:" opt; do
    case "$opt" in
        p) DATABASE_PATH=$OPTARG
    esac
done

shift $((OPTIND-1))

if [[ -z ${DATABASE_PATH+x} ]]; then
    DATABASE_PATH=.tmp/cmsdb/
fi

function do_pg_ctl() {
    pg_ctl -D "$DATABASE_PATH" -l .tmp/cmsdb.log -o"--unix-socket-directories='$PWD'" "$@"
}

if [[ "$1" == "start" ]]; then
    if [[ -d "$DATABASE_PATH" ]]; then
        do_pg_ctl start
    else
        mkdir -p $DATABASE_PATH
        initdb -D $DATABASE_PATH

        do_pg_ctl start
        createdb -h $PWD cms
    fi
else if [[ "$1" == "stop" ]]; then
    do_pg_ctl stop
fi fi
