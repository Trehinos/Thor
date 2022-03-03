<?php

namespace Thor\Web\Assets;

use Twig\TwigFunction;
use Thor\Configuration\Configuration;

final class AssetsManager
{

    /**
     * @param AssetsList[]  $lists
     * @param Configuration $configuration
     */
    public function __construct(private array $lists, private Configuration $configuration)
    {
    }

    public function twigFunction(): TwigFunction
    {
        return new TwigFunction(
            'asset',
            function (string $identifier): string {
                /** @var AssetsList $list */
                foreach ($this->lists as $listName => $list) {
                    foreach ($list as $asset) {
                        return $this[$identifier]?->getHtml() ?? '';
                    }
                }
                return '';
            }
        );
    }

}
