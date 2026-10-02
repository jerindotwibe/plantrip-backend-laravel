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
