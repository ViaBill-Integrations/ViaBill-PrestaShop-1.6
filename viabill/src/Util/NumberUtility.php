<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Written for or by ViaBill
 * @copyright Copyright (c) Viabill
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 *
 */

namespace ViaBill\Util;

class NumberUtility
{
    public static function replaceCommaToDot($possibleFloat)
    {
        $possibleFloat = str_replace(",", ".", $possibleFloat);
        $possibleFloat = preg_replace('/\.(?=.*\.)/', '', $possibleFloat);

        return (float) $possibleFloat;
    }
}
