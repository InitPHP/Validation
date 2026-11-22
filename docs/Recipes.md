# Recipes

Practical, copy-pasteable patterns built only from the public API. See the
[API Reference](API-Reference.md) and [Rules Reference](Rules-Reference.md).

## Validate a registration form

```php
use InitPHP\Validation\Validation;

$v = new Validation($_POST);

$valid = $v
    ->rule('username', 'required|alphanum|length(3...20)')
    ->rule('email', 'required|mail')
    ->rule('password', 'required|length(8...)')
    ->rule('confirm', 'again(password)')
    ->rule('terms', 'equals(1)')
    ->validation();

if (!$valid) {
    $errors = $v->getError();
}
```

## A login form

```php
$v = new Validation($_POST);

$valid = $v
    ->rule('email', 'required|mail')
    ->rule('password', 'required')
    ->validation();
```

## A REST API JSON payload

```php
$payload = json_decode((string) file_get_contents('php://input'), true) ?? [];

$v = new Validation($payload);

$valid = $v
    ->rule('title', 'required|string|length(1...120)')
    ->rule('body', 'required|string')
    ->rule('status', 'only(draft, published, archived)')
    ->rule('tags', 'optional|array')
    ->rule('published_at', 'optional|dateFormat(Y-m-d)')
    ->validation();

if (!$valid) {
    http_response_code(422);
    echo json_encode(['errors' => $v->getError()]);
    exit;
}
```

## A password change

```php
$v = new Validation($_POST);

$valid = $v
    ->rule('current_password', 'required')
    ->rule('new_password', 'required|length(10...)|notIn(password)')
    ->rule('new_password_confirm', 'again(new_password)')
    ->validation();
```

## Query-string filters with optional fields

```php
// GET /products?min_price=10&in_stock=1
$v = new Validation($_GET);

$valid = $v
    ->rule('min_price', 'optional|numeric|min(0)')
    ->rule('max_price', 'optional|numeric|min(0)')
    ->rule('in_stock', 'optional|boolean')
    ->rule('sort', 'optional|only(price, name, newest)')
    ->validation();
```

See [Optional Fields](Optional-Fields.md) for the exact "absent" semantics.

## A reusable, pre-configured validator

Configure language, labels and custom rules **once**, then reuse the instance
for many *queue → validate → read* cycles:

```php
use InitPHP\Validation\Validation;

$v = new Validation();
$v->setLocale('en');
$v->labels([
    'email' => 'E-mail address',
    'dob'   => 'Date of birth',
]);
$v->extend('adult', static function ($value): bool {
    return strtotime((string) $value) <= strtotime('-18 years');
}, '{field} must be 18 or older.');

// later, per request:
$v->setData($_POST)
  ->rule('email', 'required|mail')
  ->rule('dob', 'required|date|adult');
$ok = $v->validation();
```

Custom rules, patterns, labels and the locale survive `validation()` — only the
rule queue and errors reset.

## Errors grouped by field

`getError()` is a flat list. Validate each field on its own run to key the
messages by field:

```php
function validateFields(Validation $v, array $rules): array
{
    $errors = [];
    foreach ($rules as $field => $rule) {
        $v->rule($field, $rule);
        if (!$v->validation()) {
            $errors[$field] = $v->getError();
        }
    }
    return $errors;
}

$errors = validateFields(new Validation($_POST), [
    'username' => 'required|alphanum',
    'email'    => 'required|mail',
]);
```

## Validate a list of items

```php
$rows = [
    ['sku' => 'AB-1234', 'qty' => '2'],
    ['sku' => 'bad',     'qty' => 'x'],
];

$v = new Validation();
$v->pattern('sku', '[A-Z]{2}-[0-9]{4}');

$problems = [];
foreach ($rows as $i => $row) {
    $v->setData($row)
      ->rule('sku', 'required|regex(sku)')
      ->rule('qty', 'required|integer|min(1)');
    if (!$v->validation()) {
        $problems[$i] = $v->getError();
    }
}
```

## A custom domain rule

```php
$v = new Validation(['website' => 'https://example.org']);

$v->extend(
    'httpsUrl',
    static fn ($value): bool => is_string($value) && str_starts_with($value, 'https://'),
    '{field} must use HTTPS.'
);

$ok = $v->rule('website', 'required|url|httpsUrl')->validation();
```

## Next

- [Callable & Custom Rules](Callable-and-Custom-Rules.md) — more on `extend()`.
- [Testing](Testing.md) — test your validation logic.
