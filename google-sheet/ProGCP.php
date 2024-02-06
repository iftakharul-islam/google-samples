<?php

class GoogleSheetService
{
    private string $spreadsheetId;
    private string $sheetName;
    private string $range = '';
    private array $data;
    private Google_Service_Sheets $service;

    const VALUE_INPUT_OPTION = 'USER_ENTERED';

    public function __construct( Google_Service_Sheets $service, string $spreadsheetId, string $sheetName )
    {
        $this->service = $service;
        $this->spreadsheetId = $spreadsheetId;
        $this->sheetName = $sheetName;
    }

    public function setSpreadSheetId( string $spreadsheetId ): self
    {
        $this->spreadsheetId = $spreadsheetId;

        return $this;
    }

    public function setSheet( string $sheetName ): self
    {
        $this->sheetName = $sheetName;

        return $this;
    }

    public function setData( array $data ): self
    {
        $this->data = $data;

        return $this;
    }

    public function setRange( string $range ): self
    {
        $this->range = $range;

        return $this;
    }

    public function sendData(): void
    {
        $valueRange = new Google_Service_Sheets_ValueRange();
        $valueRange->setValues( [$this->data] );
        $range = $this->sheetName; // the service will detect the last row of this sheet
        $options = ['valueInputOption' => self::VALUE_INPUT_OPTION];
        $this->service->spreadsheets_values->append( $this->spreadsheetId, $range, $valueRange, $options );
    }

    public function updateData( array $updateRow ): void
    {
        $valueRange = new Google_Service_Sheets_ValueRange();
        $valueRange->setValues( [$updateRow] );
        $range = $this->sheetName . $this->range;
        $options = ['valueInputOption' => self::VALUE_INPUT_OPTION];
        $this->service->spreadsheets_values->update( $this->spreadsheetId, $range, $valueRange, $options );
    }

    public function deleteRow(): void
    {
        $range = $this->sheetName . '!' . $this->range; // the range to clear, the 23th and 24th lines
        $clear = new Google_Service_Sheets_ClearValuesRequest();
        $this->service->spreadsheets_values->clear( $this->spreadsheetId, $range, $clear );
    }

    public function getData(): array
    {
        $range = $this->sheetName . $this->range;
        $response = $this->service->spreadsheets_values->get( $this->spreadsheetId, $range );

        return $response->getValues();
    }

    protected function getRange(): string
    {
        return $this->sheetName . '!' . $this->range;
    }

}

// Usage:
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setApplicationName( 'Google Sheets API' );
$client->setScopes( [Google_Service_Sheets::SPREADSHEETS] );
$client->setAccessType( 'offline' );
$path = __DIR__ . '/cred.json';
$client->setAuthConfig( $path );

$service = new Google_Service_Sheets( $client );

// ->setSpreadSheetId('1ijecuM5f4WzTSfuUCizzvcnEWC2hVn7XOpOrp0h91jw')
// ->setSheet('plugin')
$googleSheetService = new GoogleSheetService( $service, '1ijecuM5f4WzTSfuUCizzvcnEWC2hVn7XOpOrp0h91jw', 'plugin' );

// Example usage:
$newRow = [
    '456740',
    'Hellboy',
    'https://image.tmdb.org/t/p/w500/bk8LyaMqUtaQ9hUShuvFznQYQKR.jpg',
    "Hellboy comes to England, where he must defeat Nimue, Merlin's consort and the Blood Queen. But their battle will bring about the end of the world, a fate he desperately tries to turn away.",
    '1554944400',
    'Fantasy, Action',
];

$updateRow = [
    '456741',
    'Hellboy Updated Row',
    'https://image.tmdb.org/t/p/w500/bk8LyaMqUtaQ9hUShuvFznQYQKR.jpg',
    "Hellboy comes to England, where he must defeat Nimue, Merlin's consort and the Blood Queen. But their battle will bring about the end of the world, a fate he desperately tries to turn away.",
    '1554944400',
    'Fantasy, Action',
];

// $googleSheetService

//     ->setData( $newRow )

//     ->sendData();

// $googleSheetService

//     ->setRange( 'A6:B6' )

//     ->deleteRow();

// $data = $googleSheetService->getData();

// print_r( $data );

// Retrieve existing data from the spreadsheet
$existingData = $googleSheetService->getData();
print_r( $existingData );

// Check for duplicates
$isDuplicate = false;

foreach ( $existingData as $rowData ) {

    if ( $rowData[0] == $newRow[0] ) { // Assuming the first column contains unique identifiers
        $isDuplicate = true;
        break;
    }

}

// If no duplicates, add the new row
if ( !$isDuplicate ) {
    $googleSheetService->setData( $newRow )->sendData();
    echo "New entry added successfully.";
} else {
    echo "Duplicate entry found. Not added to the spreadsheet.";
}
