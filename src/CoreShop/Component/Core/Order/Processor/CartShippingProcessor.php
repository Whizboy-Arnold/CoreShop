<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CartInterface as CoreCartInterface;
use CoreShop\Component\Core\Shipping\Calculator\TaxedShippingCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Shipping\Exception\UnresolvedDefaultCarrierException;
use CoreShop\Component\Shipping\Resolver\DefaultCarrierResolverInterface;

final class CartShippingProcessor implements CartProcessorInterface
{
    /**
     * @var TaxedShippingCalculatorInterface
     */
    private $carrierPriceCalculator;

    /**
     * @var DefaultCarrierResolverInterface
     */
    private $defaultCarrierResolver;

    /**
     * @param TaxedShippingCalculatorInterface $carrierPriceCalculator
     * @param DefaultCarrierResolverInterface $defaultCarrierResolver
     */
    public function __construct(
        TaxedShippingCalculatorInterface $carrierPriceCalculator,
        DefaultCarrierResolverInterface $defaultCarrierResolver
    )
    {
        $this->carrierPriceCalculator = $carrierPriceCalculator;
        $this->defaultCarrierResolver = $defaultCarrierResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        if (!$cart instanceof \CoreShop\Component\Core\Model\CartInterface) {
            return;
        }

        if (null === $cart->getShippingAddress()) {
            return;
        }

        if (null === $cart->getCarrier()) {
            $this->resolveDefaultCarrier($cart, $cart->getShippingAddress());

            if (null === $cart->getCarrier()) {
                return;
            }
        }

        $priceWithTax = $this->carrierPriceCalculator->getPrice($cart->getCarrier(), $cart, $cart->getShippingAddress(), true);
        $priceWithoutTax = $this->carrierPriceCalculator->getPrice($cart->getCarrier(), $cart, $cart->getShippingAddress(), false);

        $cart->setShipping($priceWithTax, true);
        $cart->setShipping($priceWithoutTax, false);
    }

    /**
     * @param CartInterface $cart
     * @param AddressInterface $address
     */
    private function resolveDefaultCarrier(CartInterface $cart, AddressInterface $address)
    {
        if (!$cart instanceof CoreCartInterface) {
            return;
        }

        try {
            $cart->setCarrier($this->defaultCarrierResolver->getDefaultCarrier($cart, $address));
        } catch (UnresolvedDefaultCarrierException $ex) {

        }
    }
}