<?php
// A simple webhook script to automatically pull changes from GitHub.

// 1. Change this to a random password of your choice
$secretToken = "plantrip_update_2024"; 

// 2. Security check: Only run if the token matches
if (!isset($_GET['token']) || $_GET['token'] !== $secretToken) {
    http_response_code(403);
    die("Forbidden");
}

echo "<pre>";
echo "Starting Automatic Deployment...\n\n";

// 3. Move one directory up to the main folder and run git pull
$output = shell_exec("cd .. && git pull origin main 2>&1");

echo htmlspecialchars($output);
echo "\n\nDeployment Finished!</pre>";
