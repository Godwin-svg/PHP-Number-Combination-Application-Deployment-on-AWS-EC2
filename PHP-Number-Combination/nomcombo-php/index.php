<?php
require 'vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Custom function to generate a single random string
function generateRandomString($characters, $length) {
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $randomIndex = random_int(0, strlen($characters) - 1);
        $result .= $characters[$randomIndex];
    }
    return $result;
}

// Function to generate the HTML form
function getFormHtml($username = '', $stringLength = '', $numCombinations = '', $results = 'No results generated.') {
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NomCombo</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { text-align: center; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; margin-top: 10px; border: none; border-radius: 4px; cursor: pointer; }
        #generate { background-color: #007bff; color: white; }
        #clear { background-color: #6c757d; color: white; }
        #results { margin-top: 20px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>NomCombo</h1>
    <form method="POST" action="">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="$username" required>

        <label for="stringLength">String Length</label>
        <input type="number" id="stringLength" name="stringLength" value="$stringLength" min="1" required>

        <label for="numCombinations">Number of Combinations</label>
        <input type="number" id="numCombinations" name="numCombinations" value="$numCombinations" min="1" required>

        <button id="generate" type="submit">Generate</button>
    </form>
    <form method="GET" action="">
        <button id="clear" type="submit">Clear Results</button>
    </form>
    <div id="results">$results</div>
</body>
</html>
HTML;
}

// Handle the request
$request = Request::createFromGlobals();

if ($request->getMethod() === 'POST') {
    $data = $request->request->all();
    $username = $data['username'] ?? '';
    $stringLength = $data['stringLength'] ?? '';
    $numCombinations = $data['numCombinations'] ?? '';

    // Validate inputs
    if (empty($username) || empty($stringLength) || empty($numCombinations)) {
        $response = new Response('Please fill in all fields.', 400);
    } else {
        $length = (int)$stringLength;
        $count = (int)$numCombinations;

        if ($length < 1 || $count < 1) {
            $response = new Response('String length and number of combinations must be positive numbers.', 400);
        } else {
            // Cap the number of combinations to prevent overload
            $maxCombinations = min($count, 1000);
            if ($count > $maxCombinations) {
                error_log("Warning: Requested $count combinations, capped to $maxCombinations");
            }

            $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $results = [];
            $existing = [];

            try {
                while (count($results) < $maxCombinations) {
                    $randomString = generateRandomString($characters, $length);
                    $combination = $username . $randomString;
                    if (!in_array($combination, $existing)) {
                        $results[] = $combination;
                        $existing[] = $combination;
                    }
                }
            } catch (Exception $e) {
                error_log("Error generating combinations: " . $e->getMessage());
                $response = new Response('Error generating combinations. Please try smaller values.', 500);
            }

            $response = new Response(getFormHtml($username, $stringLength, $numCombinations, implode("\n", $results)));
        }
    }
} else {
    // Serve the initial form for GET requests (including clear)
    $response = new Response(getFormHtml());
}

$response->send();