<?php

declare(strict_types=1);

namespace Ferb\Conf;

use Ferb\Conf\Util\ConfigPath;
use Ferb\Conf\Util\TypedConfigFactory;
use Ferb\Iterators\FluentIterator;

class ConfigSection
{
    private ConfigRoot $root;
    private string $path;
    private string $key;

    private $children;

    public function __construct(ConfigRoot $root, string $path)
    {
        $this->root = $root;
        $this->path = $path;
        $this->key = ConfigPath::get_section_key($path);
    }

    public function value(string $key = null)
    {
        return $this->root->value(ConfigPath::combine([$this->path, $key ?? $this->key]));
    }

    public function section(string $key ): ?ConfigSection
    {
        return $this->root->section(ConfigPath::combine([$this->path, $key]));
    }

    public function children(): iterable
    {
        if (!isset($this->children)) {
            $this->children = $this->root->children($this->path)->to_array();
        }

        return FluentIterator::from($this->children);
    }

    public function key(): string
    {
        return $this->key;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function has_children(): bool
    {
        return $this->children()->some(function ($x) {return true; });
    }

    public function as($type)
    {
        if ('array' === $type) {
            return $this->as_array();
        }
        if ('object' === $type) {
            return (object) ($this->as_array());
        }
        if ('string' === $type) {
            return $this->value();
        }
        if ('int' === $type) {
            return intval($this->value());
        }
        if ('float' === $type) {
            return floatval($this->value());
        }
        if ('bool' === $type) {
            $v = \strtolower($this->value());

            return 'true' === $v || 'yes' === $v || '1' === $v || 'y' === $v;
        }
        if ('callable' === $type) {
            return $this->value();
        }
        if (\class_exists($type)) {
            return TypedConfigFactory::instantiate($type, $this);
        }

        return null;
    }

    private static function make_array($children, $root, $path){
        $result = [];
        foreach($children as $child){
            $child_path = ConfigPath::combine([$path, $child]);
            if($root->has_children($child_path)){
                $result[$child] = self::make_array($root->children($child_path), $root, $child_path);
            }
            else{
                $result[$child] = $root->value($child_path);
            }
        }
        return $result;
    }
    private function as_array()
    {
        return   self::make_array($this->children(), $this->root, $this->path);
    }
}
