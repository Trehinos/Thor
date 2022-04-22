<?php

namespace Thor\Web\Assets;

use Thor\Http\Uri;
use Thor\Stream\Stream;

class MergedAsset extends Asset
{

    public function __construct(
        AssetType $type,
        string $filename,
        Uri $uri,
        protected array $fileList = []
    ) {
        parent::__construct(
            $type,
            $filename,
            $filename,
            $uri,
            Stream::create(
                implode(
                    "\n",
                    array_map(
                        fn(string $filename) => file_get_contents($filename),
                        $this->fileList
                    )
                )
            )
        );
    }

}
