# Eth8505\ZfRestQueryParamValidation - ZF3 Module for zf-rest QueryString validation
The **Eth8505\ZfRestQueryParamValidation** module allows you to validate query parameters with
[zfcampus/zf-rest](https://github.com/zfcampus/zf-rest) just like you would with 
[zfcampus/zf-content-validation](https://github.com/zfcampus/zf-content-validation) for entities.

## Hot to install

Install `eth8505/zf-rest-queryparam-validation` package via composer.

~~~bash
$ composer require eth8505/zf-rest-queryparam-validation
~~~

Load the module in your `application.config.php` file like so:

~~~php
<?php

return [
	'modules' => [
		'Eth8585\\ZfRestQueryParamValidation\\',
		// ...
	],
];
~~~
