<?php

// Function to override core en language system validation or define your own en language validation message
function get_custom_language_validation($override = []) {
    // Add any default validation messages here
    $default_validation_messages = [
        'example_error' => 'This is an example error message.',
    ];

    // Merge the default messages with any overrides that have been provided
    $validation_messages = array_merge($default_validation_messages, $override);

    return $validation_messages;
}

// Call the function with an empty array to use only the default messages
$validation_messages = get_custom_language_validation([]);

// Call the function with an associative array to override the default messages
$override_validation_messages = [
    'example_error' => 'This is an overridden error message.',
];
$override_validation_messages = get_custom_language_validation($override_validation_messages);

