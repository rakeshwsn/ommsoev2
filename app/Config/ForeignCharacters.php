<?php

namespace Config;

use CodeIgniter\Config\ForeignCharacters as BaseForeignCharacters;

class ForeignCharacters extends BaseForeignCharacters
{
    public function convert($text)
    {
        // Add your custom conversion logic here
        // For example, converting all 'a' characters to '@'
        return str_replace('a', '@', $text);
    }
}
