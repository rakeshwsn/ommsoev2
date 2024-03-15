<?php

// Define the array directly, without the return statement
$text = [
    'heading_title'       => 'Event',
    'text_image'          => 'Select Image',
    'text_clear'           => 'Clear'
];

// Use the export function to return the array as a JSON string
function export($array) {
    return json_encode($array, JSON_PRETTY_PRINT);
}

// Call the export function to print the text array
echo export($text);

