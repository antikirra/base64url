# Base64URL | URL-Safe Base64 Encoding

![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/antikirra/base64url/php)
![Packagist Version](https://img.shields.io/packagist/v/antikirra/base64url)
![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)

**Ultra-lightweight PHP library for URL-safe base64 encoding and decoding.** Perfect for JWT tokens, URL parameters, API keys, and any scenario requiring base64 encoding without URL-unsafe characters (+, /, =).

## Install

```console
composer require antikirra/base64url:^2.0
```

## Why Base64URL?

- ✨ **URL-Safe** - No special characters that need URL encoding
- 🔧 **RFC 4648 Compliant** - Follows official base64url specification
- ⚡ **Blazing Fast** - Minimal overhead, optimized string operations
- 🎯 **Drop-in Replacement** - Simple API, easy migration from base64_encode/decode
- 📦 **Lightweight** - Just 2 functions, zero dependencies
- 🚀 **Production Ready** - Battle-tested in JWT and API implementations

## Features

- **URL-Safe Encoding**: Replaces `+` with `-` and `/` with `_`, removes padding `=`
- **Input Validation**: Strict validation prevents invalid characters in decoding
- **UTF-8 Support**: Handles multibyte characters correctly (Chinese, Korean, Japanese, etc.)
- **Binary Safe**: Works with any binary data, not just text
- **Legacy Compatible**: Works with PHP 5.6+ through PHP 8.4
- **Zero Dependencies**: No external libraries or extensions required
- **100% Test Coverage**: Fully tested with comprehensive test suite

## Perfect for

- **JWT Tokens**: Encoding JWT payloads and signatures
- **URL Parameters**: Safely encode data in query strings
- **API Keys**: Generate URL-safe API tokens and keys
- **OAuth**: State parameters and authorization codes
- **File Names**: Encode data for use in file names
- **Cookies**: Store encoded data in cookies without escaping

## Requirements

- **PHP**: 5.6 or higher
- **Extensions**: None (uses only core PHP functions)
- **Dependencies**: Zero

## Basic usage

```php
<?php

use function Antikirra\base64url_decode;
use function Antikirra\base64url_encode;

require __DIR__ . '/vendor/autoload.php';

// Encode text (including UTF-8)
echo base64url_encode('Hello World!'); // SGVsbG8gV29ybGQh
echo base64url_encode('火 剄る 불');    // 54GrIOWJhOOCiyDrtog

// Decode back
echo base64url_decode('SGVsbG8gV29ybGQh'); // Hello World!
echo base64url_decode('54GrIOWJhOOCiyDrtog'); // 火 剄る 불

// Invalid input returns false
var_dump(base64url_decode('invalid@chars!')); // bool(false)
```

## API Reference

### `base64url_encode(string $string): string`

Encodes a string using URL-safe base64 encoding.

**Parameters:**
- `$string` - The string to encode (binary-safe)

**Returns:**
- URL-safe base64 encoded string (no `+`, `/`, or `=` characters)

**Example:**
```php
base64url_encode('test'); // dGVzdA
```

### `base64url_decode(string $string): string|false`

Decodes a URL-safe base64 encoded string.

**Parameters:**
- `$string` - The base64url encoded string

**Returns:**
- Decoded string on success, `false` on invalid input

**Example:**
```php
base64url_decode('dGVzdA'); // test
base64url_decode('inv@lid'); // false
```

## Testing

The library is thoroughly tested with comprehensive test coverage:

- **Test Framework**: Pest
- **Total Tests**: 16 passing
- **Code Coverage**: 100% (lines and functions)

### Test Categories

- **Basic Functionality**: Encoding and decoding operations
- **UTF-8 Support**: Multibyte character handling
- **Edge Cases**: Empty strings, padding scenarios
- **Input Validation**: Invalid character detection
- **Round-trip Tests**: Encode → Decode consistency
- **Binary Safety**: Handling non-text data

## Comparison: Standard Base64 vs Base64URL

| Feature | base64_encode/decode | base64url_encode/decode |
|---------|---------------------|------------------------|
| URL-Safe | ❌ No (contains `+/=`) | ✅ Yes (uses `-_`) |
| Padding | ✅ Yes (`=`) | ❌ No (removed) |
| RFC 4648 Section 4 | ✅ Yes | ❌ No |
| RFC 4648 Section 5 | ❌ No | ✅ Yes |
| Use in URLs | ⚠️ Requires encoding | ✅ Direct use |
| JWT Compatible | ❌ No | ✅ Yes |

## Keywords

base64url, url-safe-encoding, base64-encoding, jwt, php-encoder, rfc-4648, url-encoding, api-tokens, zero-dependencies, php-library
