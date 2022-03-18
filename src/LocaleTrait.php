<?php
/**
 * LocaleTrait.php
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

use function strtr;
use function substr;
use function is_int;
use function is_string;
use function is_object;
use function is_array;
use function is_file;
use function is_dir;
use function method_exists;
use function array_merge;

trait LocaleTrait
{

    protected string $LT_Path = __DIR__ . '/languages/';

    protected array $locale_tmp = [];

    protected array $locale = [
        'labels'            => [],
        'notValidDefault'   => 'The {field} value is not valid.',
        'integer'           => '{field} must be an integer.',
        'float'             => '{field} must be an float.',
        'numeric'           => '{field} must be an numeric.',
        'string'            => '{field} must be an string.',
        'boolean'           => '{field} must be an boolean',
        'array'             => '{field} must be an Array.',
        'mail'              => '{field} must be an E-Mail address.',
        'mailHost'          => '{field} the email must be a {2} mail.',
        'url'               => '{field} must be an URL address.',
        'urlHost'           => 'The host of the {field} url must be {2}.',
        'empty'             => '{field} must be empty.',
        'required'          => '{field} cannot be left blank.',
        'min'               => '{field} must be greater than or equal to {2}.',
        'max'               => '{field} must be no more than {2}.',
        'length'            => 'The {field} length range must be {2}.',
        'range'             => 'The {field} range must be {2}.',
        'regex'             => '{field} must match the {2} pattern.',
        'date'              => '{field} must be a date.',
        'dateFormat'        => '{field} must be a correct date format.',
        'ip'                => '{field} must be the IP Address.',
        'ipv4'              => '{field} must be the IPv4 Address.',
        'ipv6'              => '{field} must be the IPv6 Address.',
        'repeat'            => '{field} must be the same as {field1}',
        'equals'            => '{field} can only be {2}.',
        'startWith'         => '{field} must start with "{2}".',
        'endWith'           => '{field} must end with "{2}".',
        'in'                => '{field} must contain {2}.',
        'notIn'             => '{field} must not contain {2}.',
        'alpha'             => '{field} must contain only alpha characters.',
        'alphaNum'          => '{field} can only be alphanumeric.',
        'creditCard'        => '{field} must be a credit card number.',
        'only'              => 'The {field} value is not valid.',
        'strictOnly'        => 'The {field} value is not valid.',
        'contains'          => '{field} must contain {2}.',
        'notContains'       => '{field} must not contain {2}.',
    ];

    public function setLocaleDir(string $dir = __DIR__ . '/languages/'): self
    {
        if(!is_dir($dir)){
            throw new \InvalidArgumentException('An existing directory ("' . $dir . '") must be selected for language definitions.');
        }
        $this->LT_Path = $dir;
        return $this;
    }

    public function setLocale(string $locale = 'en'): self
    {
        if(isset($this->locale_tmp[$locale])){
            return $this->setLocaleArray($this->locale_tmp[$locale]);
        }
        $path = rtrim($this->LT_Path, '/\\') . DIRECTORY_SEPARATOR . $locale . '.php';
        if(!is_file($path)){
            throw new \InvalidArgumentException('Could not find ("' . $path . '") file for language definitions.');
        }
        $array = $this->requirePHP($path);
        if(!is_array($array)){
            throw new \Exception('The file "' . $path . '" should return an array.');
        }
        $this->locale_tmp[$locale] = $array;
        return $this->setLocaleArray($array);
    }

    public function setLocaleArray(array $assoc): self
    {
        $this->locale = array_merge($this->locale, $assoc);
        return $this;
    }

    public function labels(array $assoc): self
    {
        $this->locale['labels'] = array_merge($this->locale['labels'], $assoc);
        return $this;
    }

    protected function __r($field, $key, array $context = []): string
    {
        $lang = $this->locale[$field][$key] ?? ($this->locale[$key] ?? $this->locale['notValidDefault']);
        return $this->interpolate($lang, $context);
    }

    protected function interpolate(string $message, array $context = []): string
    {
        if(empty($context)){
            return $message;
        }
        $replace = array();
        $i = 0;
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                if((is_string($val) || is_int($val) && substr((string)$key, 0, 5) == 'field')){
                    $val = $this->locale['labels'][$val] ?? $val;
                }
                $replace['{' . $key . '}'] = $val;
                $replace['{' . $i . '}'] = $val;
                ++$i;
            }
        }

        return strtr($message, $replace);
    }

    private function requirePHP(string $path)
    {
        return require $path;
    }

}
