#!/usr/bin/env bash

set -euo pipefail
set -x

./db.sh start
pushd frontend
    tsc
    npx tsx gen-modules-run.ts
    npm run build
popd
rm -f $(php -r "echo sys_get_temp_dir();")/cms-routes
pushd backend
./vendor/bin/phpstan
./vendor/bin/phpunit
./vendor/bin/ecs --fix

php -S localhost:7979 -t public/ &
PHP_PID=$!
popd
pushd tests
npm run test
popd
kill $PHP_PID
./db.sh stop