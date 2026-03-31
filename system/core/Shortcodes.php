<?php

class Shortcodes {
    private static $shortcodes = [];
    
    public static function add(string $name, callable $callback): void {
        self::$shortcodes[strtolower($name)] = $callback;
    }
    
    public static function remove(string $name): void {
        unset(self::$shortcodes[strtolower($name)]);
    }
    
    public static function process(string $content): string {
        
        $content = preg_replace_callback(
            '/\{([a-z0-9_-]+)\}(.*?)\{\/\1\}/is',
            function($matches) {
                $name = strtolower($matches[1]);
                $innerContent = $matches[2];
                
                if (isset(self::$shortcodes[$name])) {
                    $result = call_user_func(self::$shortcodes[$name], [], $innerContent);
                    return $result !== null ? $result : $matches[0];
                }
                
                return $matches[0];
            },
            $content
        );
        
        $content = preg_replace_callback(
            '/\{([a-z0-9_-]+)(?:\s+([^}]+))?\}/i',
            function($matches) {
                $name = strtolower($matches[1]);
                $attrs = [];
                
                if (isset($matches[2])) {
                    preg_match_all('/(\w+)\s*=\s*["\']([^"\']+)["\']/', $matches[2], $attrMatches, PREG_SET_ORDER);
                    foreach ($attrMatches as $attr) {
                        $attrs[$attr[1]] = $attr[2];
                    }
                }
                
                if (isset(self::$shortcodes[$name])) {
                    $result = call_user_func(self::$shortcodes[$name], $attrs);
                    return $result !== null ? $result : $matches[0];
                }
                
                return $matches[0];
            },
            $content
        );
        
        return $content;
    }
    
    public static function getAll(): array {
        return self::$shortcodes;
    }
}