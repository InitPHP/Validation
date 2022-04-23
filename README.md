# InitPHP Validation

Simple and fast library for verifying that the data is of the type and structure you want.

[![Latest Stable Version](http://poser.pugx.org/initphp/validation/v)](https://packagist.org/packages/initphp/validation) [![Total Downloads](http://poser.pugx.org/initphp/validation/downloads)](https://packagist.org/packages/initphp/validation) [![Latest Unstable Version](http://poser.pugx.org/initphp/validation/v/unstable)](https://packagist.org/packages/initphp/validation) [![License](http://poser.pugx.org/initphp/validation/license)](https://packagist.org/packages/initphp/validation) [![PHP Version Require](http://poser.pugx.org/initphp/validation/require/php)](https://packagist.org/packages/initphp/validation)

- [ChangeLog](./CHANGELOG.md)

## Requirements

- PHP 7.4 or higher
- PHP MB_String Extension

## Installation

```
composer require initphp/validation
```

# Validation Rules

- `integer` : Verifies that the data is an integer.
- `float` : Verifies that the data is a floating point number.
- `numeric` : Verifies that the data is a numeric value.
- `string` : Verifies that the data is of a string type.
- `boolean` : Verifies that the data has a logical value or equivalent.
- `array` : Verifies that the data is an array.
- `mail` : Verifies that the data is an E-Mail address.
- `mailHost` : Verifies that the data is the E-Mail address using the specified host.
- `url` : Verifies that the data is a URL address.
- `urlHost` : Verifies that the data is a URL of the specified host (or subdomain).
- `empty` : Verifies that the data is empty.
- `required` : Verifies that the data is not null.
- `min` : Defines the minimum value the data can have. Specifies the minimum number of elements/characters if the data is a string or array.
- `max` : Defines the maximum value the data can have. Specifies the maximum number of elements/characters if the data is a string or array.
- `length` : Verifies that the number of characters is within the specified range.
- `range` : The numeric value must be within the specified range.
- `regex` : Validates data with a predefined or postdefined regular expression.
- `date` : Attempts to verify that the data is a date.
- `dateFormat` : Verifies that the data is a date in a specified format.
- `ip` : Verifies that the data is IP address.
- `ipv4` : Verifies that the data is IPv4.
- `ipv6` : Verifies that the data is IPv6.
- `again` : Verifies that the data is the same as the value of a data key.
- `equals` : Verifies that the data is equivalent to the specified value.
- `startWith` : Verifies that the data starts with the specified value.
- `endWith` : Array or String. Verifies that the data ends with the specified value.
- `in` : Case-insensitive verifies that the specified value is in the data.
- `notIn` : Case-insensitive verifies that the specified value is not in the data.
- `contains` : Verifies that the specified case-sensitive value is in the data.
- `notContains` : Verifies that the specified case-sensitive value is not in the data.
- `alpha` : Verifies that the data is an alpha value.
- `alphaNum` : Verifies that the data is an alphanumeric value.
- `creditCard` : Verifies that the data is a credit card number.
- `only` : It validates only one of the specified values, case-insensitively.
- `strictOnly` : Case sensitive only validates that it is one of the values specified.
- `optional` : If there is data, it must obey the rules, but if there is no data with the corresponding key, the validation will not fail.

## Usage

```php
require_once "vendor/autoload.php";
use \InitPHP\Validation\Validation;

$validation = new Validation($_GET);

// GET /?name=Muhammet&year=2022

$validation->rule('name', 'string');
$validation->rule('year', 'integer|range(1970...2099)');

if($validation->validation()){
    // ... process
}else{
    foreach ($validation->getError() as $err) {
        echo $err . "<br />\n";
    }
}
```
**Again Usage**
```php
require_once "vendor/autoload.php";
use \InitPHP\Validation\Validation;

$validation = new Validation($_GET);

// GET /?password=123a456&password_again=123a456

$validation->rule('password', 'string');
$validation->rule('password', 'again(password_again)');

if($validation->validation()){
    // ... process
}else{
    foreach ($validation->getError() as $err) {
        echo $err . "<br />\n";
    }
}
```

**Callable verification rule;**

```php
require_once "vendor/autoload.php";
use \InitPHP\Validation\Validation;

$validation = new Validation($_GET);

// GET /?number=13

$validation->rule('number', function ($data) {
    if(($data % 2) == 0){
        return true;
    }
    return false;
}, "{field} must be an even number.");

if($validation->validation()){
    // ... process
}else{
    foreach ($validation->getError() as $err) {
        echo $err . "<br />\n";
    }
}
```

## Getting Help

If you have questions, concerns, bug reports, etc, please file an issue in this repository's Issue Tracker.

## Getting Involved

> All contributions to this project will be published under the MIT License. By submitting a pull request or filing a bug, issue, or feature request, you are agreeing to comply with this waiver of copyright interest.

There are two primary ways to help:

- Using the issue tracker, and
- Changing the code-base.
    
### Using the issue tracker

Use the issue tracker to suggest feature requests, report bugs, and ask questions. This is also a great way to connect with the developers of the project as well as others who are interested in this solution.

Use the issue tracker to find ways to contribute. Find a bug or a feature, mention in the issue that you will take on that effort, then follow the Changing the code-base guidance below.

### Changing the code-base

Generally speaking, you should fork this repository, make changes in your own fork, and then submit a pull request. All new code should have associated unit tests that validate implemented features and the presence or lack of defects. Additionally, the code should follow any stylistic and architectural guidelines prescribed by the project. In the absence of such guidelines, mimic the styles and patterns in the existing code-base.

## Credits

- [Muhammet ŞAFAK](https://www.muhammetsafak.com.tr) <<info@muhammetsafak.com.tr>>

## License

Copyright &copy; 2022 [MIT License](./LICENSE) 
