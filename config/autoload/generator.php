<?php
return [
    'dto'   =>  [
        'namespace' =>  'App\\Dto',
        'path'      =>  BASE_PATH.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Dto',
        'type'  =>  [
            'mapping'   =>  [
                'integer'   =>  'int',
            ],
            'format'    =>  [
                'datetime'  =>  [
                    'attribute' =>  [

                    ]
                ]
            ]
        ]
    ],
    'mapper'    =>  [
        'namespace' =>  'App\\Mapper',
        'path'  =>  BASE_PATH.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Mapper',
    ],
    'service'    =>  [
        'namespace' =>  'App\\Service',
        'path'  =>  BASE_PATH.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Service',
        'impl'  =>  'Impl'
    ]
];