<?php

// Define the array of text values
$text = [
    'heading_title'        => 'Users',
    'text_image'           => 'Select Image',
    'text_clear'            => 'Clear'
];

// Return the array of text values
function getTextValues() {
    global $text;
    return $text;
}

// Call the function to get the text values
getTextValues();
