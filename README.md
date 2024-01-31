# Base64Url implementation for PHP
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/antikirra/base64url/php)
![Packagist Version](https://img.shields.io/packagist/v/antikirra/base64url)

## Install

```console
composer require antikirra/base64url:^2.0
```

## Basic usage

```php
<?php

use function Antikirra\base64url_decode;
use function Antikirra\base64url_encode;

require __DIR__ . '/vendor/autoload.php';

base64url_encode('火 剄る 불');
base64url_decode('54GrIOWJhOOCiyDrtog');
```
