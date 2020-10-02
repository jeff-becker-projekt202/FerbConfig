<?php

declare(strict_types=1);

namespace Ferb\ConfTests;

use Ferb\Conf\Util\TypedConfigFactory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class TypedConfigFactoryTests extends TestCase
{
    private function data(){
        return[
            'a'=>1,
            'b'=>'2',
            'c'=>[
                'd'=>4,
                'e'=>'5'
            ]
            ];
    }
    public function testCanCreateWithoutConstructor(){
        $result = TypedConfigFactory::instantiate(Outer::class, $this->data());
        $this->assertNotEmpty($result);
        $this->assertEquals(1, $result->a);
        $this->assertEquals(5, $result->c->e);
    }
    public function testCanCreateWithConstructor(){
        $result = TypedConfigFactory::instantiate(OuterConstruct::class, $this->data());
        $this->assertNotEmpty($result);
        $this->assertEquals(1, $result->a);
        $this->assertEquals(5, $result->c->e);
    }
}


class OuterConstruct{
    public int $a;
    public string $b;
    public Inner $c;
    public function __construct($arr){
            $this->a = $arr['a'];
            $this->b = $arr['b'];
            $this->c = new Inner($arr['c']);

    }
}
class InnerConstruct {
    public int $d;
    public string $e;
    public function __construct($arr){
        $this->d = $arr['d'];
        $this->e = $arr['e'];
    }
}


class Outer{
    public int $a;
    public string $b;
    public Inner $c;
}
class Inner {
    public int $d;
    public string $e;
}