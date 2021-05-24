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

namespace ViaBill\Install;

/**
 * Class Tab
 *
 * @package ViaBill\Install
 */
class Tab
{
    /**
     * Filename Constant.
     */
    const FILENAME = 'Tab';

    /**
     * Defines Invisible Controller Name.
     *
     * @var string
     */
    private $controllerInvisibleName = 'AdminViaBillTabs';

    /**
     * Defines Settings Controller Name.
     *
     * @var string
     */
    private $controllerSettingsName = 'AdminViaBillSettings';

    /**
     * Defines Authentication Controller Name.
     *
     * @var string
     */
    private $controllerAuthenticationName = 'AdminViaBillAuthentication';

    /**
     * Defines Actions Controller Name.
     *
     * @var string
     */
    private $controllerActionsName = 'AdminViaBillActions';

    /**
     * Defines Contact Controller Name.
     *
     * @var string
     */
    private $controllerContactName = 'AdminViaBillContact';

    /**
     * Defines Troubleshoot Controller Name.
     *
     * @var string
     */
    private $controllerTroubleshootName = 'AdminViaBillTroubleshoot';

    /**
     * Module Main Class Variable Declaration.
     *
     * @var \ViaBill
     */
    private $module;

    /**
     * Tab constructor.
     *
     * @param \ViaBill $module
     */
    public function __construct(\ViaBill $module)
    {
        $this->module = $module;
    }

    /**
     * Gets Module Tabs.
     *
     * @return array
     */
    public function getTabs()
    {
        return array(
            array(
                'name' => $this->module->displayName,
                'class_name' => $this->controllerInvisibleName,
                'visible' => false,
                'parent' => -1,
            ),
            array(
                'name' => $this->module->l('Ajax', self::FILENAME),
                'parent' => $this->controllerInvisibleName,
                'class_name' => $this->controllerActionsName,
                'visible' => false,
                'module_tab' => true
            ),
            array(
                'name' => $this->module->l('Authentication', self::FILENAME),
                'parent' => $this->controllerInvisibleName,
                'class_name' => $this->controllerAuthenticationName,
                'visible' => true,
                'module_tab' => true
            ),
            array(
                'name' => $this->module->l('Settings', self::FILENAME),
                'parent' => $this->controllerInvisibleName,
                'class_name' => $this->controllerSettingsName,
                'module_tab' => true
            ),
            array(
                'name' => $this->module->l('Contact', self::FILENAME),
                'ParentClassName' => $this->controllerInvisibleName,
                'class_name' => $this->controllerContactName,
                'visible' => true,
                'module_tab' => true
            ),
            array(
                'name' => $this->module->l('Troubleshooting', self::FILENAME),
                'ParentClassName' => $this->controllerInvisibleName,
                'class_name' => $this->controllerTroubleshootName,
                'visible' => true,
                'module_tab' => true
            )
        );
    }

    /**
     * Gets Invisible Controller Name.
     *
     * @return string
     */
    public function getControllerInvisibleName()
    {
        return $this->controllerInvisibleName;
    }

    /**
     * Gets Settings Controller Name.
     *
     * @return string
     */
    public function getControllerSettingsName()
    {
        return $this->controllerSettingsName;
    }

    /**
     * Gets Authentication Controller Name.
     *
     * @return string
     */
    public function getControllerAuthenticationName()
    {
        return $this->controllerAuthenticationName;
    }

    /**
     * Gets Action Controller Name.
     *
     * @return string
     */
    public function getControllerActionsName()
    {
        return $this->controllerActionsName;
    }
}
