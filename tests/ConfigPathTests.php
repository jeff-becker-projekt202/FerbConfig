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
    public function testFoo()
    {
        $config = (new ConfigBuilder())
            ->add_array(
                ['test' => 1,
                    'foo' => [
                        'bar' => 2,
                        'baz' => 3,
                    ],
                ]
            )->create();
        $value = $config->section('test')->value();
        $this->assertEquals(1, $value);
    }
}
