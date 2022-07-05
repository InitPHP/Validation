<?php
/**
 * RulesTrait.php
 *
 * This file is part of InitPHP.
 *
 * @author     Muhammet ŞAFAK <info@muhammetsafak.com.tr>
 * @copyright  Copyright © 2022 InitPHP
 * @license    http://initphp.github.io/license.txt  MIT
 * @version    1.0.2
 * @link       https://www.muhammetsafak.com.tr
 */

declare(strict_types=1);

namespace InitPHP\Validation;

use const PHP_URL_HOST;
use const FILTER_VALIDATE_IP;
use const FILTER_FLAG_IPV6;
use const FILTER_FLAG_IPV4;
use const FILTER_VALIDATE_URL;
use const FILTER_VALIDATE_EMAIL;

use function function_exists;
use function in_array;
use function is_string;
use function is_numeric;
use function is_bool;
use function mb_strpos;
use function mb_stripos;
use function mb_substr;
use function mb_strlen;
use function end;
use function mb_strtolower;
use function filter_var;
use function date_parse_from_format;
use function strtotime;
use function preg_match;
use function strtolower;
use function explode;
use function strpos;
use function count;
use function trim;
use function parse_url;
use function implode;

trait RulesTrait
{
    private array $constPattern = [
        'float'         => '[+-]?([0-9]*[.])?[0-9]+',
        'int'           => '\d+',
        'numeric'       => '[+-]?([0-9]*[.])?[0-9]+',
        'boolean'       => 'true|false|1|0',
        'alpha'         => '[\p{L}]+',
        'alphanumeric'  => '[\p{L}0-9]+',
        'creditCard'    => [
            'amex'          => '(3[47]\d{13})',
            'visa'          => '(4\d{12}(?:\d{3})?)',
            'mastercard'    => '(5[1-5]\d{14})',
            'maestro'       => '((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)',
            'jcb'           => '(35[2-8][89]\d\d\d{10})',
            'solo'          => '((?:6334|6767)\d{12}(?:\d\d)?\d?)',
            'switch'        => '(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)',
        ],
    ];

    protected array $patterns = [
        'uri' => '[A-Za-z0-9-\/_?&=]+',
        'slug' => '[-a-z0-9_-]',
        'url' => '[A-Za-z0-9-:.\/_?&=#]+',
        'alpha' => '[\p{L}]+',
        'words' => '[\p{L}\s]+',
        'alphanum' => '[\p{L}0-9]+',
        'int' => '[0-9]+',
        'float' => '[0-9\.,]+',
        'tel' => '[0-9+\s()-]+',
        'text' => '[\p{L}0-9\s-.,;:!"%&()?+\'°#\/@]+',
        'file' => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+\.[A-Za-z0-9]{2,4}',
        'folder' => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+',
        'address' => '[\p{L}0-9\s.,()°-]+',
        'date_dmy' => '[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}',
        'date_ymd' => '[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}',
        'email' => '[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+[.]?[a-z-A-Z]?'
    ];

    protected function rule_integer($data): bool
    {
        if(is_int($data)){
            return true;
        }
        if(!is_string($data)){
            return false;
        }
        return (bool)preg_match('/^(' . $this->constPattern['int'] . ')$/u', $data);
    }

    protected function rule_float($data): bool
    {
        if(is_float($data)){
            return true;
        }
        if(!is_string($data)){
            return false;
        }
        return (bool)preg_match('/^(' . $this->constPattern['float'] . ')$/u', $data);
    }

    protected function rule_alpha($data): bool
    {
        return is_string($data) && (bool)preg_match('/^(' . $this->constPattern['alpha'] . ')$/u', $data);
    }

    protected function rule_alphanum($data): bool
    {
        return is_string($data) && (bool)preg_match('/^(' . $this->constPattern['alphanumeric'] . ')$/u', $data);
    }

    protected function rule_alpanumeric($data): bool
    {
        return $this->rule_alphanum($data);
    }

    protected function rule_creditcard($data, $type = null): bool
    {
        $data = (string)$data;
        $data = str_replace(' ', '', $data);

        if(empty($type)){
            return (bool)preg_match('/^(?:' . implode('|', $this->constPattern['creditCard']) . ')$/', $data);
        }
        $type = strtolower((string)$type);
        if(isset($this->constPattern['creditCard'][$type])){
            return (bool)preg_match('/^' . $this->constPattern['creditCard'][$type] . '$/', $data);
        }
        return false;
    }

    protected function rule_numeric($data): bool
    {
        if(is_numeric($data)){
            return true;
        }
        return (bool)preg_match('/^(' . $this->constPattern['numeric'] . ')$/u', (string)$data);
    }

    protected function rule_string($data): bool
    {
        return is_string($data);
    }

    protected function rule_boolean($data): bool
    {
        if(is_bool($data)){
            return true;
        }
        return (bool)preg_match('/^(' . $this->constPattern['boolean'] . ')$/u', (string)$data);
    }

    protected function rule_array($data): bool
    {
        return is_array($data);
    }

    protected function rule_mail($data): bool
    {
        return (bool)filter_var((string)$data, FILTER_VALIDATE_EMAIL);
    }

    protected function rule_mailhost($data, ...$domain): bool
    {
        if(filter_var($data, FILTER_VALIDATE_EMAIL)){
            $parse = explode('@', $data, 2);
            $mailHost = trim(($parse[1] ?? ''));
            foreach ($domain as $host) {
                $host = trim($host);
                if(empty($host)){
                    continue;
                }
                if($mailHost === $host){
                    return true;
                }
            }
        }
        return false;
    }

    protected function rule_url($data): bool
    {
        return (bool)filter_var((string)$data, FILTER_VALIDATE_URL);
    }

