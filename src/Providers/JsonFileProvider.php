<?php
declare(strict_types=1);
namespace Ferb\Conf\Providers;


class JsonFileProvider extends ConfigProviderBase
{
    private $file;
    public function __construct($file){
        parent::__construct('','');
        $this->file = $file;
    }
    protected function get_values():array{
        $txt = \get_file_contents($this->file);
        $data = \json_decode($txt);
        return parent::flatten($data);

    }
}


