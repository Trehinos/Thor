<?php

namespace Thor\Web\Assets;

use Thor\Stream\Stream;
use Thor\Stream\StreamInterface;

class MergedAsset extends Asset
{

    public function __construct(
        AssetType $type,
        string $filename,
        protected array $fileList = []
    ) {
        parent::__construct(
            $type,
            $filename,
            $filename,
            Stream::create(
                implode(
                    '',
                    array_map(
                        fn(string $filename) => file_get_contents($filename),
                        $this->fileList
                    )
                )
            )
        );
    }

}
