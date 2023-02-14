<?php

/**
 * Cette fonction permet de vérifier si la valeur est vide ou non.
 * 
 * Elle retourne "true" si elle est vide, "false" dans le cas contraire.
 *
 * @param string $value
 * @return boolean
 */

function is_blank(string $value): bool
{
    if (!is_string($value)) {
        return true;
    }
    if (mb_strlen($value, "UTF-8") == 0) {
        return true;
    }
    return false;
}
