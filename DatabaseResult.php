<?php
namespace Sleek;

/**
 * Handles database results for iterating over database rows
 */
class DatabaseResult implements \Countable, \Iterator {

    /**
     * Result set (extending mysqli_result had some weird bugs, so we encapsulate)
     * @var \mysqli_result
     */
    protected $result       = NULL;

    /**
     * Number of rows being returned, calculated once to be faster
     * @var int
     */
    protected $count        = 0;

    /**
     * Current row being looked at (works while iterating, now with ->fetch_*() functions)
     * @var int
     */
    protected $index        = 0;

    /**
     * @param \mysqli_result $result
     */
    public function __construct(\mysqli_result $result) {
        $this->result = $result;
        $this->count = $this->result->num_rows;
        $this->index = 0;
    }

    // Object Oriented approach

    /**
     * Returns the next available row as an associative array
     *  while ($row = $result->row()) { echo $row['id']; }
     * @return array
     */
    public function row() {
        return $this->result->fetch_assoc();
    }

    /**
     * Returns the next available row as an enumerated array
     *  while ($row = $result->enum()) { echo $row[0]; }
     * @return array
     */
    public function enum() {
        return $this->result->fetch_row();
    }

    /**
     * Returns the next available row as an object
     *  while ($row = $result->object()) { echo $row->id; }
     * @return stdClass
     */
    public function object() {
        return $this->result->fetch_object();
    }

    // Lets us run count($result) (implements Countable)

    /**
     * Returns the number of rows in this result
     * @return int
     */
    public function count() {
        return $this->count;
    }

    // Lets us do foreach($result AS $row) type stuff (implements Iterator)

    /**
     * Used by implements iterator
     * @return array
     */
    public function current() {
        $row = $this->result->fetch_assoc();
        $this->result->data_seek($this->index);
        return $row;
    }

    /**
     * Used by implements iterator
     * @return void
     */
    public function next() {
        if ($this->index < $this->count - 1) {
            $this->result->data_seek($this->index + 1);
        }

        $this->index++;
    }

    /**
     * Used by implements iterator
     * @return int
     */
    public function key() {
        return $this->index;
    }

    /**
     * Used by implements iterator
     * @return void
     */
    public function rewind() {
        $this->result->data_seek(0);
        $this->index = 0;
    }

    /**
     * Used by implements iterator
     * @return bool
     */
    public function valid() {
        if ($this->index >= 0 && $this->index < $this->count) {
            return TRUE;
        }

        return FALSE;
    }

}
