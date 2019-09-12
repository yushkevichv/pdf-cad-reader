# Parser PDF-CAD files  #

It is standalone PHP Library, provide simple API for get common index information about PDF file and get decoded stream vecctor data. 

Parser use TCPDF library and common parsing ideas and code from [PDF Parser](https://github.com/smalot/pdfparser/) 


## Install ##

```
composer require yushkevichv/pdf-cad-reader

```

## Usage ##

```php
$pdfReader = new PDFCadReader(); 
$pdfObject = $pdfReader->parseFile($pdfFilePath);

// get common information about pdf file and mappers
$pdfObject->getIndex();

// get array of decodedd streams
$pdfObject->getStreamData();
```

### Structure of PDFObject Index ###

```
[
    'root' ,
    'info' => [
        'width',
        'height',
        'rotate'
    ],
    'mappers' => [
        'layers' => [
            'ocCode' => 'id'  
         ],
        'streams' => [
            'id'
        ],
        'fonts' => [
            'fontCode' => [
                'layer',
                'fontFamily'
            ]   
        ]
    ],
    'layers' => [
        'id' => 'name'
    ]
]
```

