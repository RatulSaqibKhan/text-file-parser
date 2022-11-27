<?php

namespace RatulSaqibKhan\FileParser\Interfaces;

interface FileParserInterface
{
    /**
     * Parse File
     *
     * @return bool
     *
     */
    public function parse(): bool;
}
