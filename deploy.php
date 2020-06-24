<?php /** @noinspection PhpIncludeInspection */

namespace Deployer;

use Dotenv\Dotenv;

set_include_path(get_include_path() . PATH_SEPARATOR . getenv('INCLUDE_PATH'));

require_once 'autoload.php';
require_once 'deployer/deployer/recipe/laravel.php';
require_once 'deployer/recipes/recipe/slack.php';

// What is we are deploying?
$branchName = getenv('CI_COMMIT_REF_NAME');
if (in_array($branchName, ['master', 'develop'])) {
    set('application', 'dropwow.com');
    $showroomId = null;
    $deployPath = '/var/www/{{application}}';
    after('success', 'artisan:queue:restart'); // safe to use w/ horizon
} else {
    preg_match('#(?<showroomId>dk-\d+)#i', $branchName, $matches);
    !isset($matches['showroomId']) && die('Invalid branch name' . PHP_EOL);
    $showroomId = strtolower($matches['showroomId']);
    set('application', 'qa.dropwow.com');
    $deployPath = "/var/www/{{application}}/{$showroomId}";
}

// Fix user
set('user', getenv('GITLAB_USER_NAME'));
set('title', getenv('CI_COMMIT_TITLE'));

// Project repository
set('repository', 'git@git.cpadev.com:dropwow2/web-app.git');

// Hosts
host('dev-dropwow.com')
    ->user('deployer')
    ->set('branch', $branchName)
    ->set('deploy_path', $deployPath)
;

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', [
    'public/storage',
    'storage',
    'vendor'
]);

// 007
set('allow_anonymous_stats', false);

// .env
task('deploy:prepare-env', function () use ($showroomId) {
    if ($showroomId) {
        // Showrooms share single .env
        if (!$showroomDotEnv = getenv('SHOWROOM_COMMON_ENV')) {
            throw new \RuntimeException('Showroom build requested but path to common .env file is not provided');
        }
        run("cp {$showroomDotEnv} {{release_path}}");
    }

    // Deployer has access only to .env.example on fresh gitlab repo clone
    (new Dotenv(__DIR__, '.env.example'))->load();
    set('slack_webhook', getenv('SLACK_WEBHOOK'));
});
before('deploy:vendors', 'deploy:prepare-env');

// Notifications
if ($showroomId) {
    $taskId = strtoupper($showroomId);
    set('slack_text',
        "Will now prepare showroom for _{{user}}_ with `{{branch}}` on *{{target}}* for task " .
        "https://dropwow.atlassian.net/browse/{$taskId} with commit: _{{title}}_"
    );
    set('slack_success_text',
        "Successful deployment, showroom for branch `{{branch}}` is served for you at:\n" .
        "http://admitad:dropwow18@{$showroomId}.qa.dev-dropwow.com/"
    );
    after('deploy:prepare-env', 'slack:notify');
    after('success', 'slack:notify:success');
    after('deploy:failed', 'slack:notify:failure');
}

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
// Migrate database before symlink new release.
before('deploy:symlink', 'artisan:migrate');
before('deploy:symlink', 'deploy:public_disk');
