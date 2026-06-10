<?php

/**
 * RulesTrait.php
 *
 * This file is part of InitPHP Validation.
 *
 * @author     Muhammet ŞAFAK <info@muhammetsafak.com.tr>
 * @copyright  Copyright © 2022 InitPHP
 * @license    http://initphp.github.io/license.txt  MIT
 * @version    2.0.0
 * @link       https://www.muhammetsafak.com.tr
 */

declare(strict_types=1);

namespace InitPHP\Validation;

use DateTimeInterface;

/**
 * The built-in validation rules.
 *
 * Each `rule_*` method receives the field value as its first argument followed
 * by any DSL arguments, and returns true when the value satisfies the rule.
 * Rule names are matched case-insensitively by the dispatcher.
 */
trait RulesTrait
{
    /**
     * Regular expression bodies used by the type rules.
     *
     * @var array<string, string|array<string, string>>
     */
    private array $constPattern = [
        'float'       => '[+-]?([0-9]*[.])?[0-9]+',
        'int'         => '[+-]?\d+',
        'numeric'     => '[+-]?([0-9]*[.])?[0-9]+',
        'boolean'     => 'true|false|1|0',
        'alpha'       => '[\p{L}]+',
        'alphanumeric' => '[\p{L}0-9]+',
        'creditCard'  => [
            'amex'       => '(3[47]\d{13})',
            'visa'       => '(4\d{12}(?:\d{3})?)',
            'mastercard' => '(5[1-5]\d{14})',
            'maestro'    => '((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)',
            'jcb'        => '(35[2-8][89]\d\d\d{10})',
            'solo'       => '((?:6334|6767)\d{12}(?:\d\d)?\d?)',
            'switch'     => '(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)',
        ],
    ];

    /**
     * Named patterns available to the `regex` rule, extendable via pattern().
     *
     * @var array<string, string>
     */
    protected array $patterns = [
        'uri'      => '[A-Za-z0-9-\/_?&=]+',
        'slug'     => '[-a-z0-9_]+',
        'url'      => '[A-Za-z0-9-:.\/_?&=#]+',
        'alpha'    => '[\p{L}]+',
        'words'    => '[\p{L}\s]+',
        'alphanum' => '[\p{L}0-9]+',
        'int'      => '[0-9]+',
        'float'    => '[0-9\.,]+',
        'tel'      => '[0-9+\s()-]+',
        'text'     => '[\p{L}0-9\s-.,;:!"%&()?+\'°#\/@]+',
        'file'     => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+\.[A-Za-z0-9]{2,4}',
        'folder'   => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+',
        'address'  => '[\p{L}0-9\s.,()°-]+',
        'date_dmy' => '[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}',
        'date_ymd' => '[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}',
        'email'    => '[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+[.]?[a-z-A-Z]?',
    ];

    /**
     * Whether the value is an integer (or an integer-looking string).
     */
    protected function rule_integer(mixed $data): bool
    {
        if (\is_int($data)) {
            return true;
        }

        return \is_string($data)
            && preg_match('/^(' . $this->intPattern() . ')$/u', $data) === 1;
    }

    /**
     * Whether the value is a float (or a float-looking string).
     */
    protected function rule_float(mixed $data): bool
    {
        if (\is_float($data) || \is_int($data)) {
            return true;
        }

        return \is_string($data)
            && preg_match('/^(' . $this->floatPattern() . ')$/u', $data) === 1;
    }

    /**
     * Whether the value contains only alphabetic characters.
     */
    protected function rule_alpha(mixed $data): bool
    {
        return \is_string($data)
            && preg_match('/^(' . $this->alphaPattern() . ')$/u', $data) === 1;
    }

    /**
     * Whether the value contains only alphanumeric characters.
     */
    protected function rule_alphanum(mixed $data): bool
    {
        return \is_string($data)
            && preg_match('/^(' . $this->alphanumericPattern() . ')$/u', $data) === 1;
    }

    /**
     * Alias of {@see RulesTrait::rule_alphanum()}.
     */
    protected function rule_alphanumeric(mixed $data): bool
    {
        return $this->rule_alphanum($data);
    }