    protected function rule_urlhost($data, ...$domains): bool
    {
        if(filter_var($data, FILTER_VALIDATE_URL)){
            $host = parse_url($data, PHP_URL_HOST);
            foreach ($domains as $domain) {
                $domain = trim($domain);
                if(empty($domain)){
                    continue;
                }
                if($domain === $host){
                    return true;
                }
                if(mb_substr($host, (0 - (mb_strlen($domain) + 1))) === '.' . $domain){
                    return true;
                }
            }
        }
        return false;
    }

    protected function rule_empty($data): bool
    {
        return empty(trim((string)$data));
    }

    protected function rule_required($data): bool
    {
        if(is_numeric($data)){
            return true;
        }
        return !empty(trim((string)$data));
    }

    protected function rule_min($data, $min): bool
    {
        if(is_numeric($data)){
            return ($data >= $min);
        }
        if((bool)preg_match('/^(' . $this->constPattern['numeric'] . ')$/u', (string)$data) !== FALSE){
            return ($data >= $min);
        }
        $data = is_array($data) ? count($data) : mb_strlen((string)$data);
        return ($data >= $min);
    }

    protected function rule_max($data, $max): bool
    {
        if(is_numeric($data)){
            return ($data <= $max);
        }
        if((bool)preg_match('/^(' . $this->constPattern['numeric'] . ')$/u', (string)$data) !== FALSE){
            return ($data <= $max);
        }
        $data = is_array($data) ? count($data) : mb_strlen((string)$data);
        return ($data <= $max);
    }

    protected function rule_range($data, $range): bool
    {
        $separator = strpos($range, '-') ? '-' : '...';
        $range = explode($separator, $range, 2);
        $min = empty($range[0]) ? null : $range[0];
        $max = isset($range[1]) && !empty($range[1]) ? $range[1] : null;
        if(!empty($min) && ($min > $data)){
            return false;
        }
        if(!empty($max) && ($max < $data)){
            return false;
        }
        return true;
    }

    protected function rule_length($data, $range): bool
    {
        $min = null;
        $max = (is_numeric($range) && $range > 0) ? $range : null;
        $len = (is_string($data) || is_numeric($data)) ? mb_strlen((string)$data) : count($data);
        if(is_string($range)){
            $separator = strpos($range, '...') ? '...' : '-';
            $parse = explode($separator, $range, 2);
            $min = $parse[0] ?? null;
            $max = $parse[1] ?? null;
        }
        if(is_numeric($min) && $len < $min){
            return false;
        }
        if(is_numeric($max) && $max > 0 && $len > $max){
            return false;
        }
        return true;
    }

    protected function rule_regex($data, $pattern): bool
    {
        $pattern = '/^(' . ($this->patterns[\strtolower($pattern)] ?? $pattern) . ')$/u';
        return (bool)preg_match($pattern, (string)$data);
    }

    protected function rule_date($data): bool
    {
        if($data instanceof \DateTime){
            return true;
        }
        return (strtotime($data) !== FALSE);
    }

    protected function rule_dateformat($data, $format): bool
    {
        $dateFormat = date_parse_from_format($format, $data);
        return ($dateFormat['error_count'] === 0 && $dateFormat['warning_count'] === 0);
    }

    protected function rule_ip($data): bool
    {
        return (bool)filter_var($data, FILTER_VALIDATE_IP);
    }

    protected function rule_ipv4($data): bool
    {
        return (bool)filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    protected function rule_ipv6($data): bool
    {
        return (bool)filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    protected function rule_only($data, ...$only): bool
    {
        $data = mb_strtolower((string)$data);
        foreach($only as $row){
            if(mb_strtolower((string)$row) === $data){
                return true;
            }
        }
        return false;
    }

    protected function rule_strictonly($data, ...$only): bool
    {
        foreach($only as $row){
            if($row == $data){
                return true;
            }
        }
        return false;
    }

    protected function rule_again($data, $key): bool
    {
        if(!isset($this->data[$key])){
            return false;
        }
        return $data === $this->data[$key];
    }

    protected function rule_equals($data, $eqData): bool
    {
        return $data === $eqData;
    }

    protected function rule_startwith($data, $startWith): bool
    {
        if(is_array($data)){
            return ($data[0] ?? ($startWith !== null ? null : '')) == $startWith;
        }
        $len = mb_strlen((string)$startWith);
        return (mb_substr((string)$data, 0, $len) === (string)$startWith);
    }

    protected function rule_endwith($data, $endWith): bool
    {
        if(is_array($data)){
            return end($data) === $endWith;
        }
        $endWith = (string)$endWith;
        $len = mb_strlen($endWith);
        return (mb_substr((string)$data, -($len)) == $endWith);
    }

    protected function rule_in($data, $search): bool
    {
        if(is_string($data) || is_numeric($data)){
            return mb_stripos((string)$data, (string)$search) !== FALSE;
        }
        if(is_array($data)){
            return in_array($search, $data, true) !== FALSE;
        }
        return false;
    }

    protected function rule_notin($data, $search): bool
    {
        if(is_string($data) || is_numeric($data)){
            return mb_stripos((string)$data, (string)$search) === FALSE;
        }
        if(is_array($data)){
            return in_array($search, $data, true) === FALSE;
        }
        return false;
    }

    protected function rule_contains($data, $search): bool
    {
        if(function_exists('str_contains')){
            return \str_contains((string)$data, (string)$search);
        }
        return mb_strpos((string)$data, (string)$search) !== FALSE;
    }

    protected function rule_notcontains($data, $search): bool
    {
        if(function_exists('str_contains')){
            return !\str_contains((string)$data, (string)$search);
        }
        return !(mb_strpos((string)$data, (string)$search) !== FALSE);
    }

}
