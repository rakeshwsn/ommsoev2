<?php

namespace App\Libraries;

class Shortcode
{
    /**
     * @var array
     */
    protected $shortcodeTags = [];

    /**
     * Add hook for shortcode tag.
     *
     * @param string $tag
     * @param callable $func
     */
    public static function add(string $tag, callable $func): void
    {
        $tag = trim($tag);
        if (is_callable($func)) {
            self::$shortcodeTags[$tag] = $func;
        }
    }

    /**
     * Removes hook for shortcode.
     *
     * @param string $tag
     */
    public static function remove(string $tag): void
    {
        unset(self::$shortcodeTags[$tag]);
    }

    /**
     * Clear all shortcodes.
     */
    public static function removeAll(): void
    {
        self::$shortcodeTags = [];
    }

    /**
     * Search content for shortcodes and filter shortcodes through their hooks.
     *
     * @param string $content
     * @return string
     */
    public static function do(string $content): string
    {
        if (empty(self::$shortcodeTags) || !is_array(self::$shortcodeTags)) {
            return $content;
        }

        $pattern = self::getRegex();

        return preg_replace_callback($pattern, [new self, 'doTag'], $content);
    }

    /**
     * Retrieve the shortcode regular expression for searching.
     *
     * @return string
     */
    public static function getRegex(): string
    {
        $tagRegexp = '[' . implode('', array_map('preg_quote', array_keys(self::$shortcodeTags))) . ']';

        return '/(.?)\[(' . $tagRegexp . ')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s';
    }

    /**
     * Regular Expression callable for do() for calling shortcode hook.
     *
     * @param array $m
     * @return string
     */
    public static function doTag(array $m): string
    {
        // allow [[foo]] syntax for escaping a tag
        if ($m[1] == '[' && $m[6] == ']') {
            return substr($m[0], 1, -1);
        }

        $tag = $m[2];
        $attr = self::parseAttrs($m[3]);

        if (isset($m[5])) {
            // enclosing tag - extra parameter
            return $m[1] . call_user_func(self::$shortcodeTags[$tag], $attr, $m[5], $tag) . $m[6];
        } else {
            // self-closing tag
            return $m[1] . call_user_func(self::$shortcodeTags[$tag], $attr, null, $tag) . $m[6];
        }
    }

    /**
     * Retrieve all attributes from the shortcodes tag.
     *
     * @param string $text
     * @return array
     */
    public static function parseAttrs(string $text): array
    {
        $attrs = [];
        $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);

        if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {
                if (!empty($m[1])) {
                    $attrs[strtolower($m[1])] = stripcslashes($m[2]);
                } elseif (!empty($m[3])) {
                    $attrs[strtolower($m[3])] = stripcslashes($m[4]);
                } elseif (!empty($m[5])) {
                    $attrs[strtolower($m[5])] = stripcslashes($m[6]);
                } elseif (isset($m[7]) && strlen($m[7])) {
                    $attrs[] = stripcslashes($m[7]);
                } elseif (isset($m[8])) {
                    $attrs[] = stripcslashes($m[8]);
                }
            }
        } else {
            $attrs = ltrim($text);
        }

        return $attrs;
    }

    /**
     * Combine user attributes with known attributes and fill in defaults when needed.
     *
     * @param array $pairs
     * @param array $attrs
     * @return array
     */
    public static function attrs(array $pairs, ?array $attrs = []): array
    {
        $attrs = (array)$attrs;
        $out = [];

        foreach ($pairs as $name => $default) {
            if (array_key_exists($name, $attrs)) {
                $out[$name] = $attrs[$name];
            } else {
                $out[$name] = $default;
            }
        }

