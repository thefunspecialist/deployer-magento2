<?php

namespace Deployer;

// OPCache and Redis

desc('Redis cache flush');
task('redis:flush', function () {
    run("redis-cli -n 0 flushall");
});

desc('OPCache cache flush');
task('opcache:flush', function () {
    run("{{php}} -r 'opcache_reset();'");
});

desc('Restart php-fpm, varnish and nginx');
task('systemctl:restart', function () {
    run("sudo systemctl restart php7.4-fpm varnish nginx");
});

desc('Update the code via Git');
task('git:update_code', function () {
    run("cd {{release_path}}{{magento_dir}} && \
        git fetch --all --tags && \
        git reset --hard origin/{{branch}} && \
        git checkout {{branch}} && \
        git pull origin {{branch}}");
});
