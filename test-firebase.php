<?php
require 'vendor/autoload.php';

try {
    $factory = (new Kreait\Firebase\Factory)
        ->withServiceAccount(storage_path('app/firebase/credentials.json'));
    
    $auth = $factory->createAuth();
    echo "✅ Firebase connection SUCCESS!\\n";
    echo "Project ID: " . $auth->getApiClient()->getProjectId() . "\\n";
    
} catch (Exception $e) {
    echo "❌ Firebase connection FAILED: " . $e->getMessage() . "\\n";
    echo "File path: " . storage_path('app/firebase/credentials.json') . "\\n";
}