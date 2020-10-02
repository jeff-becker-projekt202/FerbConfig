<?php

declare(strict_types=1);

namespace Ferb\ConfTests;

use Ferb\Conf\Util\TypedConfigFactory;
use Ferb\Conf\ConfigBuilder;
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
    private function integration_data(){
        return  [
            'example'=> $this->data()
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

    public function canIntegrateWithConstructor(){
        $conf = (new ConfigBuilder())
            ->add_array($this->integration_data())
            ->create();
        $result = $conf->section('example')->as(OuterConstruct::class);
        $this->assertNotEmpty($result);
        $this->assertEquals(1, $result->a);
        $this->assertEquals(5, $result->c->e);
    }

    public function canIntegrateWithoutConstructor(){
        $conf = (new ConfigBuilder())
            ->add_array($this->integration_data())
            ->create();
        $result = $conf->section('example')->as(Outer::class);
        $this->assertNotEmpty($result);
        $this->assertEquals(1, $result->a);
        $this->assertEquals(5, $result->c->e);
    }
}


class OuterConstruct{
    public int $a;
    public string $b;
    public InnerConstruct $c;
    public function __construct($arr){
            $this->a = $arr['a'];
            $this->b = $arr['b'];
            $this->c = new InnerConstruct($arr['c']);

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