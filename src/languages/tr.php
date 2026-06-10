<?php

/**
 * Turkish (tr) validation messages for InitPHP Validation.
 *
 * Keys are rule names (matched case-insensitively). `{field}` is the field
 * label, `{1}` is the value and `{2}` is the first rule argument.
 */

declare(strict_types=1);

return [
    'notValidDefault' => '{field} doğrulanamadı.',
    'callable'        => '{field} doğrulanamadı.',
    'integer'         => '{field} bir tam sayı olmalıdır.',
    'float'           => '{field} ondalıklı bir sayı olmalıdır.',
    'numeric'         => '{field} sayısal bir değer olmalıdır.',
    'string'          => '{field} bir dize olmalıdır.',
    'boolean'         => '{field} mantıksal bir değer olmalıdır.',
    'array'           => '{field} bir dizi olmalıdır.',
    'mail'            => '{field} bir e-posta adresi olmalıdır.',
    'mailHost'        => '{field} {2} adresine ait bir e-posta olmalıdır.',
    'url'             => '{field} bir URL adresi olmalıdır.',
    'urlHost'         => '{field} {2} hostuna ait bir URL olmalıdır.',
    'empty'           => '{field} boş olmalıdır.',
    'required'        => '{field} boş bırakılamaz.',
    'min'             => '{field} en az {2} olmalıdır.',
    'max'             => '{field} en fazla {2} olabilir.',
    'length'          => '{field} uzunluğu {2} olmalıdır.',
    'range'           => '{field} {2} aralığında olmalıdır.',
    'regex'           => '{field} {2} desenine uymalıdır.',
    'date'            => '{field} bir tarih olmalıdır.',
    'dateFormat'      => '{field} {2} formatında geçerli bir tarih olmalıdır.',
    'ip'              => '{field} bir IP adresi olmalıdır.',
    'ipv4'            => '{field} bir IPv4 adresi olmalıdır.',
    'ipv6'            => '{field} bir IPv6 adresi olmalıdır.',
    'again'           => '{field} {2} ile aynı olmalıdır.',
    'equals'          => '{field} değeri yalnızca {2} olabilir.',
    'startWith'       => '{field} "{2}" ile başlamalıdır.',
    'endWith'         => '{field} "{2}" ile bitmelidir.',
    'in'              => '{field} {2} içermelidir.',
    'notIn'           => '{field} {2} içeremez.',
    'alpha'           => '{field} yalnızca alfabetik karakterlerden oluşmalıdır.',
    'alphaNum'        => '{field} yalnızca alfanümerik karakterlerden oluşmalıdır.',
    'alphanumeric'    => '{field} yalnızca alfanümerik karakterlerden oluşmalıdır.',
    'creditCard'      => '{field} geçerli bir kredi kartı numarası olmalıdır.',
    'only'            => '{field} doğrulanamadı.',
    'strictOnly'      => '{field} doğrulanamadı.',
    'contains'        => '{field} {2} içermelidir.',
    'notContains'     => '{field} {2} içeremez.',
];
