<?php

// Define the array directly, without the return statement
$text = [
    'heading_title'       => 'Event',
    'text_image'          => 'Select Image',
    'text_clear'           => 'Clear'
];

// Use the json_encode function to directly print the array as a JSON string
echo json_encode($text, JSON_PRETTY_PRINT);

