<?php

/** @noinspection ALL */

namespace Deployer;

require 'recipe/symfony.php';

set('repository', 'https://github.com/ivanstan/storage');
set('git_tty', true);
set('bin_dir', 'bin');
set('http_user', 'glutenfr');
set('writable_mode', 'chmod');
set('default_stage', 'production');
set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader');
add('shared_files', [
    '.env.local',
]);
add('shared_dirs', [
    'public/data',
]);
add('writable_dirs', ['var', 'public/data']);

host('tle.ivanstanojevic.me')
    ->user('glutenfr')
    ->port(2233)
    ->stage('production')
    ->set('deploy_path', '~/projects/storage.ivanstanojevic.me');

task('test', function () {
    set('symfony_env', 'test');
    runLocally('bin/phpunit');
    set('symfony_env', 'dev');
});

task('deploy:dump-env', function () {
    run('cd {{release_path}} && {{bin/composer}} dump-env prod');
});

task('deploy:executable', function () {
    run('chmod +x {{release_path}}/bin/console');
});

task(
    'deploy',
    [
        'deploy:info',
        'deploy:prepare',
        'deploy:lock',
        'deploy:release',
        'deploy:update_code',
        'deploy:clear_paths',
        'deploy:create_cache_dir',
        'deploy:shared',
        'deploy:assets',
        'deploy:writable',
        'deploy:vendors',
        'deploy:executable',
        'deploy:cache:clear',
        'deploy:cache:warmup',
        'deploy:dump-env',
        'database:migrate',
        'deploy:symlink',
        'deploy:unlock',
        'cleanup',
    ]
);

before('deploy', 'test');
after('deploy:failed', 'deploy:unlock');
