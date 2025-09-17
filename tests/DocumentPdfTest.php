<?php

use Ameax\AmeaxJsonImportApi\Models\DocumentPdf;

it('can create document pdf from base64', function () {
    $content = 'JVBERi0xLjQK';
    $pdf = DocumentPdf::fromBase64($content);

    expect($pdf->getType())->toBe('base64');
    expect($pdf->getContent())->toBe($content);
    expect($pdf->getUrl())->toBeNull();
});

it('can create document pdf from url', function () {
    $url = 'https://example.com/invoice.pdf';
    $pdf = DocumentPdf::fromUrl($url);

    expect($pdf->getType())->toBe('url');
    expect($pdf->getUrl())->toBe($url);
    expect($pdf->getContent())->toBeNull();
});

it('can create document pdf from array with base64', function () {
    $data = [
        'type' => 'base64',
        'content' => 'JVBERi0xLjQK',
    ];

    $pdf = DocumentPdf::fromArray($data);

    expect($pdf->getType())->toBe('base64');
    expect($pdf->getContent())->toBe('JVBERi0xLjQK');
});

it('can create document pdf from array with url', function () {
    $data = [
        'type' => 'url',
        'url' => 'https://example.com/invoice.pdf',
    ];

    $pdf = DocumentPdf::fromArray($data);

    expect($pdf->getType())->toBe('url');
    expect($pdf->getUrl())->toBe('https://example.com/invoice.pdf');
});

it('validates pdf type', function () {
    $pdf = new DocumentPdf;

    expect(fn() => $pdf->setType('invalid'))
        ->toThrow(InvalidArgumentException::class, 'Invalid PDF type. Valid types are: base64, url');
});

it('validates url format', function () {
    $pdf = new DocumentPdf;

    expect(fn() => $pdf->setUrl('invalid-url'))
        ->toThrow(InvalidArgumentException::class, 'URL must be a valid HTTPS URL');

    expect(fn() => $pdf->setUrl('http://example.com/file.pdf'))
        ->toThrow(InvalidArgumentException::class, 'URL must be a valid HTTPS URL');
});

it('converts to array correctly for base64', function () {
    $pdf = DocumentPdf::fromBase64('JVBERi0xLjQK');
    $array = $pdf->toArray();

    expect($array)->toBe([
        'type' => 'base64',
        'content' => 'JVBERi0xLjQK',
    ]);
});

it('converts to array correctly for url', function () {
    $pdf = DocumentPdf::fromUrl('https://example.com/invoice.pdf');
    $array = $pdf->toArray();

    expect($array)->toBe([
        'type' => 'url',
        'url' => 'https://example.com/invoice.pdf',
    ]);
});