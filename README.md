# Eth8505\ZfRestQueryParamValidation - ZF3 Module for zf-rest QueryString validation
The **Eth8505\ZfRestQueryParamValidation** module allows you to validate query parameters with
[zfcampus/zf-rest](https://github.com/zfcampus/zf-rest) just like you would with 
[zfcampus/zf-content-validation](https://github.com/zfcampus/zf-content-validation) for entities.

## How to install

Install `eth8505/zf-rest-queryparam-validation` package via composer.

~~~bash
$ composer require eth8505/zf-rest-queryparam-validation
~~~

Load the module in your `application.config.php` file like so:

~~~php
<?php

return [
	'modules' => [
		'Eth8585\\ZfRestQueryParamValidation',
		// ...
	],
];
~~~

## How to use

Just like with [zfcampus/zf-content-validation](https://github.com/zfcampus/zf-content-validation), specify a
`query_filter` key in the `zf-content-validation` section of your `module.config.php` and register a
`input_filter_spec`. The [apigility docs](https://apigility.org/documentation/content-validation/advanced)
dig into this a little deeper.

### Generic query param validation for a rest controller
~~~php
<?php
return [
// ...
    'zf-content-validation' => [
        'MyModule\\V1\\Rest\\MyModule\\Controller' => [
            'query_filter' => 'MyModule\\V1\\Rest\\MyModule\\QueryValidator',
        ],
    ],
// ...
    'input_filter_specs' => [
        'MyModule\\V1\\Rest\\MyModule\\QueryValidator' => [
            0 => [
                'required' => false,
                'validators' => [
                    // ...
                ],
                'filters' => [],
                'name' => 'my_param',
                'field_type' => 'integer',
            ],
        ],
    ],
];
~~~

### Action-specific query-validation
~~~php
<?php
return [
// ...
    'zf-content-validation' => [
        'MyModule\\V1\\Rest\\MyModule\\Controller' => [
            'query_filter' => [
                'default' => 'MyModule\\V1\\Rest\\MyModule\\QueryValidator',
                'fetchAll' => 'MyModule\\V1\\Rest\\MyModule\\FetchAllQueryValidator'
            ],
        ],
    ],
// ...
    'input_filter_specs' => [
        'MyModule\\V1\\Rest\\MyModule\\QueryValidator' => [
            0 => [
                'required' => false,
                'validators' => [
                    // ...
                ],
                'filters' => [],
                'name' => 'my_param',
                'field_type' => 'integer',
            ],
        ],
        'MyModule\\V1\\Rest\\MyModule\\FetchAllQueryValidator' => [
            0 => [
                'required' => false,
                'validators' => [
                    // ...
                ],
                'filters' => [],
                'name' => 'my_fetch_all_param',
                'field_type' => 'integer',
            ], 
        ]
    ],
];
~~~
 
## Thanks
Thanks to [jdelisle](https://github.com/jdelisle) and his 
[Query String validation gist](https://gist.github.com/jdelisle/e10dfab05427e553a7d0#file-queryvalidationlistener-php-L120)
which this module is based on.