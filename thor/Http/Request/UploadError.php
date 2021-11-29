<?php

namespace Thor\Http\Request;

use Exception;

/**
 * This enumeration lists all possible file upload errors.
 *
 * @package          Thor/Http/Request
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
enum UploadError: int
{

    case NO_ERROR = 0;
    case INI_SIZE = 1;
    case FORM_SIZE = 2;
    case PARTIAL = 3;
    case NO_FILE = 4;
    case NO_TMP_DIR = 6;
    case CANT_WRITE = 7;
    case EXTENSION = 8;

    /**
     * @throws Exception
     */
    public function getErrorMessage(): ?string
    {
        return match ($this) {
            self::NO_ERROR => null,
            self::INI_SIZE => 'PHP INI : upload_max_filesize exceeded',
            self::FORM_SIZE => 'HTML form : MAX_FILE_SIZE exceeded',
            self::PARTIAL => 'Transport error, partially uploaded',
            self::NO_FILE => 'Transport error, file not uploaded',
            self::NO_TMP_DIR => 'Uploaded temporary folder missing',
            self::CANT_WRITE => 'Write error',
            self::EXTENSION => 'PHP extension error'
        };
    }

}
