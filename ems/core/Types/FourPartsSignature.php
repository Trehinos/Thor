<?php

namespace Ems\Types;

use Thor\Tools\Guid;

class FourPartsSignature implements SignatureInterface
{

    public const QUADRANT_LENGTH = 256;

    public function __construct(private ?string $hash = null)
    {
        $this->hash ??= implode('-', [self::quadrant(), self::quadrant(), self::quadrant(), self::quadrant()]);
    }

    public static function quadrant(): string
    {
        return Guid::base64(self::QUADRANT_LENGTH);
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
