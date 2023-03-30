<?php

namespace Deployer;

desc('Lock the previous release with the maintenance flag');
task('deploy:previous', function () {
    $releases = get('releases_list');
    if ($releases[1]) {
        run("{{php}} {{deploy_path}}/releases/{$releases[1]}{{magento_dir}}{{magento_bin}} maintenance:enable {{verbose}}");
    }
});

desc('Restart nginx, varnish, php7.4-fpm');
task('deploy:restart:systemd', function () {
    if (get('restart_systemd')) {
        run("sudo systemctl restart nginx varnish php7.4-fpm");
    } else {
        write("Not restarting systemd services");
    }
});

