<?php

namespace RatulSaqibKhan\FileParser\Actions;

use DateTime;
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
        $this->printMessage("Write source file name e.g. demo.csv : ");
        $this->askSrcFileName();
        $this->printMessage("Write delimeter for column seperator: ");
        $this->askDelimeter();
        $this->printMessage("Write no of columns: ");
        $this->askColumnsNo();
        $this->printMessage("Write destination file name: ");
        $this->askDestFileName();
        $fw = fopen($this->destFileName, "w");

        $tempString = "";
        $lineCounter = 0;
        $success = (new FileParserController)
            ->setSourceFilePath($this->srcFileName)
            ->fileGetContentsChunked(4096, function ($chunk, &$handle, $iteration) use (&$lineCounter, &$lineCounter, &$fw, &$tempString) {
                /*
                    * Do what you will with the {$chunk} here
                    * {$handle} is passed in case you want to seek
                    ** to different parts of the file
                    * {$iteration} is the section of the file that has been read so
                    * ($i * 4096) is your current offset within the file.
                */
                $this->fileContentModifier($chunk, $tempString, $lineCounter, $fw);
            });

        if (!$success) {
            trigger_error("Parsing::Unsuccessfull", E_USER_NOTICE);
            return false;
        } else {
            echo "Process finished successfully!";
        }

        return true;
    }

    private function fileContentModifier($chunk, &$tempString, &$lineCounter, &$fw)
    {
        $lines = explode("\n", $chunk);
        foreach ($lines as $line) {
            $pieces = explode($this->delimeter, $line);

            if (count($pieces) < $this->columns) {
                $tempString .= $line;
                $line = $tempString;
                $pieces = explode($this->delimeter, $line);
            }

            if (array_key_exists($this->columns - 1, $pieces) && str_contains($pieces[$this->columns - 1], '}')) {
                $tempString = "";
            } else {
                $tempString .= $line;
                continue;
            }

            $data = $pieces[0];
            $datetime = new DateTime($pieces[1]);
            $timestamp = $datetime->format(DateTime::ATOM);

            $events = json_decode($pieces[2], true);
            $events['data'] = $data;
            $events['timestamp'] = $timestamp;

            $txt = json_encode($events) . "\n";

            ++$lineCounter;

            fwrite($fw, $txt);

            echo "Line: $lineCounter done!\n";
        }
    }
}
