<?php

namespace Deployer;

desc('Compile Magento DI');
task('magento:compile', function () {
    if (get('is_production') || get('compile_UAT')) {
        run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} setup:di:compile {{verbose}}");
    } else {
        write("Not running the DI Compile for UAT");
    }
});

desc('Deploy assets');
task('magento:deploy:assets', function () {
    if (get('is_production')) {
        run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} setup:static-content:deploy {{languages}} {{verbose}}");
    } elseif (get('compile_UAT')) {
        run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} setup:static-content:deploy {{languages}} --force {{verbose}}");
    } else {
        write("Not running the Static Content deploy for UAT");
    }
});

desc('Bundle assets');
task('magento:deploy:bundling', function () {
    if (get('bundle')){
        run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} config:set dev/js/enable_js_bundling 0");
        run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} config:set dev/js/minify_files 0");
        run("cd {{release_path}}{{magento_dir}} && magepack bundle -m -s");
        run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} config:set dev/js/enable_magepack_js_bundling 1");
    } else {
        run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} config:set dev/js/minify_files 1");
        run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} config:set dev/js/enable_magepack_js_bundling 0");
    }

});

desc('Enable maintenance mode');
task('magento:maintenance:enable', function () {
    run("if [ -d $(echo {{release_path}}{{magento_dir}}bin) ]; then cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} maintenance:enable {{verbose}}; fi");
});

desc('Disable maintenance mode');
task('magento:maintenance:disable', function () {
    run("if [ -d $(echo {{release_path}}{{magento_dir}}bin) ]; then cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} maintenance:disable {{verbose}}; fi");
});

desc('Flush Magento Cache');
task('magento:cache:flush', function () {
    run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} cache:flush {{verbose}}");
    run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} cache:clean {{verbose}}");
});

desc('Remove the content of the generated folder');
task('magento:clean:generated', function () {
    run("cd {{release_path}}; rm -rf generated/*");
});

desc('Set deploy mode set');
task('magento:deploy:mode:set', function () {
    if (get('is_production')) {
        run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} deploy:mode:set production --skip-compilation {{verbose}}");
    } else {
        run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} deploy:mode:set developer {{verbose}}");
    }
});

desc('Set right permissions to folders and files');
task('magento:setup:permissions', function () {
    // run("find {{release_path}}{{magento_dir}} -type d -exec chmod 755 {} \;");
    // run("find {{release_path}}{{magento_dir}} -type f -exec chmod 644 {} \;");
    run("chmod -R 755 {{release_path}}");
    run("cd {{release_path}}{{magento_dir}} && chmod -R 775 var");
    run("cd {{release_path}}{{magento_dir}} && chmod -R 775 generated");
    run("cd {{release_path}}{{magento_dir}} && chmod -R 775 pub/static");
    run("cd {{release_path}}{{magento_dir}} && chmod +x {{magento_bin}}");
});

desc('Reindex index');
task('magento:reindex:index', function () {
    run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} index:reindex");
});

desc('Set caching app 2');
task('magento:cache:set:app', function () {
    run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} config:set system/full_page_cache/caching_application 2");
});

desc('Magento config set cache hosts');
task('magento:cache:set:hosts', function () {
    run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} setup:config:set --http-cache-hosts=127.0.0.1:6081");
});

desc('Enable syslogging');
task('magento:syslogging:enable', function () {
    run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} setup:config:set --enable-syslog-logging=true");
});

desc('Install magento cron');
task('magento:cron:install', function () {
    run("cd {{release_path}}{{magento_dir}} && {{php}} {{magento_bin}} cron:install --force");
});

desc('Remove magento cron');
task('magento:cron:remove', function () {
    run("cd {{deploy_path}}/current && {{php}} {{magento_bin}} cron:remove");
});

desc('Copy Quick Order View Edit');
task('magento:copy:quickorder:view', function () {
    run("cd {{release_path}}{{magento_dir}} && cp overrides/QuickOrderViewEdit/order_view.html vendor/mirasvit/module-order-management/src/QuickView/view/adminhtml/web/template/component/order_view.html");
});

desc('Copy Better Order Comments View Edit');
task('magento:copy:betterorder:view', function () {
    run("cd {{release_path}}{{magento_dir}} && cp overrides/BetterOrderComments/form-content.html vendor/boldcommerce/magento2-ordercomments/view/frontend/web/template/checkout/form-content.html");
});