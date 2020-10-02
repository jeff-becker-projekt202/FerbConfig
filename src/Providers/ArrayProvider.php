<?php
declare(strict_types=1);
namespace Ferb\Conf\Providers;


class ArrayProvider extends ConfigProviderBase
{
    private $underlying;
    public function __construct($data){
        parent::__construct('','');
        $this->underlying = $data ?? [];
    }
    protected function get_values():array{
        return parent::flatten($this->underlying);

    }
}