    /**
     * Whether the value is a credit card number, optionally of a given type
     * (amex, visa, mastercard, maestro, jcb, solo, switch).
     */
    protected function rule_creditcard(mixed $data, ?string $type = null): bool
    {
        $number = str_replace(' ', '', $this->toStringValue($data));
        /** @var array<string, string> $cards */
        $cards = $this->constPattern['creditCard'];

        if ($type === null || $type === '') {
            return preg_match('/^(?:' . implode('|', $cards) . ')$/', $number) === 1;
        }
        $type = strtolower($type);
        if (isset($cards[$type])) {
            return preg_match('/^' . $cards[$type] . '$/', $number) === 1;
        }

        return false;
    }

    /**
     * Whether the value is numeric.
     */
    protected function rule_numeric(mixed $data): bool
    {
        if (\is_int($data) || \is_float($data)) {
            return true;
        }

        return \is_string($data) && is_numeric($data);
    }

    /**
     * Whether the value is a string.
     */
    protected function rule_string(mixed $data): bool
    {
        return \is_string($data);
    }

    /**
     * Whether the value is a boolean or a boolean-looking scalar
     * (true, false, 1, 0).
     */
    protected function rule_boolean(mixed $data): bool
    {
        if (\is_bool($data)) {
            return true;
        }
        if (\is_int($data)) {
            return $data === 0 || $data === 1;
        }

        return \is_string($data)
            && preg_match('/^(' . $this->booleanPattern() . ')$/u', $data) === 1;
    }

    /**
     * Whether the value is an array.
     */
    protected function rule_array(mixed $data): bool
    {
        return \is_array($data);
    }

