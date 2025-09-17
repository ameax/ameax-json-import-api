<?php

use Ameax\AmeaxJsonImportApi\Models\DocumentPdf;
use Ameax\AmeaxJsonImportApi\Models\LineItem;
use Ameax\AmeaxJsonImportApi\Models\Receipt;

it('can set sale external id', function () {
    $receipt = new Receipt;

    $receipt->setSaleExternalId('SALE-001');
    expect($receipt->getSaleExternalId())->toBe('SALE-001');
});

it('can remove sale external id', function () {
    $receipt = new Receipt;

    $receipt->setSaleExternalId('SALE-001');
    expect($receipt->getSaleExternalId())->toBe('SALE-001');

    $receipt->setSaleExternalId(null);
    expect($receipt->getSaleExternalId())->toBeNull();
});

it('can create receipt from array with sale external id', function () {
    $data = [
        'type' => Receipt::TYPE_INVOICE,
        'identifiers' => [
            'receipt_number' => 'INV-2024-001',
        ],
        'date' => '2024-01-01',
        'customer_number' => 'CUST-123',
        'status' => Receipt::STATUS_COMPLETED,
        'tax_mode' => Receipt::TAX_MODE_NET,
        'tax_type' => Receipt::TAX_TYPE_REGULAR,
        'sale_external_id' => 'SALE-001',
        'line_items' => [
            [
                'description' => 'Product A',
                'quantity' => 1,
                'price' => 100.00,
                'tax_rate' => 19,
                'tax_type' => 'regular',
            ],
        ],
    ];

    $receipt = Receipt::fromArray($data);

    expect($receipt->getSaleExternalId())->toBe('SALE-001');
});

it('sale external id is optional', function () {
    $receipt = new Receipt;

    $receipt->setType(Receipt::TYPE_INVOICE)
        ->setReceiptNumber('INV-2024-001')
        ->setDate('2024-01-01')
        ->setCustomerNumber('CUST-123')
        ->setStatus(Receipt::STATUS_COMPLETED)
        ->setTaxMode(Receipt::TAX_MODE_NET)
        ->setTaxType(Receipt::TAX_TYPE_REGULAR)
        ->addLineItem(
            (new LineItem)
                ->setDescription('Product A')
                ->setQuantity(1)
                ->setPrice(100.00)
                ->setTaxRate(19)
                ->setTaxType('regular')
        );

    // Not setting sale_external_id
    $data = $receipt->toArray();

    expect($data)->not->toHaveKey('sale_external_id');
});

it('can set document pdf from base64', function () {
    $receipt = new Receipt;
    $base64Content = 'JVBERi0xLjQK'; // Sample PDF base64 content

    $receipt->setDocumentPdfFromBase64($base64Content);

    $pdf = $receipt->getDocumentPdf();
    expect($pdf)->toBeInstanceOf(DocumentPdf::class);
    expect($pdf->getType())->toBe('base64');
    expect($pdf->getContent())->toBe($base64Content);

    $data = $receipt->toArray();
    expect($data)->toHaveKey('document_pdf');
    expect($data['document_pdf']['type'])->toBe('base64');
    expect($data['document_pdf']['content'])->toBe($base64Content);
});

it('can set document pdf from url', function () {
    $receipt = new Receipt;
    $url = 'https://example.com/invoice.pdf';

    $receipt->setDocumentPdfFromUrl($url);

    $pdf = $receipt->getDocumentPdf();
    expect($pdf)->toBeInstanceOf(DocumentPdf::class);
    expect($pdf->getType())->toBe('url');
    expect($pdf->getUrl())->toBe($url);

    $data = $receipt->toArray();
    expect($data)->toHaveKey('document_pdf');
    expect($data['document_pdf']['type'])->toBe('url');
    expect($data['document_pdf']['url'])->toBe($url);
});

it('can set document pdf with DocumentPdf object', function () {
    $receipt = new Receipt;
    $pdf = DocumentPdf::fromBase64('JVBERi0xLjQK');

    $receipt->setDocumentPdf($pdf);

    expect($receipt->getDocumentPdf())->toBe($pdf);

    $data = $receipt->toArray();
    expect($data)->toHaveKey('document_pdf');
    expect($data['document_pdf']['type'])->toBe('base64');
});

it('can remove document pdf', function () {
    $receipt = new Receipt;

    $receipt->setDocumentPdfFromBase64('JVBERi0xLjQK');
    expect($receipt->getDocumentPdf())->not->toBeNull();

    $receipt->setDocumentPdf(null);
    expect($receipt->getDocumentPdf())->toBeNull();

    $data = $receipt->toArray();
    expect($data)->not->toHaveKey('document_pdf');
});

it('can create receipt from array with document pdf', function () {
    $data = [
        'type' => Receipt::TYPE_INVOICE,
        'identifiers' => [
            'receipt_number' => 'INV-2024-001',
        ],
        'date' => '2024-01-01',
        'customer_number' => 'CUST-123',
        'status' => Receipt::STATUS_COMPLETED,
        'tax_mode' => Receipt::TAX_MODE_NET,
        'tax_type' => Receipt::TAX_TYPE_REGULAR,
        'document_pdf' => [
            'type' => 'base64',
            'content' => 'JVBERi0xLjQK',
        ],
        'line_items' => [
            [
                'description' => 'Product A',
                'quantity' => 1,
                'price' => 100.00,
                'tax_rate' => 19,
                'tax_type' => 'regular',
            ],
        ],
    ];

    $receipt = Receipt::fromArray($data);

    $pdf = $receipt->getDocumentPdf();
    expect($pdf)->toBeInstanceOf(DocumentPdf::class);
    expect($pdf->getType())->toBe('base64');
    expect($pdf->getContent())->toBe('JVBERi0xLjQK');
});
