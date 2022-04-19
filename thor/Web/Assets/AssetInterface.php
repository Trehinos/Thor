<?php

namespace Thor\Web\Assets;

interface AssetInterface
{

    /**
     * Gets the file content
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Gets the file name
     *
     * @return string
     */
    public function getFilename(): string;

    public function getType(): AssetType;

}
