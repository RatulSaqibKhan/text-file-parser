<?php

namespace RatulSaqibKhan\FileParser\Actions;

use DateTime;
use RatulSaqibKhan\FileParser\Abstractions\FileParserAbstraction;
use RatulSaqibKhan\FileParser\Controllers\FileParserController;

class FileSplitterAction extends FileParserAbstraction
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
        $this->printMessage("Write no of splitted files: ");
        $this->askSplittedFilesNo();
        $this->printMessage("Write destination file name: ");
        $this->askDestFileName();

        $tempString = "";
        $lineCounter = 0;
        $tmplineCounter = 0;
        $segmentNo = 1;
        
        (new FileParserController)
            ->setSourceFilePath($this->srcFileName)
            ->fileGetContentsChunked(4096, function ($chunk, &$handle, $iteration) use (&$lineCounter, &$tempString) {
                /*
                    * Do what you will with the {$chunk} here
                    * {$handle} is passed in case you want to seek
                    ** to different parts of the file
                    * {$iteration} is the section of the file that has been read so
                    * ($i * 4096) is your current offset within the file.
                */
                $this->lineCounter($chunk, $tempString, $lineCounter);
            });
            
        $perSegmentLines = (int)($lineCounter / $this->splittedFiles);
        $fw = $this->getCurrentSegmentFile($segmentNo);

        $success = (new FileParserController)
            ->setSourceFilePath($this->srcFileName)
            ->fileGetContentsChunked(4096, function ($chunk, &$handle, $iteration) use ($lineCounter, $perSegmentLines, &$tmplineCounter, &$tempString, &$segmentNo, &$fw) {
                /*
                    * Do what you will with the {$chunk} here
                    * {$handle} is passed in case you want to seek
                    ** to different parts of the file
                    * {$iteration} is the section of the file that has been read so
                    * ($i * 4096) is your current offset within the file.
                */
                $this->fileModifier($chunk, $lineCounter, $perSegmentLines, $tempString, $tmplineCounter, $segmentNo, $fw);
            });

        if (!$success) {
            trigger_error("Parsing::Unsuccessfull", E_USER_NOTICE);
            return false;
        } else {
            echo "Process finished successfully!";
        }

        return true;
    }

    private function getCurrentSegmentFile($segmentNo)
    {
        $fileNameSplit = \explode('.', $this->destFileName);
        $fileExtension = $fileNameSplit[count($fileNameSplit) - 1];

        return fopen($this->getDestDir().$fileNameSplit[0].'_'.$segmentNo.'.'.$fileExtension, "w");
    }

    private function lineCounter($chunk, &$tempString, &$lineCounter)
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
            ++$lineCounter;
        }
    }

    private function fileModifier($chunk, $lineCounter, $perSegmentLines, &$tempString, &$tmplineCounter, &$segmentNo, &$fw)
    {
        $lines = explode("\n", $chunk);
        foreach ($lines as $line) {
            if ($tmplineCounter > 0 && $tmplineCounter % $perSegmentLines == 0 && $lineCounter - $tmplineCounter > $perSegmentLines) {
                ++$segmentNo;
                $fw = $this->getCurrentSegmentFile($segmentNo);
            }
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

            ++$tmplineCounter;

            fwrite($fw, $txt);

            echo "Line: $tmplineCounter done!\n";
        }
    }
}
