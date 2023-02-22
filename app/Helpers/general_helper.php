<?php

function show_404(){
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
}

function in_lakh($amt,$separator=','){
    $amt = (float)$amt;
    return number_format(round($amt/100000,2),2,'.',$separator);
}