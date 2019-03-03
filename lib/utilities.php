<?php
/**
 * This function converts a variable name in the format foo_bar into the corresponding function
 * name FooBar.
 *
 * @param  string  $str  The variable name to convert
 * @return string
 */
function var2func($str)
{
    return preg_replace_callback("/_([a-z])/", "chrToUpper", ucfirst(strtolower($str)));
}

function chrToUpper($chr)
{
    return strtoupper($chr[1]);
}

/**
 * This function converts a function name in the format FooBar into the corresponding variable
 * name foo_bar.
 *
 * @param  string  $str  The function name to convert
 * @return string
 */
function func2var($str)
{
    $str[0] = strtolower($str[0]);
    return strtolower(preg_replace("/([A-Z])/", "_$1", $str));
}

/**
 *
 */
function var2label($var)
{
    return trim(preg_replace("/([A-Z])/", " $1", var2func($var)));
}

// recursively remove a directory
function rrmdir($dir)
{
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
}

function getUKPostcodeRegex($caseSensitive = false) {
	return '/[A-Z]{1,2}[0-9R][0-9A-Z]? ?[0-9][ABD-HJLNP-UW-Z]{2}/'.
			($caseSensitive?'':'i');
}

/**
 * Remove all characters that do not fit regex antipattern.
 *
 * @param string $string
 * @param string $antiPattern
 */
function stripInvalidStringChars($string, $antiPattern) {
	return preg_replace('/ {2,}/',' ',preg_replace($antiPattern, '', $string));
}
