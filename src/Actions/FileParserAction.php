<?php

namespace RatulSaqibKhan\FileParser\Actions;

use RatulSaqibKhan\FileParser\Abstractions\FileParserAbstraction;
use RatulSaqibKhan\FileParser\Controllers\FileParserController;

class FileParserAction extends FileParserAbstraction
{
    /**
     * Parse File
     *
     * @return bool
     *
     */
    public function parse(): bool
    {
        $this->printMessage("Write source file location: ");
        $this->askSrcFilePath();
        $this->printMessage("Write delimeter for column seperator: ");
        $this->askDelimeter();
        $this->printMessage("Write no of columns: ");
        $this->askColumnsNo();
        $this->printMessage("Write destination file location: ");
        $this->askDestFilePath();
        $fw = fopen($this->destFilePath, "w");

        $lineCounter = 0;
        $success = (new FileParserController)
            ->setSourceFilePath($this->srcFilePath)
            ->fileGetContentsChunked(4096, function ($chunk, &$handle, $iteration) use (&$lineCounter, &$fw) {
                /*
                    * Do what you will with the {$chunk} here
                    * {$handle} is passed in case you want to seek
                    ** to different parts of the file
                    * {$iteration} is the section of the file that has been read so
                    * ($i * 4096) is your current offset within the file.
                */
                $lines = explode("\n", $chunk);
                foreach ($lines as $line) {
                    ++$lineCounter;
                    $txt = $line."\n";

                    fwrite($fw, $txt);

                    echo "Line: $lineCounter done!\n";
                }
            });

        if (!$success) {
            trigger_error("Parsing::Unsuccessfull", E_USER_NOTICE);
            return false;
        }

        return true;
    }
}
