<?php

/**
 * Dump
 *
 * A generic function to print or dump variables with better formatting.
 *
 * @param mixed $var
 * @param bool  $print_r
 * @return void
 */
function dump($var, $print_r = true)
{
    ob_end_clean();
    $tag = $print_r ? 'pre' : 'code';
    echo "<$tag>";
    if ($print_r) {
        print_r($var);
    } else {
        var_dump($var);
    }
    echo "</$tag>";
}

/**
 * Array to Object
 *
 * Converts an array to an object.
 *
 * @param array $array
 * @return object
 */
function array_to_object($array)
{
    $object = (object) [];
    foreach ($array as $key => $value) {
        $object->$key = $value;
    }

    return $object;
}

/**
 * Object to Array
 *
 * Converts an object to an array.
 *
 * @param object $object
 * @return array
 */
function object_to_array($object)
{
    return json_decode(json_encode($object), true);
}

/**
 * Is Ajax
 *
 * Returns true if the request is an AJAX request.
 *
 * @return bool
 */
function is_ajax()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
}

/**
 * Load Controller
 *
 * Loads a controller and calls its method.
 *
 * @param string $controller
 * @param string $method
 * @return mixed
 */
function load_controller($controller, $method = 'index')
{
    $class = "app\\controllers\\$controller";
    if (class_exists($class)) {
        $controller = new $class;

        return $controller->$method();
    }

    return null;
}

/**
 * Image Thumb
 *
 * Creates an image thumbnail and caches the image.
 *
 * @param string $source_image
 * @param int    $width
 * @param int    $height
 * @param bool   $crop
 * @param array  $props
 * @return string
 */
function image_thumb($source_image, $width = 0, $height = 0, $crop = false, $props = [])
{
    $ci = get_instance();
    $ci->load->library('image_cache');

    $props['source_image'] = ltrim($source_image, '/');
    $props['width'] = $width;
    $props['height'] = $height;
    $props['crop'] = $crop;

    $ci->image_cache->initialize($props);
    $image = $ci->image_cache->image_cache();
    $ci->image_cache->clear();

    return $image;
}

/**
 * Resize
 *
 * Resizes an image and saves it.
 *
 * @param string $filename
 * @param int    $width
 * @param int    $height
 * @return string
 */
function resize($filename, $width, $height)
{
    $ci = get_instance();
    $ci->load->library('image_lib');

    if (!file_exists(FCPATH . 'uploads/' . $filename) || realpath(FCPATH . 'uploads/' . $filename) !== realpath($filename)) {
        return;
    }

    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    $image_old = $filename;
    $image_new = 'image-cache' . '/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

    if (!file_exists(FCPATH . 'uploads/' . $image_new) || filemtime(FCPATH . 'uploads/' . $image_old) > filemtime(FCPATH . 'uploads/' . $image_new)) {
        $config['image_library'] = 'gd2';
        $config['source_image'] = FCPATH . 'uploads/' . $image_old;
        $config['new_image'] = FCPATH . 'uploads/' . $image_new;
        $config['maintain_ratio'] = true;
        $config['width'] = $width;
        $config['height'] = $height;

        $ci->image_lib->initialize($config);
        $ci->image_lib->resize();
        $ci->image_lib->clear();
    }

    return base_url('uploads/' . $image_new);
}

/**
 * BR 2 NL
 *
 * Converts HTML <br /> tags to new lines.
 *
 * @param string $text
 * @return string
 */
function br2nl($text)
{
    return preg_replace('/<br\\s*?\/??>/i', "\n", $text);
}

/**
 * Option Array Value
 *
 * Returns a single dimension array from an array of objects with the key and value defined.
 *
 * @param array  $object_array
 * @param string $key
 * @param string $value
 * @param array  $default
 * @return array
 */
function option_array_value($object_array, $key, $value, $default = [])
{
    $option_array = $default;

    foreach ($object_array as $object) {
        $option_array[$object->$key] = $object->$value;
    }

    return $option_array;
}

/**
 * Theme Partial
 *
 * Loads a view file from the theme folder.
 *
 * @param string $path
 * @param array  $data
 * @return string
 */
function theme_partial($path, $data = [])
{
    $ci = get_instance();
    $ci->load->view('themes/default/' . $path, $data);
