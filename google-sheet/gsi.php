<?php
require_once 'vendor/autoload.php';
require_once 'MyGoogleSheet.php';
$apiKey = '61f8703648e1384d31efd17f42ff108923ba3e93';
// configure the Google Client
$client = new \Google_Client();
$client->setApplicationName( 'Google Sheets API' );
$client->setScopes( [\Google_Service_Sheets::SPREADSHEETS] );
$client->setAccessType( 'offline' );
// credentials.json is the key file we downloaded while setting up our Google Sheets API
$path = __DIR__ . '/cred.json';
$client->setAuthConfig( $path );
// $client->setDeveloperKey( $apiKey );

// configure the Sheets Service
$service = new \Google_Service_Sheets( $client );
// the spreadsheet id can be found in the url https://docs.google.com/spreadsheets/d/143xVs9lPopFSF4eJQWloDYAndMor/edit
$spreadsheetId = '1ijecuM5f4WzTSfuUCizzvcnEWC2hVn7XOpOrp0h91jw';
$spreadsheet = $service->spreadsheets->get( $spreadsheetId );
// var_dump($spreadsheet);

// get all the rows of a sheet
// $range = 'Demos'; // here we use the name of the Sheet to get all the rows
// $response = $service->spreadsheets_values->get($spreadsheetId, $range);
// $values = $response->getValues();
// var_dump($values);

// $newRow = [
//     '456740',
//     'Hellboy',
//     'https://image.tmdb.org/t/p/w500/bk8LyaMqUtaQ9hUShuvFznQYQKR.jpg',
//     "Hellboy comes to England, where he must defeat Nimue, Merlin's consort and the Blood Queen. But their battle will bring about the end of the world, a fate he desperately tries to turn away.",
//     '1554944400',
//     'Fantasy, Action',
// ];
// $rows = [$newRow]; // you can append several rows at once
// $valueRange = new \Google_Service_Sheets_ValueRange();
// $valueRange->setValues( $rows );
// $range = 'Demos'; // the service will detect the last row of this sheet
// $options = ['valueInputOption' => 'USER_ENTERED'];
// $service->spreadsheets_values->append( $spreadsheetId, $range, $valueRange, $options );
