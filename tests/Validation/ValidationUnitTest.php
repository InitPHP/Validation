<?php
declare(strict_types=1);
namespace Tests\InitPHP\Validation;

use InitPHP\Validation\Validation;

class ValidationUnitTest extends \PHPUnit\Framework\TestCase
{

    protected Validation $validation;

    protected function setUp(): void
    {
        $this->validation = new Validation();
        parent::setUp();
    }

    public function testFloatRuleValid()
    {
        $this->validation->setData([
            'id'        => '12', // Every integer is also a float value.
            'name'      => 'product',
            'price'     => '13.50',
            'discount'  => '-2.00',
        ]);

        $this->validation->rule('id', 'float');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('name', 'float');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('price', 'float');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('discount', 'float');
        $this->assertTrue($this->validation->validation());
    }

    public function testIntegerRuleValid()
    {
        $this->validation->setData([
            'id'        => '12',
            'name'      => 'product',
            'price'     => '13.50',
            'discount'  => '-2.00',
        ]);

        $this->validation->rule('id', 'integer');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('name', 'integer');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('price', 'integer');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('discount', 'integer');
        $this->assertFalse($this->validation->validation());
    }

    public function testAlphaRuleValid()
    {
        $this->validation->setData([
            'id'        => '12',
            'name'      => 'product',
            'title'     => 'Product Name' // The space character is not an alphabetic value.
        ]);

        $this->validation->rule('id', 'alpha');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('name', 'alpha');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('title', 'alpha');
        $this->assertFalse($this->validation->validation());

    }

    public function testAlphaNumRuleValid()
    {
        $this->validation->setData([
            'id'        => '12',
            'name'      => 'product',
            'title'     => 'Product Name' // The space character is not an alphanumeric value.
        ]);

        $this->validation->rule('id', 'alphanum');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('name', 'alphanum');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('title', 'alphanum');
        $this->assertFalse($this->validation->validation());

    }

    public function testNumericRuleValid()
    {
        $this->validation->setData([
            'id'        => '12',
            'name'      => 'Product',
            'price'     => '19.99',
            'discount'  => '-2.00',
        ]);

        $this->validation->rule('id', 'numeric');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('name', 'numeric');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('price', 'numeric');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('discount', 'numeric');
        $this->assertTrue($this->validation->validation());
    }

