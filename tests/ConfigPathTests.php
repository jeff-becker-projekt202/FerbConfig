<?php

declare(strict_types=1);

namespace Ferb\ConfTests;

use Ferb\Conf\ConfigBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ConfigPathTests extends TestCase
{
    public function testBuilderArray()
    {
        $config = (new ConfigBuilder())
            ->add_array($this->data())->create();
        $value = $config->section('test')->value();
        $this->assertEquals(1, $value);
    }

    public function testRoundTrip()
    {
        $config = (new ConfigBuilder())
            ->add_array($this->data())->create();
        $value = $config->as('array');
        $this->assertEquals($this->data(), $value);
    }

    private function data()
    {
        return
        ['test' => 1,
            'foo' => [
                'bar' => 2,
                'baz' => 3,
            ],
        ];
    }
}
