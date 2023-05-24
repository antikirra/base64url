# Base64Url implementation for PHP

## Install

```bash
composer require antikirra/base64url
```

## Basic usage

```php
<?php
require __DIR__ . '/vendor/autoload.php';

// only if the function has not been defined in the global scope
base64url_encode('火 剄る 불');

// if the function could not be defined globally
\Antikirra\base64url_encode('火 剄る 불');

// under the hood
\Antikirra\Base64Url\Base64Url::encode('火 剄る 불');
```

## Demo

```php
<?php

require __DIR__ . '/vendor/autoload.php';

echo base64url_encode('火 剄る 불'); // 54GrIOWJhOOCiyDrtog

echo base64url_decode('54GrIOWJhOOCiyDrtog'); // 火 剄る 불

```
