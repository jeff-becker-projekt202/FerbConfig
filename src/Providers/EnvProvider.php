<?php

declare(strict_types=1);

namespace Ferb\Conf\Providers;

class EnvProvider extends ConfigProviderBase
{
    protected function get_values(): array
    {
        return $_ENV;
    }
}
