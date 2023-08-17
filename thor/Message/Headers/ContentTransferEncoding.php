<?php

namespace Thor\Message\Headers;

enum ContentTransferEncoding: string
{

    case BIT7 = '7bit';
    case BIT8 = '8bit';
    case BINARY = 'binary';
    case QUOTED = 'quoted-printable';
    case BASE64 = 'base64';

    public function encode(string $str): string
    {
        return match ($this) {
            self::BASE64 => imap_binary($str),
            self::QUOTED => imap_8bit($str),
            self::BIT7 => imap_utf8_to_mutf7($str),
            default      => $str
        };
    }

    public function decode(string $str): string
    {
        return match ($this) {
            self::BASE64 => imap_base64($str),
            self::QUOTED => imap_qprint($str),
            self::BIT7 => imap_mutf7_to_utf8($str),
            default      => $str
        };
    }

}
