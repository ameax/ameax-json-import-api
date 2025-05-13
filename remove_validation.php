<?php

// This script removes validation-related imports and methods from all model files
$modelDir = __DIR__.'/src/Models';
$files = glob($modelDir.'/*.php');

foreach ($files as $file) {
    echo "Processing $file...\n";

    $contents = file_get_contents($file);

    // Remove validation-related imports
    $contents = preg_replace('/^use Ameax\\\\AmeaxJsonImportApi\\\\Exceptions\\\\ValidationException;$/m', '', $contents);
    $contents = preg_replace('/^use Ameax\\\\AmeaxJsonImportApi\\\\Validation\\\\Validator;$/m', '', $contents);

    // Remove "@throws ValidationException" from documentation
    $contents = preg_replace('/@throws ValidationException.*$/m', '', $contents);

    // Remove validate method if it exists
    $contents = preg_replace('/\/\*\*\s*\n\s*\*\s*Validate the model data before saving\/sending.*?public function validate\(\): bool\s*{.*?return true;\s*}/s', '', $contents);

    // Remove Validator::* calls
    $contents = preg_replace('/\s*Validator::\w+\([^;]*\);/m', '', $contents);

    // Save the modified file back
    file_put_contents($file, $contents);
}

echo "Done!\n";
