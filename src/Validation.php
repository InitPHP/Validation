<?php

/**
 * Validation.php
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

use InitPHP\Validation\Exception\InvalidArgumentException;
use InitPHP\Validation\Exception\UndefinedRuleException;

/**
 * Validates an associative data set against a list of rules.
 *
 * Rules are added with {@see Validation::rule()} and checked with
 * {@see Validation::validation()}. A rule may be written as a pipe separated
 * DSL string (e.g. `"required|integer|range(1...10)"`), a single callback, or
 * a list of either. Failed rules produce localized messages that are read
 * back with {@see Validation::getError()}.
 *
 * Each call to {@see Validation::validation()} consumes the rules that have
 * been queued since the previous call and resets the error list, so the
 * typical usage is: queue rules, validate, read errors, repeat.
 */
class Validation
{
    use LocaleTrait;
    use RulesTrait;

    /**
     * The version of the library.
     */
    public const VERSION = '2.0.0';

    /**
     * The data set being validated, keyed by field name.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * Field names that are allowed to be absent. When a field listed here has
     * no value in {@see Validation::$data}, its queued rules are skipped.
     *
     * @var list<string>
     */
    protected array $optional = [];

    /**
     * The queue of rules to evaluate on the next {@see Validation::validation()}.
     *
     * @var list<array{key: string, rule: string|callable, err: string|null}>
     */
    protected array $rule = [];

    /**
     * The messages produced by the most recent validation run.
     *
     * @var list<string>
     */
    protected array $error = [];

    /**
     * Custom named rules registered through {@see Validation::extend()}.
     *
     * @var array<string, callable>
     */
    protected array $extensions = [];

    /**
     * @param array<string, mixed> $data The data set to validate.
     */
    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    /**
     * Get the library version.
     */
    public function version(): string
    {
        return self::VERSION;
    }

    /**
     * Replace the data set to validate.
     *
     * @param array<string, mixed> $data
     * @return $this
     */
    public function setData(array $data = []): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Merge additional values into the current data set. Existing keys are
     * overwritten by the incoming values.
     *
     * @param array<string, mixed> $data
     * @return $this
     */
    public function mergeData(array $data = []): self
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Get the current data set.
     *
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Reset the queued rules, optional fields and collected errors. Registered
     * custom rules ({@see Validation::extend()}) and the loaded locale are kept.
     *
     * @return $this
     */
    public function clear(): self
    {
        $this->error = [];
        $this->rule = [];
        $this->optional = [];

        return $this;
    }

    /**
     * Register (or override) a named pattern usable by the `regex` rule.
     *
     * @param string $name    Pattern name, matched case-insensitively.
     * @param string $pattern A regular expression body without delimiters.
     * @return $this
     */
    public function pattern(string $name, string $pattern = '[\w]+'): self
    {
        $this->patterns[strtolower($name)] = $pattern;

        return $this;
    }

    /**
     * Register a custom named rule usable from the DSL string.
     *
     * The callback receives the field value as its first argument followed by
     * any arguments passed in the rule string, and must return a boolean.
     *
     * ```php
     * $validation->extend('even', static fn ($value): bool => $value % 2 === 0, '{field} must be even.');
     * $validation->rule('age', 'even');
     * ```
     *
     * @param string      $name     Rule name, matched case-insensitively.
     * @param callable    $callback Returns true when the value is valid.
     * @param string|null $message  Optional message template for this rule.
     * @return $this
     */
    public function extend(string $name, callable $callback, ?string $message = null): self
    {
        $this->extensions[strtolower($name)] = $callback;
        if ($message !== null) {
            $this->locale[$name] = $message;
        }

        return $this;
    }

    /**
     * Queue one or more rules for one or more fields.
     *
     * `$key` may be a single field name, a pipe separated list (`"a|b"`), or an
     * array of field names. `$rule` may be a pipe separated DSL string, a
     * callable, or an array mixing both. The pseudo rule `optional` marks the
     * field(s) as allowed to be absent rather than adding a check.
     *
     * @param string|array<int, string>                 $key The field(s) to validate.
     * @param string|callable|array<int, string|callable> $rule The rule(s) to apply.
     * @param string|null                                $err Optional custom message template.
     * @return $this
     *
     * @throws InvalidArgumentException When a key or rule entry has an unsupported type.
     */
    public function rule(string|array $key, string|callable|array $rule, ?string $err = null): self
    {
        $keys = \is_string($key) ? explode('|', $key) : $key;

        if (\is_string($rule)) {
            $rules = explode('|', $rule);
        } elseif (\is_callable($rule)) {
            $rules = [$rule];
        } else {
            $rules = $rule;
        }

        foreach ($keys as $field) {
            if (!\is_string($field)) {
                throw new InvalidArgumentException('Each $key must be a string.');
            }
            foreach ($rules as $singleRule) {
                if (\is_string($singleRule)) {
                    $normalized = strtolower(trim($singleRule));
                    if ($normalized === 'optional') {
                        if (!\in_array($field, $this->optional, true)) {
                            $this->optional[] = $field;
                        }
                        continue;
                    }
                    $this->rule[] = [
                        'key'  => $field,
                        'rule' => trim($singleRule),
                        'err'  => $err,
                    ];
                    continue;
                }
                if (\is_callable($singleRule)) {
                    $this->rule[] = [
                        'key'  => $field,
                        'rule' => $singleRule,
                        'err'  => $err,
                    ];
                    continue;
                }
                throw new InvalidArgumentException('Each rule must be a string or a callable.');
            }
        }

        return $this;
    }

