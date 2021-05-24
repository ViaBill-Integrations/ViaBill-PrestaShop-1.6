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
 * Class Order
 *
 * @package ViaBill\Adapter
 */
class Order
{
    /**
     * Gets Order ID By Cart ID.
     *
     * @param int $idCart
     *
     * @return array
     */
    public function getIdByCartId($idCart)
    {
        return \Order::getOrderByCartId($idCart);
    }
}
