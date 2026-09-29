<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/git-status', function () {
    if (!function_exists('shell_exec')) {
        return "Error: shell_exec() is disabled on this server. Cannot run git commands.";
    }
    
    $commit = shell_exec('git log -1 --oneline 2>&1');
    $status = shell_exec('git status 2>&1');
    
    return "<h2>Current Git Status</h2>" .
           "<pre><b>Last Commit:</b>\n" . htmlspecialchars($commit ?? 'No output') . "\n\n" .
           "<b>Status:</b>\n" . htmlspecialchars($status ?? 'No output') . "</pre>";
});
