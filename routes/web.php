<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/storage-link', function () {
    $targetFolder = storage_path('app/public');
    $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
    symlink($targetFolder, $linkFolder);
});

Route::get('/migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return response()->json([
            'status' => 'success',
            'message' => 'Migration ran successfully',
            'output' => \Illuminate\Support\Facades\Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Migration failed',
            'error' => $e->getMessage()
        ]);
    }
});

Route::get('/db-seed', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        return response()->json([
            'status' => 'success',
            'message' => 'All database seeds ran successfully',
            'output' => \Illuminate\Support\Facades\Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Seeding failed',
            'error' => $e->getMessage()
        ]);
    }
});

// ─────────────────────────────────────────────
// Auto-deploy webhook — called by GitHub on push
// POST /deploy?token=YOUR_SECRET_TOKEN
// ─────────────────────────────────────────────
Route::post('/deploy', function (\Illuminate\Http\Request $request) {

    // 1. Simple token auth (set DEPLOY_SECRET in your .env)
    $secret = env('DEPLOY_SECRET', 'change-me-please');
    if ($request->query('token') !== $secret) {
        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
    }

    $basePath = base_path();
    $output   = [];

    // 2. Pull latest code
    $output['git_pull'] = shell_exec("cd \"{$basePath}\" && git pull origin HEAD 2>&1");

    // 3. Install / update composer dependencies (no dev on production)
    $output['composer'] = shell_exec("cd \"{$basePath}\" && composer install --no-dev --optimize-autoloader 2>&1");

    // 4. Run migrations
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $output['migrate'] = \Illuminate\Support\Facades\Artisan::output();

    // 5. Clear & re-cache config/routes/views
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    $output['cache_clear'] = \Illuminate\Support\Facades\Artisan::output();

    \Illuminate\Support\Facades\Artisan::call('config:cache');
    \Illuminate\Support\Facades\Artisan::call('route:cache');
    \Illuminate\Support\Facades\Artisan::call('view:cache');
    $output['cache'] = 'config + route + view cached';

    return response()->json([
        'status'    => 'success',
        'message'   => 'Deployment completed!',
        'timestamp' => now()->toDateTimeString(),
        'output'    => $output,
    ]);
});

Route::get('/git', function () {
    try {
        $basePath = base_path();

        $status  = shell_exec("cd \"{$basePath}\" && git status 2>&1");
        $branch  = shell_exec("cd \"{$basePath}\" && git rev-parse --abbrev-ref HEAD 2>&1");
        $log     = shell_exec("cd \"{$basePath}\" && git log --oneline -10 2>&1");
        $remote  = shell_exec("cd \"{$basePath}\" && git remote -v 2>&1");

        return response()->json([
            'status'        => 'success',
            'branch'        => trim($branch ?? 'unknown'),
            'git_status'    => $status,
            'recent_commits'=> $log,
            'remotes'       => $remote,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to get git info',
            'error'   => $e->getMessage()
        ]);
    }
});

Route::get('/clear-cache', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return response()->json([
            'status' => 'success',
            'message' => 'Cache cleared successfully!',
            'output' => \Illuminate\Support\Facades\Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Cache clearing failed',
            'error' => $e->getMessage()
        ]);
    }
});
