<?php

// Define the array of text values
$text = [
	'heading_title'                 => 'Pages',
	'text_image'                    => 'Select Image',
	'text_clear'                     => 'Clear'
];

// Return the array of text values
function getTextValues(): array
{
	global $text;
	return $text;
}

// Call the function to get the text values
getTextValues();