    public function testCreditCardRuleValid()
    {
        $this->validation->setData([
            'id'                => '12334',
            'name'              => 'John',
            'amex_card'         => '3700 0000 0000 002',
            'jcb_card'          => '3569 9900 1009 5841',
            'maestro_card'      => '6761 7980 2100 0008',
            'master_card'       => '5577 0000 5577 0004',
            'visa_card'         => '4988 4388 4388 4305',
        ]);

        $this->validation->rule('id', 'creditcard');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('name', 'creditcard');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('amex_card', 'creditcard');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('amex_card', 'creditcard(amex)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('jcb_card', 'creditcard');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('jcb_card', 'creditcard(jcb)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('maestro_card', 'creditcard');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('maestro_card', 'creditcard(maestro)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('master_card', 'creditcard');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('master_card', 'creditcard(mastercard)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('visa_card', 'creditcard');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('visa_card', 'creditcard(visa)');
        $this->assertTrue($this->validation->validation());
    }

    public function testStringRuleValid()
    {
        // This validation method tests the type of data.

        $this->validation->setData([
            'id'        => 12, // It is an integer, not a string
            'name'      => 'admin',
            'time'      => '12345678901' // This is a string.
        ]);

        $this->validation->rule('id', 'string');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('name', 'string');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('time', 'string');
        $this->assertTrue($this->validation->validation());
    }

    public function testBooleanRuleValid()
    {
        // All values defined below are considered boolean.
        $this->validation->setData([
            'key_1'     => 0,
            'key_2'     => 1,
            'key_3'     => true,
            'key_4'     => false,
            'key_5'     => '0',
            'key_6'     => '1',
            'key_7'     => 'true',
            'key_8'     => 'false',
        ]);

        $this->validation->rule('key_1', 'boolean');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_2', 'boolean');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_3', 'boolean');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_4', 'boolean');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_5', 'boolean');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_6', 'boolean');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_7', 'boolean');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_8', 'boolean');
        $this->assertTrue($this->validation->validation());

    }

    public function testMailRuleValid()
    {
        $this->validation->setData([
            'mail_1'        => 'example@gmail.com',
            'mail_2'        => 'example@outlook.com',
            'mail_3'        => 'random value'
        ]);

        $this->validation->rule('mail_1', 'mail');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('mail_1', 'mailHost(gmail.com)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('mail_1', 'mailHost(hotmail.com)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('mail_1', 'mailHost(outlook.com,hotmail.com,google.com)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('mail_1', 'mailHost(outlook.com,hotmail.com,gmail.com)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('mail_2', 'mail');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('mail_2', 'mailHost(outlook.com)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('mail_2', 'mailHost(gmail.com)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('mail_3', 'mail');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('mail_3', 'mailHost(gmail.com)');
        $this->assertFalse($this->validation->validation());
    }

    public function testUrlRuleValid()
    {
        $this->validation->setData([
            'url_1'         => 'http://www.example.com',
            'url_2'         => 'http://www.example.com/path?x=y',
        ]);

        $this->validation->rule('url_1', 'url');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('url_2', 'url');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('url_1', 'urlHost(google.com)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('url_1', 'urlHost(example.com)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('url_1', 'urlHost(google.com,yahoo.com)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('url_1', 'urlHost(google.com,example.com)');
        $this->assertTrue($this->validation->validation());
    }

    public function testEmptyRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '   ', // An empty string is an empty value.
            'key_2'     => 'abc'
        ]);

        $this->validation->rule('key_1', 'empty');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_1', 'required');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_2', 'empty');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_2', 'required');
        $this->assertTrue($this->validation->validation());
    }

    public function testMinRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '3.14',
            'key_2'     => 5,
            'key_3'     => 'a string value', // This is a 14-character alphanumeric string. So the number of characters is considered a value.
        ]);

        $this->validation->rule('key_1', 'min(3.14)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_1', 'min(2)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_1', 'min(5)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_2', 'min(5.1)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_3', 'min(14)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_3', 'min(15)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_3', 'min(10)');
        $this->assertTrue($this->validation->validation());
    }

    public function testMaxRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '3.14',
            'key_2'     => 5,
            'key_3'     => 'a string value', // This is a 14-character alphanumeric string. So the number of characters is considered a value.
        ]);

        $this->validation->rule('key_1', 'max(3.14)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_1', 'max(2)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_1', 'max(5)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_2', 'max(5.1)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_3', 'max(14)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_3', 'max(15)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_3', 'max(10)');
        $this->assertFalse($this->validation->validation());

    }

    public function testRangeRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '3.14',
            'key_2'     => 5,
            'key_3'     => 'a string value', // Because an alphanumeric is a string, it cannot be used with range().
        ]);

        $this->validation->rule('key_1', 'range(2...5)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_1', 'range(1...3)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_1', 'range(4...10)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_2', 'range(0...100)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_3', 'range(5...255)');
        $this->assertFalse($this->validation->validation());
    }

    public function testLengthRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '3.14', // a 4-character string
            'key_2'     => 5, // a 1-character string
            'key_3'     => 'a string value', // a 14-character string.
        ]);

        $this->validation->rule('key_1', 'length(2...5)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_1', 'length(1...3)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_1', 'length(5...10)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_2', 'length(1...100)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_3', 'length(5...255)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_3', 'length(...255)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_3', 'length(10...)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_3', 'length(15...)');
        $this->assertFalse($this->validation->validation());
    }

    public function testRegexRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '13',
            'key_2'     => 'admin',
            'key_3'     => 'example@example.com',
        ]);

        $this->validation->pattern('custom_int', '[0-9]+')
                        ->rule('key_1', 'regex(custom_int)');
        $this->assertTrue($this->validation->validation());

        $this->validation->pattern('alphanumeric', '[a-zA-Z0-9.,]+')
                        ->rule('key_2', 'regex(alphanumeric)');
        $this->assertTrue($this->validation->validation());

        $this->validation->pattern('numeric', '[\d]+')
            ->rule('key_2', 'regex(numeric)');
        $this->assertFalse($this->validation->validation());

        // "email" is a default regex.
        $this->validation->rule('key_3', 'regex(email)');
        $this->assertTrue($this->validation->validation());
    }

    public function testDateRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '2022/01/01',
            'key_2'     => '2020-03-03',
            'key_3'     => '10 September 2000',
            'key_4'     => 'not good', // false
            'key_5'     => 'next Thursday'
        ]);
        $this->validation->rule('key_1', 'date');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_2', 'date');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_3', 'date');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_4', 'date');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_5', 'date');
        $this->assertTrue($this->validation->validation());
    }

    public function testDateFormatRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '2022/01/01'
        ]);

        $this->validation->rule('key_1', 'dateFormat(Y/m/d)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_1', 'dateFormat(d/m/Y)');
        $this->assertFalse($this->validation->validation());
    }

    public function testIPsRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '111.111.11.11',
            'key_2'     => '2001:98:0:1:0:12:144.122.199.90',
            'key_3'     => '2001:98:A11:10:11:1:0:106',
        ]);

        $this->validation->rule('key_1', 'ip');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_2', 'ip');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_3', 'ip');
        $this->assertTrue($this->validation->validation());


        $this->validation->rule('key_1', 'ipv4');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_2', 'ipv4');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_3', 'ipv4');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_1', 'ipv6');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_2', 'ipv6');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_3', 'ipv6');
        $this->assertTrue($this->validation->validation());
    }

    public function testOnlyRuleValid()
    {
        $this->validation->setData([
            'key_1'     => 'Hamburger'
        ]);

        $this->validation->rule('key_1', 'only(food,tea,meat)');
        $this->assertFalse($this->validation->validation());

        // The only() method tries to match case-insensitively.
        $this->validation->rule('key_1', 'only(hamburger,pizza)');
        $this->assertTrue($this->validation->validation());

        // strictOnly() tries to match in a case sensitive way.
        $this->validation->rule('key_1', 'strictOnly(hamburger, pizza)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_1', 'strictOnly(Hamburger,Pizza)');
        $this->assertTrue($this->validation->validation());
    }

    public function testAgainRuleValid()
    {
        $this->validation->setData([
            'password'          => '123b456',
            'password_again'    => '123a45',
            'mail'              => 'example@example.com',
            'mail_retype'       => 'example@example.com'
        ]);

        $this->validation->rule('password', 'again(password_again)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('mail', 'again(mail_retype)');
        $this->assertTrue($this->validation->validation());
    }

    public function testEqualsRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '123',
            'key_2'     => 'example@example.com',
        ]);

        $this->validation->rule('key_1', 'equals(1234)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_2', 'equals(example@example.com)');
        $this->assertTrue($this->validation->validation());
    }

    public function testStartWithRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '12345',
            'key_2'     => 'lorem ipsum dolor sit amet'
        ]);

        $this->validation->rule('key_1', 'startWith(345)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_1', 'startWith(12)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_2', 'startWith(lorem ipsum)');
        $this->assertTrue($this->validation->validation());
    }

    public function testEndWithRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '12345',
            'key_2'     => 'lorem ipsum dolor sit amet'
        ]);

        $this->validation->rule('key_1', 'endWith(345)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_1', 'endWith(12)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_2', 'endWith(lorem ipsum)');
        $this->assertFalse($this->validation->validation());
    }

    public function testInRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '12345',
            'key_2'     => 'Lorem ipsum Dolor sit amet'
        ]);

        // Verifies that the desired value is within the specified value, case-insensitive.

        $this->validation->rule('key_1', 'in(345)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_1', 'in(12)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_1', 'in(34)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_1', 'in(43)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_2', 'in(dolor)');
        $this->assertTrue($this->validation->validation());
    }

    public function testNotInRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '12345',
            'key_2'     => 'Lorem ipsum Dolor sit amet'
        ]);

        // Verifies case-insensitively that the requested value is not within the specified value.

        $this->validation->rule('key_1', 'notIn(345)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_1', 'notIn(12)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_1', 'notIn(34)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_1', 'notIn(43)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_2', 'notIn(dolor)');
        $this->assertFalse($this->validation->validation());
    }

    public function testContainsRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '12345',
            'key_2'     => 'Lorem ipsum Dolor sit amet'
        ]);

        // Verifies case sensitive that the requested value is within the specified value.

        $this->validation->rule('key_1', 'contains(345)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_1', 'contains(12)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_1', 'contains(34)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_1', 'contains(43)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_2', 'contains(dolor)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_2', 'contains(Dolor)');
        $this->assertTrue($this->validation->validation());
    }

    public function testNotContainsRuleValid()
    {
        $this->validation->setData([
            'key_1'     => '12345',
            'key_2'     => 'Lorem ipsum Dolor sit amet'
        ]);

        // Verifies case sensitive that the requested value is not within the specified value.

        $this->validation->rule('key_1', 'notContains(345)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_1', 'notContains(12)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_1', 'notContains(34)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('key_1', 'notContains(43)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_2', 'notContains(dolor)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('key_2', 'notContains(Dolor)');
        $this->assertFalse($this->validation->validation());
    }

}
