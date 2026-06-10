<?php

/**
 * English (en) validation messages for InitPHP Validation.
 *
 * Keys are rule names (matched case-insensitively). `{field}` is the field
 * label, `{1}` is the value and `{2}` is the first rule argument.
 */

declare(strict_types=1);

return [
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
