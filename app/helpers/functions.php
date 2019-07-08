<?php

/**
 * Deze file bevat alle applicatie functies
 */

# Returns part of haystack string starting from
# and including the nth occurrence of needle to the end of haystack.
function strstr_nth($haystack, $needle, $nth = 2, $beforeNeedle = false)
{
    $strlen = strlen($needle);
    $pos = 0;
    $nth = abs($nth);

    while ($nth--)
    {
        if (($pos = strpos($haystack, $needle, $pos)) === false) break;
        $pos += $strlen;
    }

    if ($pos == 0)
        return null;

    # Return the part of the string
    return $beforeNeedle ? substr($haystack, 0, $pos - $strlen) : substr($haystack, $pos);
}