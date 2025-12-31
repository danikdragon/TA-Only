<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function testRequest($desc, $email, $params, $expectedCount, $mustHave = [], $mustNotHave = []) {
    echo "\n--- TEST: $desc ---\n";
    
    $user = User::where('email', $email)->first();
    if (!$user) {
        echo "ERROR: User $email not found.\n";
        return;
    }
    
    $request = Illuminate\Http\Request::create('/api/cars/available', 'GET', $params);
    $request->setUserResolver(function () use ($user) { return $user; });

    $formRequest = \App\Http\Requests\GetAvailableCarsRequest::createFrom($request);
    $formRequest->setContainer(app());
    $formRequest->setUserResolver($request->getUserResolver());

    $controller = app(\App\Http\Controllers\Api\CarAvailabilityController::class);
    
    try {
        $validator = app(\Illuminate\Validation\Factory::class)->make($params, $formRequest->rules());
        if ($validator->fails()) throw new \Illuminate\Validation\ValidationException($validator);
        
        $formRequest->setValidator($validator);

        $response = $controller->index($formRequest);
        
        // Handle both JsonResponse and ResourceCollection
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $json = $response->getData(true);
        } else {
            // It's likely a ResourceCollection
            $json = $response->response()->getData(true);
        }

        // Check if 'data' wrapper exists, if not, assume the root is the data
        $data = $json['data'] ?? $json;

        $count = count($data);
        echo "User: $email\n";
        echo "Found Cars: $count\n";

        if ($count === $expectedCount) {
            echo "✅ Count OK\n";
        } else {
            echo "❌ Count FAIL (Expected $expectedCount, got $count)\n";
            // Print actual data for debugging if count mismatch
            foreach ($data as $d) echo "   Found: " . ($d['model'] ?? 'unknown') . "\n";
        }

        foreach ($mustHave as $model) {
            $found = false;
            foreach ($data as $car) if (isset($car['model']) && str_contains($car['model'], $model)) $found = true;
            echo $found ? "✅ Found required: $model\n" : "❌ MISSING required: $model\n";
        }

        foreach ($mustNotHave as $model) {
            $found = false;
            foreach ($data as $car) if (isset($car['model']) && str_contains($car['model'], $model)) $found = true;
            echo !$found ? "✅ Correctly hidden: $model\n" : "❌ SHOULD NOT BE HERE: $model\n";
        }

    } catch (\Exception $e) {
        echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
        if ($e instanceof \Illuminate\Validation\ValidationException) print_r($e->errors());
    }
}

// 1. Manager Morning
testRequest(
    "Manager (Morning - 09:00-10:00)", 
    "manager@example.com", 
    ['start_time' => '2025-12-31 09:00:00', 'end_time' => '2025-12-31 10:00:00'],
    2,
    ['Lada', 'BMW'],
    []
);

// 2. Manager Afternoon
testRequest(
    "Manager (Afternoon - 15:00-16:00 - BMW Busy)", 
    "manager@example.com", 
    ['start_time' => '2025-12-31 15:00:00', 'end_time' => '2025-12-31 16:00:00'],
    1,
    ['Lada'],
    ['BMW']
);

// 3. Junior Morning
testRequest(
    "Junior (Morning - 09:00-10:00 - Restricted Access)", 
    "junior@example.com", 
    ['start_time' => '2025-12-31 09:00:00', 'end_time' => '2025-12-31 10:00:00'],
    1,
    ['Lada'],
    ['BMW']
);
