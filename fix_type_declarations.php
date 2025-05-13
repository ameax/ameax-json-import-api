<?php

// This script adds type declarations to method parameters that are missing them

$modelDir = __DIR__.'/src/Models';
$files = glob($modelDir.'/*.php');

$typeMapping = [
    '$customerNumber' => 'string|int|null',
    '$externalId' => 'string|int|null',
    '$value' => 'mixed',
    '$default' => 'mixed',
    '$data' => 'array',
    '$meta' => 'Meta',
    '$identifiers' => 'Identifiers',
    '$address' => 'Address',
    '$communications' => 'Communications',
    '$agent' => 'Agent',
    '$socialMedia' => 'SocialMedia',
    '$businessInfo' => 'BusinessInformation',
    '$employment' => 'Employment',
    '$contact' => 'Contact',
    '$apiClient' => 'AmeaxJsonImportApi',
    '$name' => 'string',
    '$additionalName' => 'string',
    '$firstName' => 'string',
    '$lastName' => 'string',
    '$email' => 'string',
    '$phone' => 'string',
    '$postalCode' => 'string',
    '$locality' => 'string',
    '$country' => 'string',
    '$route' => 'string',
    '$street' => 'string',
    '$houseNumber' => 'string',
    '$phoneNumber' => 'string',
    '$mobilePhone' => 'string',
    '$fax' => 'string',
    '$website' => 'string',
    '$vatId' => 'string',
    '$iban' => 'string',
    '$version' => 'string',
    '$honorifics' => 'string',
    '$salutation' => 'string',
    '$key' => 'string',
    '$url' => 'string',
    '$status' => 'array',
    '$type' => 'string',
    '$dateOfBirth' => 'string|\DateTime|mixed',
    '$jobTitle' => 'string',
    '$department' => 'string',
    '$documentType' => 'string',
    '$contactData' => 'array',
    '$metaData' => 'array',
    '$identifiersData' => 'array',
    '$communicationsData' => 'array',
    '$businessInfoData' => 'array',
    '$socialMediaData' => 'array',
    '$employmentData' => 'array',
    '$additionalData' => 'array',
];

$methodReturnTypes = [
    'getCustomerNumber' => '?string',
    'getExternalId' => '?string',
    'getName' => '?string',
    'getAdditionalName' => '?string',
    'getFirstName' => '?string',
    'getLastName' => '?string',
    'getEmail' => '?string',
    'getPhone' => '?string',
    'getMobilePhone' => '?string',
    'getFax' => '?string',
    'getWebsite' => '?string',
    'getVatId' => '?string',
    'getIban' => '?string',
    'getSalutation' => '?string',
    'getHonorifics' => '?string',
    'getJobTitle' => '?string',
    'getDepartment' => '?string',
    'getDateOfBirth' => '?string',
    'getDocumentType' => 'string',
    'getSchemaVersion' => 'string',
    'getImportStatus' => '?array',
    'getMeta' => 'Meta',
    'getIdentifiers' => '?Identifiers',
    'getAddress' => '?Address',
    'getCommunications' => '?Communications',
    'getAgent' => '?Agent',
    'getSocialMedia' => '?SocialMedia',
    'getBusinessInformation' => '?BusinessInformation',
    'getEmployment' => '?Employment',
    'getContacts' => 'array',
    'getCustomData' => 'array',
    'get' => 'mixed',
    'toArray' => 'array',
    'sendToAmeax' => 'array',
];

foreach ($files as $file) {
    echo "Processing $file...\n";

    $content = file_get_contents($file);
    $modified = false;

    // Add missing parameter type declarations
    foreach ($typeMapping as $paramName => $paramType) {
        $pattern = '/function\s+(\w+)\s*\([^)]*'.preg_quote($paramName).'\s*[,\)]/';
        preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);

        if (! empty($matches[0])) {
            foreach ($matches[0] as $key => $match) {
                $methodName = $matches[1][$key][0];
                $matchPos = $match[1];
                $matchStr = $match[0];

                // Skip if parameter already has a type declaration
                if (preg_match('/function\s+\w+\s*\([^)]*'.$paramType.'\s+'.preg_quote($paramName).'\s*[,\)]/', $matchStr)) {
                    continue;
                }

                // Add the type declaration
                $newMatchStr = str_replace($paramName, $paramType.' '.$paramName, $matchStr);
                $content = substr_replace($content, $newMatchStr, $matchPos, strlen($matchStr));
                $modified = true;

                // Adjust future match positions (if any)
                $offset = strlen($newMatchStr) - strlen($matchStr);
                for ($i = $key + 1; $i < count($matches[0]); $i++) {
                    $matches[0][$i][1] += $offset;
                }
            }
        }
    }

    // Add missing return type declarations
    foreach ($methodReturnTypes as $methodName => $returnType) {
        $pattern = '/function\s+'.$methodName.'\s*\([^)]*\)\s*(?!:)/';
        $replacement = 'function '.$methodName.'($1): '.$returnType.' ';
        $content = preg_replace($pattern, $replacement, $content, -1, $count);
        if ($count > 0) {
            $modified = true;
        }
    }

    if ($modified) {
        file_put_contents($file, $content);
        echo "Updated $file\n";
    } else {
        echo "No changes needed for $file\n";
    }
}

echo "Done!\n";
