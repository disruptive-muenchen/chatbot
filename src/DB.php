<?php

/**
 * Class DB
 *
 * This class is a simple interface for interacting with a SQLite database.
 */
class DB
{
    /**
     * @var PDO $db The PDO instance representing the SQLite database connection.
     */
    private $db;

    /**
     * DB constructor.
     *
     * Establish a connection with a SQLite database and create a messages table.
     *
     * @param string $database Path to the SQLite database file.
     */
    public function __construct($database)
    {
        if (!file_exists($database)) {
            throw new Exception("Database file does not exist: $database");
        }

        if (!is_writable($database)) {
            throw new Exception("Database file is not writable: $database");
        }

        if (!is_writable(dirname($database))) {
            throw new Exception("Database directory is not writable: " . dirname($database));
        }

        try {
            $this->db = new PDO('sqlite:' . $database);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->exec("CREATE TABLE IF NOT EXISTS messages (user_id TEXT, date TEXT, message TEXT)");
            $this->db->exec("CREATE TABLE IF NOT EXISTS events (event_id TEXT, user_id TEXT, date TEXT)");
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Insert a new event record.
     *
     * @param string $event_id The ID of the event.
     * @param string $user_id The ID of the user.
     */
    public function addEvent($event_id, $user_id)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO events (event_id, user_id, date) VALUES (:event_id, :user_id, datetime())");
            $stmt->execute([':event_id' => $event_id, ':user_id' => $user_id]);
        } catch (PDOException $e) {
            throw new Exception("Failed to add event: " . $e->getMessage());
        }
    }

    /**
     * Check if an event already exists.
     *
     * @param string $event_id The ID of the event.
     * @param string $user_id The ID of the user.
     * @return bool True if the event is already in the database.
     */
    public function eventExists($event_id, $user_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM events WHERE event_id = :event_id and user_id = :user_id");
            $stmt->execute([':event_id' => $event_id, ':user_id' => $user_id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Failed to check event: " . $e->getMessage());
        }
    }

    /**
     * Insert a new message record.
     *
     * @param string $user_id The ID of the user associated with the message.
     * @param string $message The message text.
     */
    public function addMessage($user_id, $message)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO messages (user_id, date, message) VALUES (:user_id, datetime(), :message)");
            $stmt->execute([':user_id' => $user_id, ':message' => $message]);
        } catch (PDOException $e) {
            throw new Exception("Failed to add message: " . $e->getMessage());
        }
    }
}
