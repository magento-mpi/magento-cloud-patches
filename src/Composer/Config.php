<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CloudPatches\Composer;

use Composer;
use Composer\Package\RootPackageInterface;
use InvalidArgumentException;

/**
 * Config data from root composer.json `extra` section.
 */
class Config
{
    /**
     * @var RootPackageInterface
     */
    private $rootPackage;

    /**
     * @param Composer\Composer $composer
     */
    public function __construct(
        Composer\Composer $composer
    ) {
        $this->rootPackage = $composer->getPackage();
    }

    /**
     * Returns array of patch ids.
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function getQualityPatches(): array
    {
        $extra = $this->rootPackage->getExtra();
        if (empty($extra['magento-quality-patches']['patches'])) {
            return [];
        }

        $patches = $extra['magento-quality-patches']['patches'];
        if (!is_array($patches)) {
            throw new InvalidArgumentException(
                'Composer configuration value `extra` -> `magento-quality-patches` -> `patches` ' .
                'must be of the type array, ' . gettype($patches) . ' given'
            );
        }

        return $patches;
    }
}
