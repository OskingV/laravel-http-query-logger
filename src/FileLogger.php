<?php

namespace Oskingv\HttpQueryLogger;

use Illuminate\Support\Facades\File;
use Oskingv\HttpQueryLogger\Contracts\LoggerInterface;

class FileLogger extends AbstractLogger implements LoggerInterface
{

    /**
     * file path to save the logs
     */
    protected $path;

    public function __construct()
    {
        parent::__construct();
        $this->path = storage_path('logs/http-query-logger');
    }

    /**
     * read files from log directory
     *
     * @param string $date
     * @return array
     */
    public function getLogs(string $date)
    {
        //check if the directory exists
        if (is_dir($this->path)) {
            //scann the directory
            $files = scandir($this->path);

            $contentCollection = [];

            //loop each files
            foreach ($files as $file) {
                if (!is_dir($file)) {
                    if (strpos($file, $date) === false) {
                        continue;
                    }
                    $lines = file($this->path.DIRECTORY_SEPARATOR.$file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    foreach ($lines as $line) {
                        $contentarr = explode(";", $line);
                        array_push($contentCollection, $this->mapArrayToModel($contentarr));
                    }
                }
            }
            return collect($contentCollection);
        } else {
            return [];
        }
    }

    /**
     * write logs to file
     *
     * @param [type] $request
     * @param [type] $response
     * @return void
     */
    public function saveLogs($request, $response)
    {
        $data = $this->logData($request, $response);
        if ($data !== null) {
            $filename = $this->getLogFilename();

            $contents = implode(";", $data);

            File::makeDirectory($this->path, 0777, true, true);


            File::append(($this->path.DIRECTORY_SEPARATOR.$filename), $contents.PHP_EOL);
        }
    }

    /**
     * get log file if defined in constants
     *
     * @return string
     */
    public function getLogFilename()
    {
        // original default filename
        $filename = 'http-query-'.date('d-m-Y') . '.log';

        $configFilename = config('http-query-logger.filename', 'log-{Y-m-d}.log');
        preg_match('/{(.*?)}/', $configFilename, $matches, PREG_OFFSET_CAPTURE);
        if (sizeof($matches) > 0) {
            $filename = str_replace($matches[0][0], date("{$matches[1][0]}"), $configFilename);
        }
        return $filename;
    }

    /**
     * delete all api log  files
     *
     * @return void
     */
    public function deleteLogs()
    {
        if (is_dir($this->path)) {
            File::deleteDirectory($this->path);
        }

    }

}
