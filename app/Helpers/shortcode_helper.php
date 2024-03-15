<?php

/**
 * Codeigniter Shortcode helper
 *
 * Author: Niranjan
 * Site URL: https://www.niranjan.com
 * License: GNU v3.
 */

class Shortcode {
    private $tags = [];

    public function add($tag, $func) {
        $this->tags[$tag] = $func;
    }

    public function remove($tag) {
        unset($this->tags[$tag]);
    }

    public function remove_all() {
        $this->tags = [];
    }

    public function do($content) {
        ksort($this->tags);
        foreach ($this->tags as $tag => $func) {
            $pattern = '/\[' . $tag . '\b(.*?)\](?:(.*?)\[\/' . $tag . '\])?/is';
            $content = preg_replace_callback($pattern, [$this, 'do_tag'], $content);
        }
        return $content;
    }

    private function do_tag($m) {
        if (!isset($this->tags[$m[1]])) {
            return '';
        }
        $attrs = $this->parse_attrs($m[2]);
        return call_user_func($this->tags[$m[1]], $attrs, $m[3]);
    }

    private function parse_attrs($text) {
        $attrs = [];
        if (empty($text)) {
            return $attrs;
        }
        $pairs = explode(' ', $text);
        foreach ($pairs as $pair) {
            $pair = trim($pair);
            if (strpos($pair, '=') === false) {
                $attrs[$pair] = true;
                continue;
            }
            list($key, $value) = explode('=', $pair, 2);
            $value = trim($value, "'\"");
            $attrs[trim($key)] = $value;
        }
        return $attrs;
    }

    public function strip($content) {
        return preg_replace('/\[.*?\]/', '', $content);
    }

    public function extract($tag, $content) {
        preg_match_all('/\[' . $tag . '\b(.*?)\](?:(.*?)\[\/' . $tag . '\])?/is', $content, $matches);
        $result = [];
        foreach ($matches[1] as $i => $attr_text) {
            $attrs = $this->parse_attrs($matches[2][$i]);
            $result[] = ['attr' => $attrs, 'content' => $matches[3][$i]];
        }
        return $result;
    }
}

function add_shortcode($tag, $func) {
    static $shortcode;
    if (!isset($shortcode)) {
        $shortcode = new Shortcode();
    }
    $shortcode->add($tag, $func);
}

function do_shortcode($content) {
    static $shortcode;
    if (!isset($shortcode)) {
        $shortcode = new Shortcode();
    }
    return $shortcode->do($content);
}

function remove_shortcode($tag) {
    static $shortcode;
    if (!isset($shortcode)) {
        $shortcode = new Shortcode();
    }
    $shortcode->remove($tag);
}

function remove_all_shortcodes() {
    static $shortcode;
    if (!isset($shortcode)) {
        $shortcode = new Shortcode();
    }
    $shortcode->remove_all();
}

function strip_shortcodes($content) {
    static $shortcode;
    if (!isset($shortcode)) {
        $shortcode = new Shortcode();
    }
    return $shortcode->strip($content);
}

function extract_shortcodes($tag, $content) {
    static $shortcode;
    if (!isset($shortcode)) {
        $shortcode = new Shortcode();
    }
    return $shortcode->extract($tag, $content);
}
