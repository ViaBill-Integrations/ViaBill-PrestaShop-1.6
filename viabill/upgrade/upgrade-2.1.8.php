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

use ViaBill\Config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 *
 * @param Viabill $module
 * @return bool
 */
function upgrade_module_2_1_8($module)
{
    if (!\Configuration::updateValue(Config::ENABLE_AUTO_PAYMENT_CAPTURE, 0)) {
        return false;
    }
    if (!\Configuration::updateValue(Config::CAPTURE_ORDER_STATUS_MULTISELECT, '[]')) {
        return false;
    }

    return true;
}
