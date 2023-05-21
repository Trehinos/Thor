<?php

namespace Thor\Message\Part;

use Stringable;
use Thor\Message\Headers\Headers;

interface PartInterface extends Stringable
{
    /**
     * Returns this Part headers.
     *
     * @return Headers
     */
    function getHeaders(): Headers;

    /**
     * Returns the MIME type in the form of "type"/"subtype".
     *
     * @return string
     */
    function getMimeType(): string;

    /**
     * Gets the body of this part.
     *
     * @return string
     */
    function getBody(): string;

    /**
     * Returns $text encoded and chunked for emails.
     *
     * @param string $text
     *
     * @return string
     */
    function chunk(string $text): string;

    /**
     * Returns the part raw text.
     *
     * @return string
     */
    function __toString(): string;

}
