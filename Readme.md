# Parser PDF-CAD files  #

It is standalone PHP Library, provide simple API for get common index information about PDF file and get decoded stream vecctor data. 

Parser use TCPDF library and common parsing ideas and code from [PDF Parser](https://github.com/smalot/pdfparser/) 


## Install ##

```
composer require "yushkevichv/pdf-cad-reader"

```

## Usage ##

```php
$pdfReader = new PDFCadReader(); 
$pdfObject = $pdfReader->parseFile($pdfFilePath);

// get common information about pdf file and mappers
$pdfObject->getIndex();

// get array of decodedd streams
$pdfObject->getStreamData();

// decode text from stream
$pdfObject->decodeText('F1', '<02450262026b026c0268025c>');
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
                'fontFamily',
                'font' => [
                    'code',
                    'name',
                    'encoding',
                    'type',
                    'flags',
                    'composite',
                    'subType',
                    'fontInfo' => [
                        'fontFamily',
                        'fontWeight',
                        'fontBox' => [],
                        'ascent',
                        'descent',
                        'leading',
                        'capHeight'
                    ],
                    'glyphIndexArray' => [],
    '               'CIDSystemInfo' => [
                        'Registry',
                        'Ordering',
                        'Supplement'
                    ]
                ]
            ]   
        ]
    ],
    'layers' => [
        'id' => 'name'
    ]
]
```

