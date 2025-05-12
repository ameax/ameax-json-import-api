<?php

namespace Ameax\AmeaxJsonImportApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi
 */
class AmeaxJsonImportApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ameax-json-import-api';
    }
}
