<?php

namespace App\Helpers;

class UserHelper
{
    /**
     * Genera un color hexadecimal aleatorio
     * 
     * @return string
     */
    public static function getRandomColor()
    {
        $colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEEAD',
            '#D4A5A5', '#9B786F', '#E9AFA3', '#F4E1D2', '#B2B2B2',
            '#6B4E71', '#53687E', '#F5DD90', '#FFA07A', '#20B2AA'
        ];
        
        return $colors[array_rand($colors)];
    }

    /**
     * Obtiene la primera letra del nombre de usuario
     * 
     * @param string $name
     * @return string
     */
    public static function getInitials($name)
    {
        $words = explode(' ', $name);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) >= 2) break;
        }
        
        return $initials;
    }
}
