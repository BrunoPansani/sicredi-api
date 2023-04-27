<?php

if (!function_exists('filter_only_numbers')) {
    function filter_only_numbers($value)
    {
        return preg_replace('/[^0-9]/', '', $value);
    }
}

if (!function_exists('valid_brazilian_state')) {
    function valid_brazilian_state($value)
    {
        $states = [
            'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO',
            'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI',
            'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
        ];

        return in_array($value, $states);
    }
}
