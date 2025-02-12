#!/usr/bin/env bash

set -euo pipefail
set -x

shellcheck ./**.sh

DATABASE_PATH=.tmp/cmsdb-tests/
function do_db() {
    ./db.sh -p "${DATABASE_PATH}" "$@"
}

./db.sh stop || true

rm -r "${DATABASE_PATH}" || true
do_db start

function on_exit() {
    popd || true
    
    if [[ -n ${PHP_PID+x} ]]; then
        kill $PHP_PID
    fi

    do_db stop
}

pushd frontend
    tsc
    npx tsx gen-modules-run.ts
    npm run build
popd

rm -f "$(php -r "echo sys_get_temp_dir();")/cms-routes"

pushd backend
    ./vendor/bin/phpstan
    ./vendor/bin/phpunit
    ./vendor/bin/ecs --fix
    
    ./vendor/bin/doctrine-migrations migrate --verbose --no-interaction

    php bin/console.php test-support:populate-test-data

    php -S localhost:7979 -t public/ > ../.tmp/php-tests.log 2>&1 &

    PHP_PID=$!
popd

trap on_exit EXIT

pushd tests
    npm run test
popd