    /**
     * Whether the value is a valid e-mail address.
     */
    protected function rule_mail(mixed $data): bool
    {
        return \is_string($data) && filter_var($data, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Whether the value is an e-mail at one of the given hosts.
     */
    protected function rule_mailhost(mixed $data, string ...$domain): bool
    {
        if (!\is_string($data) || filter_var($data, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }
        $parts = explode('@', $data, 2);
        $mailHost = trim($parts[1] ?? '');
        foreach ($domain as $host) {
            $host = trim($host);
            if ($host !== '' && $mailHost === $host) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the value is a valid URL.
     */
    protected function rule_url(mixed $data): bool
    {
        return \is_string($data) && filter_var($data, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Whether the value is a URL whose host matches (or is a subdomain of) one
     * of the given domains.
     */
    protected function rule_urlhost(mixed $data, string ...$domains): bool
    {
        if (!\is_string($data) || filter_var($data, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        $host = parse_url($data, PHP_URL_HOST);
        if (!\is_string($host)) {
            return false;
        }
        foreach ($domains as $domain) {
            $domain = trim($domain);
            if ($domain === '') {
                continue;
            }
            if ($host === $domain) {
                return true;
            }
            if (mb_substr($host, 0 - (mb_strlen($domain) + 1)) === '.' . $domain) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the value is empty once trimmed.
     */
    protected function rule_empty(mixed $data): bool
    {
        return trim($this->toStringValue($data)) === '';
    }

    /**
     * Whether the value is present (not an empty/blank string).
     */
    protected function rule_required(mixed $data): bool
    {
        if (\is_int($data) || \is_float($data)) {
            return true;
        }
        if (\is_array($data)) {
            return $data !== [];
        }

        return trim($this->toStringValue($data)) !== '';
    }

    /**
     * Whether the value's size is at least `$min`. Numbers are compared by
     * value; strings and arrays by length/element count.
     */
    protected function rule_min(mixed $data, int|float|string $min): bool
    {
        return $this->size($data) >= (float) $min;
    }

    /**
     * Whether the value's size is at most `$max`. Numbers are compared by
     * value; strings and arrays by length/element count.
     */
    protected function rule_max(mixed $data, int|float|string $max): bool
    {
        return $this->size($data) <= (float) $max;
    }

    /**
     * Whether a numeric value falls within `min...max` (or `min-max`). An open
     * bound (`...max` or `min...`) checks only one side.
     */
    protected function rule_range(mixed $data, int|float|string $range): bool
    {
        [$min, $max] = $this->parseBounds((string) $range);
        $value = (float) $this->toStringValue($data);
        if ($min !== null && $value < (float) $min) {
            return false;
        }

        return $max === null || $value <= (float) $max;
    }

    /**
     * Whether the value's length is within range. `$range` may be a single
     * number (maximum), `min...max`, `min-max`, `...max` or `min...`.
     */
    protected function rule_length(mixed $data, int|float|string $range): bool
    {
        $length = $this->length($data);
        $range = (string) $range;

        if (is_numeric($range)) {
            $max = (int) $range;

            return $max <= 0 || $length <= $max;
        }
        [$min, $max] = $this->parseBounds($range);
        if ($min !== null && is_numeric($min) && $length < (int) $min) {
            return false;
        }

        return $max === null || !is_numeric($max) || $length <= (int) $max;
    }

    /**
     * Whether the value matches a named pattern or an inline regex body.
     */
    protected function rule_regex(mixed $data, string $pattern): bool
    {
        $body = $this->patterns[strtolower($pattern)] ?? $pattern;

        return preg_match('/^(' . $body . ')$/u', $this->toStringValue($data)) === 1;
    }

    /**
     * Whether the value is a date, accepting a DateTimeInterface or any string
     * that strtotime() understands.
     */
    protected function rule_date(mixed $data): bool
    {
        if ($data instanceof DateTimeInterface) {
            return true;
        }

        return \is_string($data) && strtotime($data) !== false;
    }

    /**
     * Whether the value is a date string in the given format.
     */
    protected function rule_dateformat(mixed $data, string $format): bool
    {
        if (!\is_string($data)) {
            return false;
        }
        $parsed = date_parse_from_format($format, $data);

        return $parsed['error_count'] === 0 && $parsed['warning_count'] === 0;
    }

    /**
     * Whether the value is an IP address (v4 or v6).
     */
    protected function rule_ip(mixed $data): bool
    {
        return \is_string($data) && filter_var($data, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Whether the value is an IPv4 address.
     */
    protected function rule_ipv4(mixed $data): bool
    {
        return \is_string($data) && filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    /**
     * Whether the value is an IPv6 address.
     */
    protected function rule_ipv6(mixed $data): bool
    {
        return \is_string($data) && filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    /**
     * Whether the value case-insensitively equals one of the given options.
     */
    protected function rule_only(mixed $data, string ...$only): bool
    {
        $value = mb_strtolower($this->toStringValue($data));
        foreach ($only as $option) {
            if (mb_strtolower($option) === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the value loosely equals one of the given options (case-sensitive).
     */
    protected function rule_strictonly(mixed $data, string ...$only): bool
    {
        foreach ($only as $option) {
            if ($option == $data) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the value equals the value of another field in the data set.
     */
    protected function rule_again(mixed $data, string $key): bool
    {
        if (!isset($this->data[$key])) {
            return false;
        }

        return $data == $this->data[$key];
    }

    /**
     * Whether the value loosely equals the given value.
     */
    protected function rule_equals(mixed $data, mixed $eqData): bool
    {
        return $data == $eqData;
    }

    /**
     * Whether the value starts with the given prefix. For arrays, compares the
     * first element.
     */
    protected function rule_startwith(mixed $data, mixed $startWith): bool
    {
        if (\is_array($data)) {
            $first = $data === [] ? null : $data[array_key_first($data)];

            return $first === $startWith;
        }
        $needle = $this->toStringValue($startWith);

        return $needle === '' || str_starts_with($this->toStringValue($data), $needle);
    }

    /**
     * Whether the value ends with the given suffix. For arrays, compares the
     * last element.
     */
    protected function rule_endwith(mixed $data, mixed $endWith): bool
    {
        if (\is_array($data)) {
            return $data !== [] && end($data) === $endWith;
        }
        $needle = $this->toStringValue($endWith);

        return $needle === '' || str_ends_with($this->toStringValue($data), $needle);
    }

    /**
     * Whether the value contains the search term: a case-insensitive substring
     * for strings/numbers, or a strict member for arrays.
     */
    protected function rule_in(mixed $data, mixed $search): bool
    {
        if (\is_array($data)) {
            return \in_array($search, $data, true);
        }
        if (\is_string($data) || is_numeric($data)) {
            return mb_stripos($this->toStringValue($data), $this->toStringValue($search)) !== false;
        }

        return false;
    }

    /**
     * Inverse of {@see RulesTrait::rule_in()}.
     */
    protected function rule_notin(mixed $data, mixed $search): bool
    {
        if (\is_array($data)) {
            return !\in_array($search, $data, true);
        }
        if (\is_string($data) || is_numeric($data)) {
            return mb_stripos($this->toStringValue($data), $this->toStringValue($search)) === false;
        }

        return false;
    }

    /**
     * Whether the value contains the search term as a case-sensitive substring.
     */
    protected function rule_contains(mixed $data, mixed $search): bool
    {
        return str_contains($this->toStringValue($data), $this->toStringValue($search));
    }

    /**
     * Inverse of {@see RulesTrait::rule_contains()}.
     */
    protected function rule_notcontains(mixed $data, mixed $search): bool
    {
        return !str_contains($this->toStringValue($data), $this->toStringValue($search));
    }

    /**
     * The numeric magnitude used by min/max: the value itself for numbers, the
     * element count for arrays, otherwise the string length.
     */
    private function size(mixed $data): float
    {
        if (\is_int($data) || \is_float($data)) {
            return (float) $data;
        }
        if (\is_array($data)) {
            return (float) \count($data);
        }
        if (\is_string($data) && is_numeric($data)) {
            return (float) $data;
        }

        return (float) mb_strlen($this->toStringValue($data));
    }

    /**
     * The length used by the length rule: element count for arrays, otherwise
     * the string length.
     */
    private function length(mixed $data): int
    {
        if (\is_array($data)) {
            return \count($data);
        }

        return mb_strlen($this->toStringValue($data));
    }

    /**
     * Split a `min...max` (or `min-max`) bound spec. Either side may be empty
     * to leave it open; the returned parts are null when open.
     *
     * @return array{0: string|null, 1: string|null}
     */
    private function parseBounds(string $spec): array
    {
        $spec = trim($spec);
        $separator = str_contains($spec, '...') ? '...' : '-';
        $parts = explode($separator, $spec, 2);
        $min = trim($parts[0] ?? '');
        $max = trim($parts[1] ?? '');

        return [$min === '' ? null : $min, $max === '' ? null : $max];
    }

    /**
     * Safely coerce a scalar value to a string for comparison and matching.
     * Arrays and non-stringable objects become an empty string.
     */
    private function toStringValue(mixed $data): string
    {
        if (\is_string($data)) {
            return $data;
        }
        if (\is_bool($data)) {
            return $data ? 'true' : 'false';
        }
        if ($data === null || \is_array($data)) {
            return '';
        }
        if (\is_int($data) || \is_float($data)) {
            return (string) $data;
        }
        if (\is_object($data)) {
            return method_exists($data, '__toString') ? (string) $data : '';
        }

        return '';
    }

    private function intPattern(): string
    {
        return $this->stringPattern('int');
    }

    private function floatPattern(): string
    {
        return $this->stringPattern('float');
    }

    private function alphaPattern(): string
    {
        return $this->stringPattern('alpha');
    }

    private function alphanumericPattern(): string
    {
        return $this->stringPattern('alphanumeric');
    }

    private function booleanPattern(): string
    {
        return $this->stringPattern('boolean');
    }

    /**
     * Fetch a scalar pattern body from {@see RulesTrait::$constPattern}.
     */
    private function stringPattern(string $name): string
    {
        $pattern = $this->constPattern[$name] ?? '';

        return \is_string($pattern) ? $pattern : '';
    }
}
