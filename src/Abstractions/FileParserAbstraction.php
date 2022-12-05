<?php

namespace RatulSaqibKhan\FileParser\Abstractions;

use RatulSaqibKhan\FileParser\Interfaces\FileParserInterface;

abstract class FileParserAbstraction implements FileParserInterface
{
    /**
     * Source File Path
     *
     * @var string $srcFileName
     */
    public string $srcFileName;

    /**
     * Destination File Path
     *
     * @var string $destFileName
     */
    public string $destFileName;

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
     * Splitted Files Count
     *
     * @var string $splittedFiles
     */
    public string $splittedFiles;

     /**
     * Reurn Source Directory
     *
     * @return string
     *
     */
    public function getSrcDir(): string
    {
        return __DIR__."/../../input_source/";
    }

     /**
     * Reurn Source Directory
     *
     * @return string
     *
     */
    public function getDestDir(): string
    {
        return __DIR__."/../../output_source/";
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
    public function askSrcFileName(): void
    {
        $this->srcFileName = $this->getSrcDir() . $this->getInput();
    }

    /**
     * Ask Source File Path
     *
     * @return void
     *
     */
    public function askDestFileName(): void
    {
        $this->destFileName = $this->getDestDir() . $this->getInput();
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
     * Ask No of Splitted Files
     *
     * @return void
     *
     */
    public function askSplittedFilesNo(): void
    {
        $this->splittedFiles = (int)$this->getInput();
    }

    /**
     * Parse File
     *
     * @return bool
     *
     */
    abstract public function parse(): bool;
}
