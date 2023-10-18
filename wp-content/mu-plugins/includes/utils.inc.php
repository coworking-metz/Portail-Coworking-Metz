<?php

/**
 * retourne une valeur de data si l'index `$keys` existe
 *
 * @param  mixed $data La donnée à analyser
 * @param  mixed $keys Liste des champs à rechercher dans $data. Le premier trouvé sera retourné
 * @param  mixed $default Valeur par défaut si rien n'a été trouvé dans $data
 * @return mixed La valeur qui a été trouvée
 * @package Utils
 * @subpackage Utilitaires
 */
function si($data, $keys, $default = null)
{
    if (is_object($data)) {
        $data = get_object_vars($data);
    }
    if (is_array($data) || $data instanceof stdClass) {
        foreach (tableau($keys) as $key) {
            if (isset($data[$key])) {
                return $data[$key];
            }
        }
    }
    return $default;
}

/**
 * tableau - Fait en sorte que la donnée d'entrée soit transformée en tableau si elle ne l'est pas déjà
 *
 * @example 
 * tableau(1) retourne [1]
 * tableau([1]) retourne [1]
 * @param  mixed $data La donnée envoyée
 * @return array
 * @package Utils
 * @subpackage Utilitaires
 */
function tableau($data)
{
    if (is_object($data)) {
        $data = get_object_vars($data);
    }
    if (!is_array($data)) {
        $data = [$data];
    }

    return array_filter($data);
}
