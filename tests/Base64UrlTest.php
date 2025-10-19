<?php

use function Antikirra\base64url_encode;
use function Antikirra\base64url_decode;

describe('base64url_encode', function () {
    it('encodes simple strings correctly', function () {
        expect(base64url_encode('hello'))->toBe('aGVsbG8');
        expect(base64url_encode('world'))->toBe('d29ybGQ');
    });

    it('replaces + with - and / with _', function () {
        // Binary data that produces '+' and '/' in standard base64
        $data = "\xff\xff\xff";
        $encoded = base64url_encode($data);

        expect($encoded)->not->toContain('+');
        expect($encoded)->not->toContain('/');
        expect($encoded)->toContain('_');
    });

    it('removes padding (=)', function () {
        expect(base64url_encode('a'))->toBe('YQ');
        expect(base64url_encode('ab'))->toBe('YWI');
        expect(base64url_encode('abc'))->toBe('YWJj');
        expect(base64url_encode('abcd'))->toBe('YWJjZA');

        // None should contain '='
        expect(base64url_encode('a'))->not->toContain('=');
        expect(base64url_encode('ab'))->not->toContain('=');
    });

    it('handles empty string', function () {
        expect(base64url_encode(''))->toBe('');
    });

    it('handles special characters', function () {
        expect(base64url_encode('hello world!'))->toBe('aGVsbG8gd29ybGQh');
        expect(base64url_encode('!@#$%^&*()'))->toBe('IUAjJCVeJiooKQ');
    });

    it('handles unicode strings', function () {
        expect(base64url_encode('Привет'))->toBe('0J_RgNC40LLQtdGC');
        expect(base64url_encode('你好'))->toBe('5L2g5aW9');
        expect(base64url_encode('🚀'))->toBe('8J-agA');
    });

    it('handles binary data', function () {
        $binary = pack('C*', 0, 1, 2, 3, 4, 5, 255, 254, 253);
        $encoded = base64url_encode($binary);

        expect($encoded)->toBeString();
        expect($encoded)->not->toContain('=');
        expect($encoded)->not->toContain('+');
        expect($encoded)->not->toContain('/');
    });

    it('handles long strings', function () {
        $longString = str_repeat('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', 100);
        $encoded = base64url_encode($longString);

        expect($encoded)->toBeString();
        expect(strlen($encoded))->toBeGreaterThan(0);
        expect($encoded)->not->toContain('=');
    });

    it('produces URL-safe output', function () {
        $data = "subjects?_d=1";
        $encoded = base64url_encode($data);

        // Check that it can be used in URL without encoding
        expect($encoded)->toMatch('/^[A-Za-z0-9_-]+$/');
    });
});

describe('base64url_decode', function () {
    it('decodes simple strings correctly', function () {
        expect(base64url_decode('aGVsbG8'))->toBe('hello');
        expect(base64url_decode('d29ybGQ'))->toBe('world');
    });

    it('handles URL-safe characters (- and _)', function () {
        $original = "\xff\xff\xff";
        $encoded = base64url_encode($original);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($original);
    });

    it('handles missing padding correctly', function () {
        // These are without padding
        expect(base64url_decode('YQ'))->toBe('a');
        expect(base64url_decode('YWI'))->toBe('ab');
        expect(base64url_decode('YWJj'))->toBe('abc');
        expect(base64url_decode('YWJjZA'))->toBe('abcd');
    });

    it('handles strings with different padding requirements', function () {
        // Length % 4 = 0 (no padding needed)
        expect(base64url_decode('YWJj'))->toBe('abc');

        // Length % 4 = 2 (needs 2 padding chars)
        expect(base64url_decode('YQ'))->toBe('a');

        // Length % 4 = 3 (needs 1 padding char)
        expect(base64url_decode('YWI'))->toBe('ab');
    });

    it('handles empty string', function () {
        expect(base64url_decode(''))->toBe('');
    });

    it('decodes special characters', function () {
        expect(base64url_decode('aGVsbG8gd29ybGQh'))->toBe('hello world!');
        expect(base64url_decode('IUAjJCVeJiooKQ'))->toBe('!@#$%^&*()');
    });

    it('decodes unicode strings', function () {
        expect(base64url_decode('0J_RgNC40LLQtdGC'))->toBe('Привет');
        expect(base64url_decode('5L2g5aW9'))->toBe('你好');
        expect(base64url_decode('8J-agA'))->toBe('🚀');
    });

    it('decodes binary data', function () {
        $binary = pack('C*', 0, 1, 2, 3, 4, 5, 255, 254, 253);
        $encoded = base64url_encode($binary);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($binary);
    });

    it('handles long strings', function () {
        $longString = str_repeat('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', 100);
        $encoded = base64url_encode($longString);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($longString);
    });

    it('returns false for invalid base64 input', function () {
        // Invalid characters for base64url
        $result = base64url_decode('invalid@#$%characters');

        // base64_decode returns false for invalid input
        expect($result)->toBeFalse();
    });
});

describe('base64url_encode and base64url_decode', function () {
    it('are reversible for various inputs', function () {
        $testCases = [
            'hello',
            'world',
            'hello world!',
            '!@#$%^&*()',
            'Привет мир',
            '你好世界',
            '🚀🌟💻',
            str_repeat('test', 1000),
            pack('C*', 0, 1, 2, 3, 255, 254, 253),
            '',
        ];

        foreach ($testCases as $original) {
            $encoded = base64url_encode($original);
            $decoded = base64url_decode($encoded);

            expect($decoded)->toBe($original);
        }
    });

    it('produces different output than standard base64', function () {
        // Test data that will produce '+' in standard base64
        // Binary data 0xFB produces '+' in base64
        $dataWithPlus = "\xfb\xef";

        $standardBase64 = base64_encode($dataWithPlus);
        $base64url = base64url_encode($dataWithPlus);

        // Standard base64 has '+', base64url should have '-' instead
        expect($standardBase64)->toContain('+');
        expect($base64url)->not->toContain('+');
        expect($base64url)->toContain('-');

        // Test data that will produce '/' in standard base64
        $dataWithSlash = "\xff\xff\xff";
        $standardBase64Slash = base64_encode($dataWithSlash);
        $base64urlSlash = base64url_encode($dataWithSlash);

        // Standard base64 has '/', base64url should have '_' instead
        expect($standardBase64Slash)->toContain('/');
        expect($base64urlSlash)->not->toContain('/');
        expect($base64urlSlash)->toContain('_');
    });

    it('handles URL-safe encoding without additional escaping', function () {
        $data = "subjects?_d=1&test=value";
        $encoded = base64url_encode($data);

        // Verify it contains only URL-safe characters
        expect($encoded)->toMatch('/^[A-Za-z0-9_-]*$/');

        // Verify it can be decoded back
        expect(base64url_decode($encoded))->toBe($data);
    });

    it('handles all possible byte values', function () {
        $allBytes = '';
        for ($i = 0; $i < 256; $i++) {
            $allBytes .= chr($i);
        }

        $encoded = base64url_encode($allBytes);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($allBytes);
    });
});