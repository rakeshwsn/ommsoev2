<?php

// Define the array of text values
$text = [
    'heading_title'        => 'Banner',
    'text_image'           => 'Select Image',
    'text_clear'            => 'Clear'
];

// Return the array of text values
function getText() {
    global $text;
    return $text;
}

// Call the function to get the text values
$textValues = getText();

// Print the text values
echo $textValues['heading_title'];
echo $textValues['text_image'];
echo $textValues['text_clear'];

