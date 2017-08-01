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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Store\Model\Store as BaseStore;
use Doctrine\Common\Collections\Collection;

class Store extends BaseStore implements StoreInterface
{
    /**
     * @var CountryInterface
     */
    private $baseCountry;
    
    /**
     * @var Collection|ConfigurationInterface[]
     */
    protected $configurations;

    /**
     * @var Collection|CountryInterface[]
     */
    protected $countries;

    /**
     * @var Collection|TaxRuleGroupInterface[]
     */
    protected $taxRuleGroups;

    /**
     * {@inheritdoc}
     */
    public function getConfigurations()
    {
        return $this->configurations;
    }

    /**
     * {@inheritdoc}
     */
    public function hasConfigurations()
    {
        return !$this->configurations->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ConfigurationInterface $configuration)
    {
        if (!$this->hasConfiguration($configuration)) {
            $this->configurations->add($configuration);
            $configuration->setStore($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeConfiguration(ConfigurationInterface $configuration)
    {
        if ($this->hasConfiguration($configuration)) {
            $this->configurations->removeElement($configuration);
            $configuration->setStore(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasConfiguration(ConfigurationInterface $configuration)
    {
        return $this->configurations->contains($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseCountry()
    {
        return $this->baseConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseCountry(CountryInterface $baseCountry)
    {
        $this->baseCountry = $baseCountry;
    }
}
