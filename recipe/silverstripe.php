<?php
namespace Deployer;

require_once __DIR__ . '/common.php';

add('recipes', ['silverstripe']);

/**
 * Silverstripe configuration
 */

set('shared_assets', function () {
    if (test('[ -d {{release_path}}/public ]') || test('[ -d {{deploy_path}}/shared/public ]')) {
        return 'public/assets';
    }
    return 'assets';
});


// Silverstripe shared dirs
set('shared_dirs', [
    '{{shared_assets}}'
]);

// Silverstripe writable dirs
set('writable_dirs', [
    '{{shared_assets}}'
]);

// Silverstripe cli script
set('silverstripe_cli_script', function () {
    $paths = [
        'framework/cli-script.php',
        'vendor/silverstripe/framework/cli-script.php'
    ];
    foreach ($paths as $path) {
        if (test('[ -f {{release_path}}/'.$path.' ]')) {
            return $path;
        }
    }
});

/**
 * Helper tasks
 */
task('silverstripe:build', function () {
    return run('{{bin/php}} {{release_path}}/{{silverstripe_cli_script}} /dev/build');
})->desc('Run /dev/build');

task('silverstripe:buildflush', function () {
    return run('{{bin/php}} {{release_path}}/{{silverstripe_cli_script}} /dev/build flush=all');
})->desc('Run /dev/build?flush=all');

/**
 * Main task
 */
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'silverstripe:buildflush',
    'deploy:publish',
])->desc('Deploy your project');

after('deploy', 'success');
