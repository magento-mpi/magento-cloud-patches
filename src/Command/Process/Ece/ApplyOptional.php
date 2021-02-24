<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CloudPatches\Command\Process\Ece;

use Magento\CloudPatches\Command\Process\Action\ActionPool;
use Magento\CloudPatches\Command\Process\ProcessInterface;
use Magento\CloudPatches\Environment\Config;
use Magento\CloudPatches\Patch\FilterFactory;
use Magento\CloudPatches\Composer\Config as ComposerConfig;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Applies optional patches (Cloud).
 */
class ApplyOptional implements ProcessInterface
{
    /**
     * @var FilterFactory
     */
    private $filterFactory;

    /**
     * @var ActionPool
     */
    private $actionPool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Config
     */
    private $envConfig;

    /**
     * @var ComposerConfig
     */
    private $composerConfig;

    /**
     * @param FilterFactory $filterFactory
     * @param ActionPool $actionPool
     * @param LoggerInterface $logger
     * @param Config $envConfig
     * @param ComposerConfig $composerConfig
     */
    public function __construct(
        FilterFactory $filterFactory,
        ActionPool $actionPool,
        LoggerInterface $logger,
        Config $envConfig,
        ComposerConfig $composerConfig
    ) {
        $this->filterFactory = $filterFactory;
        $this->actionPool = $actionPool;
        $this->logger = $logger;
        $this->envConfig = $envConfig;
        $this->composerConfig = $composerConfig;
    }

    /**
     * @inheritDoc
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $envQualityPatches = $this->envConfig->getQualityPatches();
        $composerQualityPatches = $this->composerConfig->getQualityPatches();
        $patches = array_unique(array_merge($envQualityPatches, $composerQualityPatches));

        $patchFilter = $this->filterFactory->createApplyFilter($patches);
        if ($patchFilter === null) {
            return;
        }

        $this->logger->notice('Start of applying optional patches');
        $this->logger->info('QUALITY_PATCHES env variable: ' . implode(' ', $envQualityPatches));
        $this->actionPool->execute($input, $output, $patchFilter);
        $this->logger->notice('End of applying optional patches');
    }
}
