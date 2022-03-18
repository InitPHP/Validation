<?php
/**
 * Validation.php
 *
 * This file is part of InitPHP.
 *
 * @author     Muhammet ŞAFAK <info@muhammetsafak.com.tr>
 * @copyright  Copyright © 2022 InitPHP
 * @license    http://initphp.github.io/license.txt  MIT
 * @version    1.0
 * @link       https://www.muhammetsafak.com.tr
 */

declare(strict_types=1);

namespace InitPHP\Validation;

use function array_merge;
use function is_string;
use function is_array;
use function is_callable;
use function in_array;
use function explode;
use function strtolower;
use function method_exists;
use function function_exists;
use function call_user_func_array;
use function preg_match;
use function preg_replace;
use function array_merge_recursive;
use function trim;

class Validation
{
    use LocaleTrait, RulesTrait;

    public const VERSION = '1.0';

    protected array $data = [];

    protected array $optional = [];

    protected array $rule = [];

    protected array $error = [];

    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    public function __destruct()
    {
        $this->data = [];
        $this->locale_tmp = [];
        $this->clear();
    }

    public function version(): string
    {
        return self::VERSION;
    }

    public function setData(array $data = []): self
    {
        $this->data = $data;
        return $this;
    }

    public function mergeData(array $data = []): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function clear(): self
    {
        $this->error = [];
        $this->rule = [];
        $this->optional = [];
        return $this;
    }

    /**
     * @param string|string[] $key
     * @param string|callable|string[]|callable[] $rule
     * @param string|null $err
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function rule($key, $rule, ?string $err = null): self
    {
        if(!is_string($key) && !is_array($key)){
            throw new \InvalidArgumentException("The \$key parameter can only be string|string[].");
        }
        if(!is_string($rule) && !is_callable($rule) && !is_array($rule)){
            throw new \InvalidArgumentException("The \$rule parameter can only be string|callable|string[]|callable[].");
        }
        $keys = is_string($key) ? explode('|', $key) : $key;

        if(is_string($rule)){
            $rules = explode('|', $rule);
        }elseif(is_callable($rule)){
            $rules = [$rule];
        }else{
            $rules = $rule;
        }
        foreach ($rules as $rule) {
            if(!is_string($rule) && !is_callable($rule)){
                throw new \InvalidArgumentException("The \$rule parameter can only be string|callable|string[]|callable[].");
            }
            foreach ($keys as $key) {
                if(!is_string($key)){
                    throw new \InvalidArgumentException("The \$key parameter can only be string|string[].");
                }
                if(strtolower($rule) === 'optional'){
                    $this->optional[] = $key;
                    continue;
                }
                $this->rule[] = [
                    'key'       => $key,
                    'rule'      => $rule,
                    'err'       => $err
                ];
            }
        }
        return $this;
    }

    public function validation(): bool
    {
        $this->error = [];
        foreach ($this->rule as $rule) {
            if(is_string($rule['rule'])){
                $this->stringProcessValidation($rule);
            }else{
                $this->callableProcessValidation($rule);
            }
        }
        return empty($this->error);
    }

    public function setError(string $error, array $context = []): self
    {
        $this->error[] = $this->interpolate($error, $context);
        return $this;
    }

    public function getError(): array
    {
        return $this->error ?? [];
    }

    private function stringProcessValidation($rule): bool
    {
        $field = ['field' => $rule['key']];
        $parse = $this->prepareArguments($rule['rule'], $rule['key']);
        $validMethod = 'rule_' . strtolower($parse['method']);
        $context = array_merge($field, $parse['arguments']);
        if(method_exists($this, $validMethod) !== FALSE){
            $res = $this->{$validMethod}(...$parse['arguments']);
            if($res === FALSE && !in_array($rule['key'], $this->optional, true)){
                $this->error[] = empty($rule['err']) ? $this->__r($rule['key'], $parse['method'], $context) : $this->interpolate($rule['err'], $context);
                return false;
            }
            return true;
        }
        if(function_exists($parse['method'])){
            $res = (bool)call_user_func_array($parse['method'], $parse['arguments']);
            if($res === FALSE && !in_array($rule['key'], $this->optional, true)){
                $this->error[] = empty($rule['err']) ? $this->__r($rule['key'], $parse['method'], $context) : $this->interpolate($rule['err'], $context);
                return false;
            }
            return true;
        }
        return false;
    }

    private function callableProcessValidation($rule): bool
    {
        $arguments = [($this->data[$rule['key']] ?? null)];
        $res = (bool)call_user_func_array($rule['rule'], $arguments);
        $field = ['field' => $rule['key']];
        if($res === FALSE && !in_array($rule['key'], $this->optional, true)){
            $this->error[] = empty($rule['err']) ? $this->__r($rule['key'], 'callable', $field) : $this->interpolate($rule['err'], $field);
            return false;
        }
        return true;
    }

    private function prepareArguments(string $rule, string $key): array
    {
        $arguments = [($this->data[$key] ?? null)];
        preg_match("/\((.*)\)/u", $rule, $params);
        if(isset($params[0])){
            $method = preg_replace("/\((.*)\)/u", '', $rule);
            $arguments = array_merge_recursive($arguments, explode(',', trim($params[0], '()')));
        }else{
            $method = $rule;
        }
        return [
            'method'        => $method,
            'arguments'     => $arguments
        ];
    }

}
