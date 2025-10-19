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

describe('base64url edge cases', function () {
    it('handles multiline strings with \\n', function () {
        $multiline = "First line\nSecond line\nThird line";
        $encoded = base64url_encode($multiline);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($multiline);
        expect($encoded)->not->toContain("\n");
        expect($encoded)->toMatch('/^[A-Za-z0-9_-]+$/');
    });

    it('handles multiline strings with \\r\\n', function () {
        $multiline = "First line\r\nSecond line\r\nThird line";
        $encoded = base64url_encode($multiline);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($multiline);
        expect($encoded)->not->toContain("\r");
        expect($encoded)->not->toContain("\n");
    });

    it('handles multiline strings with mixed line endings', function () {
        $multiline = "Line with \\n\nLine with \\r\\n\r\nLine with \\r\rEnd";
        $encoded = base64url_encode($multiline);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($multiline);
    });

    it('handles strings with only whitespace', function () {
        $testCases = [
            ' ',
            '   ',
            "\t",
            "\n",
            "\r\n",
            " \t\n\r",
        ];

        foreach ($testCases as $whitespace) {
            $encoded = base64url_encode($whitespace);
            $decoded = base64url_decode($encoded);

            expect($decoded)->toBe($whitespace);
        }
    });

    it('handles strings with leading and trailing whitespace', function () {
        $testCases = [
            ' hello ',
            "\nhello\n",
            "\r\nhello\r\n",
            "\thello\t",
            "  \n\t hello world \t\n  ",
        ];

        foreach ($testCases as $input) {
            $encoded = base64url_encode($input);
            $decoded = base64url_decode($encoded);

            expect($decoded)->toBe($input);
        }
    });

    it('handles very long single lines', function () {
        $longLine = str_repeat('a', 10000);
        $encoded = base64url_encode($longLine);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($longLine);
        expect($encoded)->not->toContain('=');
    });

    it('handles null bytes in strings', function () {
        $withNull = "before\x00after";
        $encoded = base64url_encode($withNull);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($withNull);
        expect(strlen($decoded))->toBe(12); // 6 + 1 null + 5
    });

    it('handles multiple consecutive null bytes', function () {
        $multiNull = "start\x00\x00\x00end";
        $encoded = base64url_encode($multiNull);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($multiNull);
        expect(strlen($decoded))->toBe(11);
    });

    it('handles strings that are exactly multiples of 3 bytes', function () {
        // 3 bytes = no padding needed in base64
        $exact3 = 'abc'; // 3 bytes
        $exact6 = 'abcdef'; // 6 bytes
        $exact9 = 'abcdefghi'; // 9 bytes

        expect(base64url_decode(base64url_encode($exact3)))->toBe($exact3);
        expect(base64url_decode(base64url_encode($exact6)))->toBe($exact6);
        expect(base64url_decode(base64url_encode($exact9)))->toBe($exact9);
    });

    it('handles strings with 1 byte padding requirement', function () {
        // 4 bytes, 5 bytes, etc. need 1 padding char
        $need1pad_4 = 'abcd'; // 4 bytes
        $need1pad_5 = 'abcde'; // 5 bytes

        $encoded4 = base64url_encode($need1pad_4);
        $encoded5 = base64url_encode($need1pad_5);

        expect($encoded4)->not->toContain('=');
        expect($encoded5)->not->toContain('=');
        expect(base64url_decode($encoded4))->toBe($need1pad_4);
        expect(base64url_decode($encoded5))->toBe($need1pad_5);
    });

    it('handles strings with 2 bytes padding requirement', function () {
        // 1 byte, 2 bytes need 2 padding chars
        $need2pad_1 = 'a'; // 1 byte
        $need2pad_2 = 'ab'; // 2 bytes

        $encoded1 = base64url_encode($need2pad_1);
        $encoded2 = base64url_encode($need2pad_2);

        expect($encoded1)->not->toContain('=');
        expect($encoded2)->not->toContain('=');
        expect(base64url_decode($encoded1))->toBe($need2pad_1);
        expect(base64url_decode($encoded2))->toBe($need2pad_2);
    });

    it('handles JSON strings', function () {
        $json = '{"name":"John","age":30,"city":"New York","data":{"nested":true}}';
        $encoded = base64url_encode($json);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($json);
        expect(json_decode($decoded, true))->toBeArray();
    });

    it('handles XML strings', function () {
        $xml = '<?xml version="1.0"?><root><item id="1">Test</item></root>';
        $encoded = base64url_encode($xml);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($xml);
    });

    it('handles strings with only special characters', function () {
        $special = '!@#$%^&*()_+-=[]{}|;:\'",.<>?/\\~`';
        $encoded = base64url_encode($special);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($special);
        expect($encoded)->toMatch('/^[A-Za-z0-9_-]+$/');
    });

    it('handles repeated characters', function () {
        $repeated = str_repeat('x', 1000);
        $encoded = base64url_encode($repeated);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($repeated);
    });

    it('handles alternating patterns', function () {
        $pattern = str_repeat('01', 500);
        $encoded = base64url_encode($pattern);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($pattern);
    });

    it('handles binary data with all zeros', function () {
        $zeros = str_repeat("\x00", 100);
        $encoded = base64url_encode($zeros);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($zeros);
        expect(strlen($decoded))->toBe(100);
    });

    it('handles binary data with all ones', function () {
        $ones = str_repeat("\xff", 100);
        $encoded = base64url_encode($ones);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($ones);
    });

    it('handles markdown formatted text', function () {
        $markdown = "# Header\n\n## Subheader\n\n- List item 1\n- List item 2\n\n**Bold** and *italic*";
        $encoded = base64url_encode($markdown);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($markdown);
    });

    it('handles code snippets with indentation', function () {
        $code = "function test() {\n    if (true) {\n        return 'hello';\n    }\n}";
        $encoded = base64url_encode($code);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($code);
    });

    it('handles URLs and query strings', function () {
        $url = 'https://example.com/path?param1=value1&param2=value2&special=!@#$%';
        $encoded = base64url_encode($url);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($url);
        expect($encoded)->toMatch('/^[A-Za-z0-9_-]+$/');
    });

    it('handles base64 encoded data as input', function () {
        // Encode something with standard base64, then encode that with base64url
        $original = 'test data';
        $standardBase64 = base64_encode($original);
        $base64urlEncoded = base64url_encode($standardBase64);
        $decoded = base64url_decode($base64urlEncoded);

        expect($decoded)->toBe($standardBase64);
    });

    it('handles email addresses', function () {
        $email = 'test.user+tag@example.co.uk';
        $encoded = base64url_encode($email);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($email);
    });

    it('handles file paths', function () {
        $testCases = [
            '/usr/local/bin/php',
            'C:\\Windows\\System32\\cmd.exe',
            '../../../etc/passwd',
            './relative/path/to/file.txt',
        ];

        foreach ($testCases as $path) {
            $encoded = base64url_encode($path);
            $decoded = base64url_decode($encoded);

            expect($decoded)->toBe($path);
        }
    });

    it('handles emoji sequences', function () {
        $emojis = '👨‍👩‍👧‍👦🏳️‍🌈👍🏿';
        $encoded = base64url_encode($emojis);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($emojis);
    });

    it('handles right-to-left text', function () {
        $rtl = 'مرحبا بك في العالم'; // Arabic
        $encoded = base64url_encode($rtl);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($rtl);
    });

    it('handles mixed RTL and LTR text', function () {
        $mixed = 'Hello مرحبا World עולם';
        $encoded = base64url_encode($mixed);
        $decoded = base64url_decode($encoded);

        expect($decoded)->toBe($mixed);
    });
});