    /**
     * Evaluate every queued rule against the data set.
     *
     * Clears the error list, runs each rule (skipping optional fields with no
     * value), then empties the rule queue. Returns true when no rule failed.
     *
     * @return bool
     *
     * @throws UndefinedRuleException When a string rule names an unknown rule.
     */
    public function validation(): bool
    {
        $this->error = [];
        foreach ($this->rule as $definition) {
            $field = $definition['key'];
            if (!isset($this->data[$field]) && \in_array($field, $this->optional, true)) {
                continue;
            }
            if (\is_string($definition['rule'])) {
                $this->validateStringRule($field, $definition['rule'], $definition['err']);
            } else {
                $this->validateCallableRule($field, $definition['rule'], $definition['err']);
            }
        }
        $this->rule = [];
        $this->optional = [];

        return $this->error === [];
    }

    /**
     * Whether the most recent {@see Validation::validation()} run had no errors.
     */
    public function isValid(): bool
    {
        return $this->error === [];
    }

    /**
     * Append a message to the error list.
     *
     * Note that {@see Validation::validation()} clears the error list at the
     * start of each run, so a message added before validating is replaced.
     *
     * @param string               $error   Message template.
     * @param array<string, mixed> $context Placeholder values for interpolation.
     * @return $this
     */
    public function setError(string $error, array $context = []): self
    {
        $this->error[] = $this->interpolate($error, $context);

        return $this;
    }

    /**
     * Get the messages produced by the most recent validation run.
     *
     * @return list<string>
     */
    public function getError(): array
    {
        return $this->error;
    }

    /**
     * Validate a field against a single DSL rule string.
     *
     * @param string      $field The field name.
     * @param string      $rule  The rule expression, e.g. `range(1...10)`.
     * @param string|null $err   Optional custom message template.
     *
     * @throws UndefinedRuleException When the rule name cannot be resolved.
     */
    private function validateStringRule(string $field, string $rule, ?string $err): void
    {
        $parsed = $this->parseRule($rule);
        $arguments = array_merge([$this->data[$field] ?? null], $parsed['args']);

        if ($this->executeRule($parsed['name'], $arguments) === false) {
            $this->addError($field, $parsed['name'], $arguments, $err);
        }
    }

    /**
     * Validate a field against a single callback rule.
     *
     * @param string      $field The field name.
     * @param callable    $rule  Receives the value and returns a boolean.
     * @param string|null $err   Optional custom message template.
     */
    private function validateCallableRule(string $field, callable $rule, ?string $err): void
    {
        $value = $this->data[$field] ?? null;
        if ((bool) $rule($value) === false) {
            $this->addError($field, 'callable', [$value], $err);
        }
    }

    /**
     * Resolve and run a rule by name.
     *
     * Built-in `rule_*` methods are tried first (case-insensitively), then
     * callbacks registered with {@see Validation::extend()}.
     *
     * @param string            $name      The rule name as written by the caller.
     * @param array<int, mixed> $arguments The value followed by the rule arguments.
     * @return bool
     *
     * @throws UndefinedRuleException When neither a method nor an extension matches.
     */
    private function executeRule(string $name, array $arguments): bool
    {
        $method = 'rule_' . strtolower($name);
        if (method_exists($this, $method)) {
            return (bool) $this->{$method}(...$arguments);
        }

        $normalized = strtolower($name);
        if (isset($this->extensions[$normalized])) {
            return (bool) ($this->extensions[$normalized])(...$arguments);
        }

        throw UndefinedRuleException::forRule($name);
    }

    /**
     * Split a DSL rule expression into its name and arguments.
     *
     * `min(5)` becomes `['name' => 'min', 'args' => ['5']]`; arguments are
     * comma separated and individually trimmed. A rule without parentheses has
     * no arguments.
     *
     * @param string $rule
     * @return array{name: string, args: list<string>}
     */
    private function parseRule(string $rule): array
    {
        $rule = trim($rule);
        if (preg_match('/^(?<name>[^(]+)\((?<args>.*)\)$/us', $rule, $matches) === 1) {
            $args = array_map('trim', explode(',', $matches['args']));
            if ($args === ['']) {
                $args = [];
            }

            return ['name' => trim($matches['name']), 'args' => $args];
        }

        return ['name' => $rule, 'args' => []];
    }

    /**
     * Build and store the message for a failed rule.
     *
     * @param string            $field     The field name.
     * @param string            $rule      The rule name (used to look up the template).
     * @param array<int, mixed> $arguments The value followed by the rule arguments.
     * @param string|null       $custom    Optional custom message template.
     */
    private function addError(string $field, string $rule, array $arguments, ?string $custom): void
    {
        $context = array_merge(['field' => $field], $arguments);
        $this->error[] = ($custom === null || $custom === '')
            ? $this->interpolate($this->message($rule), $context)
            : $this->interpolate($custom, $context);
    }
}
