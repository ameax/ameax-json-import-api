<?php

use Ameax\AmeaxJsonImportApi\Models\Meta;

test('meta can set and get import mode', function () {
    $meta = new Meta();
    
    // Test default (null)
    expect($meta->getImportMode())->toBeNull();
    
    // Test setting valid import modes
    $meta->setImportMode(Meta::IMPORT_MODE_CREATE_OR_UPDATE);
    expect($meta->getImportMode())->toBe('create_or_update');
    
    $meta->setImportMode(Meta::IMPORT_MODE_CREATE_ONLY);
    expect($meta->getImportMode())->toBe('create_only');
    
    $meta->setImportMode(Meta::IMPORT_MODE_UPDATE_ONLY);
    expect($meta->getImportMode())->toBe('update_only');
    
    // Test setting null
    $meta->setImportMode(null);
    expect($meta->getImportMode())->toBeNull();
});

test('meta throws exception for invalid import mode', function () {
    $meta = new Meta();
    
    expect(fn() => $meta->setImportMode('invalid_mode'))
        ->toThrow(InvalidArgumentException::class, 'Import mode must be one of: create_or_update, create_only, update_only, got: invalid_mode');
});

test('meta includes import mode in toArray output', function () {
    $meta = new Meta();
    $meta->setImportMode(Meta::IMPORT_MODE_CREATE_ONLY);
    
    $data = $meta->toArray();
    
    expect($data)->toHaveKey('import_mode')
        ->and($data['import_mode'])->toBe('create_only');
});

test('meta can be created from array with import mode', function () {
    $data = [
        'document_type' => Meta::DOCUMENT_TYPE_ORGANIZATION,
        'schema_version' => '1.0',
        'import_mode' => 'update_only',
    ];
    
    $meta = Meta::fromArray($data);
    
    expect($meta->getImportMode())->toBe('update_only')
        ->and($meta->getDocumentType())->toBe(Meta::DOCUMENT_TYPE_ORGANIZATION)
        ->and($meta->getSchemaVersion())->toBe('1.0');
});