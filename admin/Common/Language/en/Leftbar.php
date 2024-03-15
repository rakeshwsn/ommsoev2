<?php

/**
 * Translation file for the application.
 *
 * This file contains an array of translations for the application. The keys
 * of the array correspond to the English text, and the values correspond to
 * the translated text.
 */

const TRANSLATIONS = [
    // ... (same translations as before)
];

/**
 * Gets the translations for the application.
 *
 * @return array The translations array.
 */
function getTranslations()
{
    return TRANSLATIONS;
}

/**
 * Gets a translated string for the given key.
 *
 * @param string $key The key for the translation.
 * @return string The translated string, or the key itself if no translation is found.
 */
function __($key)
{
    $translations = getTranslations();
    return $translations[$key] ?? $key;
}

// Example usage:
echo __('text_dashboard');
