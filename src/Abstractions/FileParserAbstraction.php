<?php

namespace RatulSaqibKhan\FileParser\Abstractions;

use RatulSaqibKhan\FileParser\Interfaces\FileParserInterface;

abstract class FileParserAbstraction implements FileParserInterface
{
    /**
     * Source File Path
     *
     * @var string $srcFilePath
     */
    public string $srcFilePath;

    /**
     * Destination File Path
     *
     * @var string $destFilePath
     */
    public string $destFilePath;

    /**
     * Delimeter
     *
     * @var string $delimeter
     */
    public string $delimeter;

    /**
     * Columns
     *
     * @var int $columns
     */
    public int $columns;

     /**
     * Reurn Source Directory
     *
     * @return string
     *
     */
    public function getSrcDir(): string
    {
        return __DIR__."/../";
    }

     /**
     * Print Message
     *
     * @param string
     * @return void
     *
     */
    public function printMessage(string $msg): void
    {
        echo $msg;
    }

    /**
     * Get Input From User from Console
     */
    private function getInput()
    {
        return rtrim(fgets(STDIN));
    }

    /**
     * Ask Source File Path
     *
     * @return void
     *
     */
    public function askSrcFilePath(): void
    {
        $this->srcFilePath = $this->getSrcDir() . $this->getInput();
    }

    /**
     * Ask Source File Path
     *
     * @return void
     *
     */
    public function askDestFilePath(): void
    {
        $this->destFilePath = $this->getSrcDir() . $this->getInput();
    }

    /**
     * Ask Delimeter
     *
     * @return void
     *
     */
    public function askDelimeter(): void
    {
        $this->delimeter = $this->getInput();
    }

    /**
     * Ask No of Columns
     *
     * @return void
     *
     */
    public function askColumnsNo(): void
    {
        $this->columns = (int)$this->getInput();
    }

    /**
     * Parse File
     *
     * @return bool
     *
     */
    abstract public function parse(): bool;
}
