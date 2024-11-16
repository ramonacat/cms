#!/usr/bin/env bash

set -euo pipefail
set -x

rm -f $(php -r "echo sys_get_temp_dir();")/cms-routes
pushd backend
./vendor/bin/phpstan
./vendor/bin/phpunit
./vendor/bin/ecs --fix

php -S localhost:7979 -t public/
function on_exit {
    popd
}

trap on_exit EXIT
