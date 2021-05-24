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

namespace ViaBill\Adapter;

/**
 * Class Link
 *
 * @package ViaBill\Adapter
 */
class Link
{
    /**
     * Link Variable Declaration.
     *
     * @var Link
     */
    private $link;

    /**
     * Link constructor.
     *
     * @param \Link $link
     */
    public function __construct(\Link $link)
    {
        $this->link = $link;
    }

    /**
     * Gets Admin Link And Adds Parameters.
     *
     * @param string $controller
     * @param array $params
     * @param bool $withToken
     *
     * @return string
     */
    public function getAdminLink($controller, $params = array(), $withToken = true)
    {
        $stringParams = '';

        foreach ($params as $key => $param) {
            $stringParams .= '&'.$key.'='.$param;
        }

        return $this->link->getAdminLink($controller, $withToken).$stringParams;
    }
}
