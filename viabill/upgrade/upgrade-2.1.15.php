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
function upgrade_module_2_1_15($module)
{
    $db = Db::getInstance();

    # insert the tabs

    $query = 'SELECT `id_parent` FROM `'._DB_PREFIX_.'tab` WHERE class_name = "AdminViaBillSettings"';
    $parent_id = $db->getValue($query);
    if (empty($parent_id)) {
        $parent_id = 0;
    }

    $query = 'INSERT INTO `'._DB_PREFIX_.'tab`
        (`id_parent`, `class_name`, `module`, `position`, `active`, `hide_host_mode`) VALUES '.
        '('.$parent_id.', "AdminViaBillContact", "viabill", 4, 1, 0)';

    $db->execute($query);
    $id = $db->insert_id();
    $count = (int) $db->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'lang');
    while ($count > 0) {
        $db->execute(
            'INSERT INTO `'._DB_PREFIX_.'tab_lang`
                    (`id_tab`, `id_lang`, `name`)
                    VALUES ('.$id.', '. $count.', "Contact")'
        );
        $count--;
    }

    $query = 'INSERT INTO `'._DB_PREFIX_.'tab`
        (`id_parent`, `class_name`, `module`, `position`, `active`, `hide_host_mode`) VALUES '.
        '('.$parent_id.', "AdminViaBillTroubleshoot", "viabill", 5, 1, 0)';

    $db->execute($query);
    $id = $db->insert_id();
    $count = (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'lang');
    while ($count > 0) {
        $db->execute(
            'INSERT INTO `'._DB_PREFIX_.'tab_lang`
                    (`id_tab`, `id_lang`, `name`)
                    VALUES ('.$id.', '.  $count.', "Troubleshooting")'
        );
        $count--;
    }

    $query = 'INSERT INTO `'._DB_PREFIX_.'access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) '.
        'SELECT "1", `id_tab`,  "1", "1", "1", "1" FROM `'
        ._DB_PREFIX_.'tab` WHERE `class_name` IN ('.
        '"AdminViaBillContact",'.
        '"AdminViaBillTroubleshoot")';

    $db->execute($query);

    return true;
}
