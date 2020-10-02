<?php
declare(strict_types=1);
namespace Ferb\Config\Util;

final class ConfigPath {
        /// <summary>
        /// The delimiter ":" used to separate individual keys in a path.
        /// </summary>

        public const KeyDelimiter =":";

        public static function combine(array $path_segments) : string
        {
            return implode(self::KeyDelimiter,  $path_segments ?? []);
        }
        public static function get_section_key(string $path) : string
        {
            $last_index = stripos($path,self::KeyDelimiter, -0);
            if($last_index === false) return $path;
            return substr($path, $last_index+1);
        }
        public static function get_parent_path($path): string{
            $last_index = stripos($path,self::KeyDelimiter);
            if($last_index === false) return null;
            return substr($path, 0, $last_index);
        }

        public static function compare_paths(string $x, string $y):int
        {
            $x_parts = explode(ConfigPath::KeyDelimiter, $x ) ??[];
            $y_parts = explode(ConfigPath::KeyDelimiter, $y)??[];

            for($i = 0; $i< min(count($x_parts), \count($y_parts)); $i++ )
            {
                $x = $x_parts[$i];
                $y = $y_parts[$i];
                $x_is_int = \is_numeric($x);
                $y_is_int = \is_numeric($y);
                
                $diff = 0;
                if($x_is_int && $y_is_int){
                    $diff = intval($x) - intval($y);
                }
                elseif (!$x_is_int && !$y_is_int) {
                    $diff = \strcasecmp($x, $y);
                }
                else{
                    $diff = $x_is_int? -1 : 1;
                }
                if($diff !== 0){
                    return $diff;
                }
                
                
            }
            return count($x_parts)- count($y_parts);
        }
        
}

