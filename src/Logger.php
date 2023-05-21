<?php

/**
 * Class Logger
 *
 * This class is used for logging actions taken in the app to assist with debugging.
 */
class Logger
{
    /**
     * @var string $log_file Path to the log file.
     */
    private $log_file;

    /**
     * @var int $log_file_lines Number of lines to keep in the log file before cleaning up.
     */
    private $log_file_lines;
    
    /**
     * @var string $event_id The ID of the current event.
     */
    private $event_id = 'undefined';

    /**
     * Logger constructor.
     *
     * @param string $log_file Path to the log file.
     * @param int $log_file_lines Optional. Number of lines to keep in the log file.
     *
     * @throws Exception if the log file is not writable.
     */
    public function __construct($log_file, $log_file_lines = 100)
    {
        if (!is_writable($log_file) && file_exists($log_file)) {
            throw new Exception("Log file is not writable: $log_file");
        }

        $this->log_file = $log_file;
        $this->log_file_lines = $log_file_lines;
    }

    /**
     * Set the ID of the current event.
     *
     * @param string $event_id The ID of the event.
     */
    public function setEvent($event_id)
    {
        $this->event_id = $event_id;
    }

/**
 * Log a message to the log file and ensure some data cleanup/retention.
 *
 * @param string $message The message to log.
 *
 * @throws Exception if the log message could not be written to the log file.
 */
public function log($message)
{
    $date = date('Y-m-d H:i:s');
    $log_entry = sprintf("%s: (%s) %s\n", $date, $this->event_id, $message);

    if (file_put_contents($this->log_file, $log_entry, FILE_APPEND) === false) {
        throw new Exception("Failed to write to log file: $this->log_file");
    }

    // Keep only a certain number of lines in the log file.
    $file_content = file($this->log_file, FILE_IGNORE_NEW_LINES);
    if (count($file_content) > $this->log_file_lines) {
        $file_content = array_slice($file_content, -$this->log_file_lines);
        if (file_put_contents($this->log_file, implode(PHP_EOL, $file_content) . PHP_EOL) === false) {
            throw new Exception("Failed to cleanup the log file: $this->log_file");
        }
    }
}

}
