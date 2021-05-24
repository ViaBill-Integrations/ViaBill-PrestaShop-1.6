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

namespace ViaBill\Service;

use ViaBill\Adapter\Context;
use ViaBill\Adapter\Link;
use ViaBill\Builder\Message\OrderMessageBuilder;
use Order;
use Tools;

/**
 * Class UserService
 *
 * @package ViaBill\Service
 */
class MessageService
{
    /**
     * Filename Constant.
     */
    const FILENAME = 'MessageService';

    /**
     * Order Message Builder Variable Declaration.
     *
     * @var OrderMessageBuilder
     */
    private $orderMessageBuilder;

    /**
     * Context Variable Declaration.
     *
     * @var Context
     */
    private $context;

    /**
     * Link Variable Declaration.
     *
     * @var Context
     */
    private $link;

    /**
     * MessageService constructor.
     *
     * @param OrderMessageBuilder $orderMessageBuilder
     * @param Context $context
     * @param Link $link
     */
    public function __construct(OrderMessageBuilder $orderMessageBuilder, Context $context, Link $link)
    {
        $this->orderMessageBuilder = $orderMessageBuilder;
        $this->context = $context;
        $this->link = $link;
    }

    /**
     * Redirects To Admin Orders Controller With Messages.
     *
     * @param Order $order
     * @param array $confirmations
     * @param array $errors
     * @param array $warnings
     *
     * @throws \PrestaShopException
     */
    public function redirectWithMessages(Order $order, array $confirmations, array $errors, array $warnings)
    {
        $messageBuilder = $this->orderMessageBuilder;
        $messageBuilder->setContext($this->context->getContext());
        $messageBuilder->setErrorMessage($errors);
        $messageBuilder->setSuccessMessage($confirmations);
        $messageBuilder->setWarningMessage($warnings);

        Tools::redirectAdmin(
            $this->link->getAdminLink(
                'AdminOrders',
                array(
                    'id_order' => $order->id
                )
            ).'&vieworder'
        );
    }

    /**
     * Sets Controller Messages.
     *
     * @param array $confirmations
     * @param array $errors
     * @param array $warnings
     */
    public function setMessages(array $confirmations, array $errors, array $warnings)
    {
        $messageBuilder = $this->orderMessageBuilder;
        $messageBuilder->setContext($this->context->getContext());
        $messageBuilder->setErrorMessage($errors);
        $messageBuilder->setSuccessMessage($confirmations);
        $messageBuilder->setWarningMessage($warnings);
    }
}
