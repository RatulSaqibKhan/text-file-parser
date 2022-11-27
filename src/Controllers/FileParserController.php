<?php

namespace RatulSaqibKhan\FileParser\Controllers;

use Exception;

class FileParserController
{
    /**
     * Source File Path
     *
     * @var string $srcFilePath
     */
    private string $srcFilePath;


    /**
     * Set Source File Path
     *
     * @param string $srcFilePath
     * @return FileParserController
     *
     */
    public function setSourceFilePath(string $srcFilePath): FileParserController
    {
        $this->srcFilePath = $srcFilePath;
        return $this;
    }

    /**
     * Get Source File Path
     *
     * @return string $srcFilePath
     *
     */
    public function getSourceFilePath(): string
    {
        return $this->srcFilePath;
    }

    /**
     * Get File Contents with a given Chunk sizeof
     *
     * @param int $chunkSize
     * @param mixed $callback
     *
     * @return bool
     *
     */
    public function fileGetContentsChunked(int $chunkSize, mixed $callback): bool
    {
        try {
            $file = $this->getSourceFilePath();

            $handle = fopen($file, "r");
            $i = 0;
            
            while (!feof($handle)) {
                call_user_func_array($callback, array(fread($handle, $chunkSize), &$handle, $i));
                $i++;
            }

            fclose($handle);
        } catch (Exception $e) {
            trigger_error("fileGetContentsChunked::" . $e->getMessage(), E_USER_NOTICE);
            return false;
        }

        return true;
    }
}
