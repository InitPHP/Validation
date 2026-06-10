<?php

/**
 * LocaleTrait.php
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

use InitPHP\Validation\Exception\LocaleException;

/**
 * Holds the error-message templates and turns them into final strings.
 *
 * Message templates are keyed by rule name (matched case-insensitively) and
 * may contain `{field}` and positional `{0}`, `{1}`, ... placeholders. The
 * field value is `{1}` and the first rule argument is `{2}`.
 */
trait LocaleTrait
{
    /**
     * Directory that language files are loaded from.
     */
    protected string $LT_Path = __DIR__ . '/languages/';

    /**
     * Cache of language arrays already loaded from disk, keyed by locale name.
     *
     * @var array<string, array<string, string|array<string, string>>>
     */
    protected array $locale_tmp = [];

    /**
     * The active message templates. `labels` holds human-readable field names;
     * every other entry is a rule message template. Defaults to English.
     *
     * @var array<string, string|array<string, string>>
     */
    protected array $locale = [
        'labels'          => [],
        'notValidDefault' => 'The {field} value is not valid.',
        'callable'        => 'The {field} value is not valid.',
        'integer'         => '{field} must be an integer.',
        'float'           => '{field} must be a float.',
        'numeric'         => '{field} must be a numeric value.',
        'string'          => '{field} must be a string.',
        'boolean'         => '{field} must be a boolean.',
        'array'           => '{field} must be an array.',
        'mail'            => '{field} must be an e-mail address.',
        'mailHost'        => '{field} must be an e-mail at {2}.',
        'url'             => '{field} must be a URL.',
        'urlHost'         => 'The host of {field} must be {2}.',
        'empty'           => '{field} must be empty.',
        'required'        => '{field} cannot be left blank.',
        'min'             => '{field} must be greater than or equal to {2}.',
        'max'             => '{field} must be no more than {2}.',
        'length'          => 'The length of {field} must be {2}.',
        'range'           => '{field} must be within the range {2}.',
        'regex'           => '{field} must match the {2} pattern.',
        'date'            => '{field} must be a date.',
        'dateFormat'      => '{field} must be a valid date in the {2} format.',
        'ip'              => '{field} must be an IP address.',
        'ipv4'            => '{field} must be an IPv4 address.',
        'ipv6'            => '{field} must be an IPv6 address.',
        'again'           => '{field} must be the same as {2}.',
        'equals'          => '{field} can only be {2}.',
        'startWith'       => '{field} must start with "{2}".',
        'endWith'         => '{field} must end with "{2}".',
        'in'              => '{field} must contain {2}.',
        'notIn'           => '{field} must not contain {2}.',
        'alpha'           => '{field} must contain only alphabetic characters.',
        'alphaNum'        => '{field} must be alphanumeric.',
        'alphanumeric'    => '{field} must be alphanumeric.',
        'creditCard'      => '{field} must be a credit card number.',
        'only'            => 'The {field} value is not valid.',
        'strictOnly'      => 'The {field} value is not valid.',
        'contains'        => '{field} must contain {2}.',
        'notContains'     => '{field} must not contain {2}.',
    ];

    /**
     * Set the directory language files are loaded from.
     *
     * @param string $dir An existing directory containing `<locale>.php` files.
     * @return $this
     *
     * @throws LocaleException When the directory does not exist.
     */
    public function setLocaleDir(string $dir = __DIR__ . '/languages/'): self
    {
        if (!is_dir($dir)) {
            throw new LocaleException('An existing directory ("' . $dir . '") must be selected for language definitions.');
        }
        $this->LT_Path = $dir;

        return $this;
    }

    /**
     * Load a locale from `<LT_Path>/<locale>.php` and make it active.
     *
     * @param string $locale Locale name (the file base name without `.php`).
     * @return $this
     *
     * @throws LocaleException When the file is missing or does not return an array.
     */
    public function setLocale(string $locale = 'en'): self
    {
        if (isset($this->locale_tmp[$locale])) {
            return $this->setLocaleArray($this->locale_tmp[$locale]);
        }
        $path = rtrim($this->LT_Path, '/\\') . DIRECTORY_SEPARATOR . $locale . '.php';
        if (!is_file($path)) {
            throw new LocaleException('Could not find ("' . $path . '") file for language definitions.');
        }
        $array = $this->requirePHP($path);
        if (!\is_array($array)) {
            throw new LocaleException('The file "' . $path . '" must return an array.');
        }
        /** @var array<string, string|array<string, string>> $array */
        $this->locale_tmp[$locale] = $array;

        return $this->setLocaleArray($array);
    }

    /**
     * Merge message templates into the active locale.
     *
     * @param array<string, string|array<string, string>> $assoc
     * @return $this
     */
    public function setLocaleArray(array $assoc): self
    {
        $this->locale = array_merge($this->locale, $assoc);

        return $this;
    }

    /**
     * Register human-readable labels that replace raw field names in messages.
     *
     * @param array<string, string> $assoc Map of field name to display label.
     * @return $this
     */
    public function labels(array $assoc): self
    {
        /** @var array<string, string> $labels */
        $labels = $this->locale['labels'];
        $this->locale['labels'] = array_merge($labels, $assoc);

        return $this;
    }

    /**
     * Resolve the message template for a rule, matched case-insensitively, or
     * fall back to the default "not valid" template.
     *
     * @param string $rule The rule name.
     */
    protected function message(string $rule): string
    {
        if (isset($this->locale[$rule]) && \is_string($this->locale[$rule])) {
            return $this->locale[$rule];
        }
        $needle = strtolower($rule);
        foreach ($this->locale as $key => $template) {
            if (\is_string($template) && strtolower((string) $key) === $needle) {
                return $template;
            }
        }
        /** @var string $default */
        $default = $this->locale['notValidDefault'];

        return $default;
    }

    /**
     * Replace `{field}`, `{0}`, `{1}`, ... placeholders in a message template.
     *
     * Values under a `field*` key are passed through the registered labels;
     * other scalar values are inserted verbatim. Array and non-stringable
     * object values are skipped.
     *
     * @param string                  $message The message template.
     * @param array<array-key, mixed> $context Placeholder values.
     */
    protected function interpolate(string $message, array $context = []): string
    {
        if ($context === []) {
            return $message;
        }
        $replace = [];
        $index = 0;
        foreach ($context as $key => $value) {
            if (\is_array($value)) {
                continue;
            }
            if (\is_object($value) && !method_exists($value, '__toString')) {
                continue;
            }
            if ((\is_string($value) || \is_int($value)) && str_starts_with((string) $key, 'field')) {
                /** @var array<string, string> $labels */
                $labels = $this->locale['labels'];
                $value = $labels[$value] ?? $value;
            }
            $replacement = $this->stringify($value);
            $replace['{' . $key . '}'] = $replacement;
            $replace['{' . $index . '}'] = $replacement;
            ++$index;
        }

        return strtr($message, $replace);
    }

    /**
     * Convert a scalar (or stringable) placeholder value to a string. Arrays
     * and non-stringable objects become an empty string.
     */
    private function stringify(mixed $value): string
    {
        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if ($value === null || \is_array($value)) {
            return '';
        }
        if (\is_int($value) || \is_float($value) || \is_string($value)) {
            return (string) $value;
        }
        if (\is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        return '';
    }

    /**
     * Include a PHP language file and return its value.
     *
     * @param string $path
     * @return mixed
     */
    private function requirePHP(string $path)
    {
        return require $path;
    }
}
