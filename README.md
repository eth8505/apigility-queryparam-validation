# Eth8505\ApigilityQueryStringValidation - ZF3 Module for Apigility QueryString validation
The **Eth8505\ApigilityQueryStringValidation** module allows you to validate query string parameters with
[Apigility](https://apigility.org) just like you would with 
[zfcampus/zf-content-validation](https://github.com/zfcampus/zf-content-validation).

## Hot to install

Install `eth8505/zf-symfony-console` package via composer.

~~~bash
$ composer require eth8505/apigility-querystring-validation
~~~

Load the module in your `application.config.php` file like so:

~~~php
<?php

return [
	'modules' => [
		'Eth8585\\ApigilityQueryStringValidation\\',
		// ...
	],
];
~~~
