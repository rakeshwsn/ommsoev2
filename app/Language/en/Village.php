<?php
// An array to store text strings for the Village module
$text = [
    'heading_title'        	=> 'Village', // The title of the page
    'text_list'           	=> 'Village List', // The title of the list page
    'text_edit'            	=> 'Edit Village', // The title of the edit page
    'text_add'            	=> 'Add Village' // The title of the add page
];

// A function to return the text array
function getText() {
    global $text;
    return $text;
}
?>
