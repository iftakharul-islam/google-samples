<?php

class MyGoogleSheet{

    private string $spreadsheetId = '';
    private string $sheetId = '';
    private string $range = '';
    private array $data = [];

    private Google_Service_Sheets $service;

    public function setSheetService(Google_Service_Sheets $service){
         $this->service =  $service;
         return $this;
    }

    function setSpreadSheetId( string $spreadsheetId ){

        $this->spreadsheetId = $spreadsheetId;
        return $this;
    }

    function setSheet( string $sheetId ){
        $this->sheetId = $sheetId;
        return $this;
    }

    function setData(array $data){
        $this->data = $data;
        return $this;
    }
    function setRange(string $range){
        $this->range = $range;
        return $this;
    }

    function sendData(){
       
        $rows = [$this->data]; // you can append several rows at once
        $valueRange = new \Google_Service_Sheets_ValueRange();
        $valueRange->setValues( $rows );
        $range = $this->sheetId; // the service will detect the last row of this sheet
        $options = ['valueInputOption' => 'USER_ENTERED'];
        $this->service->spreadsheets_values->append( $this->spreadsheetId, $range, $valueRange, $options );
        
    }

    function updateData(array $updateRow){
        
        $rows = [$updateRow];
        $valueRange = new \Google_Service_Sheets_ValueRange();
        $valueRange->setValues($rows);
        $range = $this->sheetId.$this->range;
        $options = ['valueInputOption' => 'USER_ENTERED'];
        $this->service->spreadsheets_values->update($this->spreadsheetId, $range, $valueRange, $options);
    }
    function deleteRow(){
        $range = $this->sheetId.'!'.$this->range; // the range to clear, the 23th and 24th lines
        $clear = new \Google_Service_Sheets_ClearValuesRequest();
        $this->service->spreadsheets_values->clear($this->spreadsheetId, $range, $clear);


    }

   function getData(){
        // get all the rows of a sheet
        $range = $this->sheetId.$this->range; // here we use the name of the Sheet to get all the rows
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();
        print_r($values);
        return $values;
    }

    protected function getRange(){
       return $this->sheetId.'!'.$this->range;
    }
}




require_once 'vendor/autoload.php';

$client = new \Google_Client();
$client->setApplicationName( 'Google Sheets API' );
$client->setScopes( [\Google_Service_Sheets::SPREADSHEETS] );
$client->setAccessType( 'offline' );
$path = __DIR__ . '/cred.json';
$client->setAuthConfig( $path );




$newRow = [
    '456740',
    'Hellboy',
    'https://image.tmdb.org/t/p/w500/bk8LyaMqUtaQ9hUShuvFznQYQKR.jpg',
    "Hellboy comes to England, where he must defeat Nimue, Merlin's consort and the Blood Queen. But their battle will bring about the end of the world, a fate he desperately tries to turn away.",
    '1554944400',
    'Fantasy, Action',
];

$updateRow = [
    '456740',
    'Hellboy Updated Row',
    'https://image.tmdb.org/t/p/w500/bk8LyaMqUtaQ9hUShuvFznQYQKR.jpg',
    "Hellboy comes to England, where he must defeat Nimue, Merlin's consort and the Blood Queen. But their battle will bring about the end of the world, a fate he desperately tries to turn away.",
    '1554944400',
    'Fantasy, Action'
];

// (new MyGoogleSheet)
// ->setSheetService($service)
// ->setSpreadSheetId('1ijecuM5f4WzTSfuUCizzvcnEWC2hVn7XOpOrp0h91jw')
// ->setSheet('plugin')
// // ->setData($newRow)
// ->getData();

$service = new \Google_Service_Sheets( $client );

(new MyGoogleSheet)
->setSheetService($service)
->setSpreadSheetId('1ijecuM5f4WzTSfuUCizzvcnEWC2hVn7XOpOrp0h91jw')
->setSheet('plugin')
->setRange('A6:B6')
// ->setData($newRow)
->deleteRow();