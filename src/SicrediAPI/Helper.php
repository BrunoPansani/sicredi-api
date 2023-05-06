<?php

namespace SicrediAPI;

class Helper
{
    public const BRAZILIAN_STATES = [
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        'AP' => 'Amapá',
        'AM' => 'Amazonas',
        'BA' => 'Bahia',
        'CE' => 'Ceará',
        'DF' => 'Distrito Federal',
        'ES' => 'Espírito Santo',
        'GO' => 'Goiás',
        'MA' => 'Maranhão',
        'MT' => 'Mato Grosso',
        'MS' => 'Mato Grosso do Sul',
        'MG' => 'Minas Gerais',
        'PA' => 'Pará',
        'PB' => 'Paraíba',
        'PR' => 'Paraná',
        'PE' => 'Pernambuco',
        'PI' => 'Piauí',
        'RJ' => 'Rio de Janeiro',
        'RN' => 'Rio Grande do Norte',
        'RS' => 'Rio Grande do Sul',
        'RO' => 'Rondônia',
        'RR' => 'Roraima',
        'SC' => 'Santa Catarina',
        'SP' => 'São Paulo',
        'SE' => 'Sergipe',
        'TO' => 'Tocantins'
    ];

    public static function filter_only_numbers($value)
    {
        return preg_replace('/[^0-9]/', '', $value);
    }

    public static function valid_brazilian_state($value)
    {
        $states = array_keys(self::BRAZILIAN_STATES);

        return in_array($value, $states);
    }

    /**
     * Try to abbreviate a brazilian state name
     * @param string $search
     * @return mixed
     */
    public static function abbreviate_state_name($search)
    {
        $found = array_search(strtolower($search), array_map('strtolower', self::BRAZILIAN_STATES));

        return $found ? $found : false;
    }
}
