<?php

declare(strict_types=1);

namespace Ferb\Conf;

use Ferb\Conf\Util\ConfigPath;
use Ferb\Conf\Util\TypedConfigFactory;

class ConfigSection
{
    private ConfigRoot $root;
    private string $path;
    private string $key;

    public function __construct(ConfigRoot $root, string $path)
    {
        $this->root = $root;
        $this->path = $path;
        $this->key = ConfigPath::get_section_key($path);
    }

    public function value(string $key = null)
    {
        return $this->root->value(ConfigPath::combine([$this->path, $key]));
    }

    public function section(string $key): ?ConfigSection
    {
        return $this->root->section(ConfigPath::combine([$this->path, $key]));
    }

    public function children(): iterable
    {
        return $this->root->children($this->path);
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
        return $this->root->has_children($this->path);
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

    private function as_array()
    {
        $children = $this->children();
        $result = [];
        foreach ($this->children() as $child) {
            if ($child->has_children()) {
                $result[$child->key()] = $child->as_array();
            } else {
                $result[$child->key()] = $child->value();
            }
        }

        return $result;
    }
}
