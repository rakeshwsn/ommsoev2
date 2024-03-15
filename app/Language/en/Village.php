<?php

namespace App\Modules\Village;

/**
 * Class Text
 *
 * Provides text strings for the Village module
 */
class Text
{
    /**
     * The title of the page
     * @var string
     */
    public const HEADING_TITLE = 'Village';

    /**
     * The title of the list page
     * @var string
     */
    public const TEXT_LIST = 'Village List';

    /**
     * The title of the edit page
     * @var string
     */
    public const TEXT_EDIT = 'Edit Village';

    /**
     * The title of the add page
     * @var string
     */
    public const TEXT_ADD = 'Add Village';

    /**
     * @return array<string, string>
     */
    public static function getText(): array
    {
        return [
            'heading_title' => self::HEADING_TITLE,
            'text_list' => self::TEXT_LIST,
            'text_edit' => self::TEXT_EDIT,
            'text_add' => self::TEXT_ADD,
        ];
    }
}
