#!/usr/bin/env bash

set -euo pipefail
set -x

rm -f $(php -r "echo sys_get_temp_dir();")/cms-routes
pushd frontend
    tsc
    npx tsx gen-modules-run.ts
    npm run dev &
    VITE_PID=$!
popd
pushd backend

./vendor/bin/phpstan
./vendor/bin/phpunit
./vendor/bin/ecs --fix

php -S localhost:7979 -t public/
function on_exit {
    popd
    kill $VITE_PID
}

trap on_exit EXIT
