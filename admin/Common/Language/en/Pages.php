<?php

// Define the array of text values as a constant
define('TEXT_VALUES', [
	'heading_title'                 => 'Pages',
	'text_image'                    => 'Select Image',
	'text_clear'                     => 'Clear'
]);

// Function to return the array of text values
function getTextValues(): array
{
	return TEXT_VALUES;
}

// Call the function to get the text values
$textValues = getTextValues();

// Use the text values in the code
echo $textValues['heading_title'];

