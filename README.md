# Base64Url implementation for PHP

## Install

```console
composer require antikirra/base64url
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
