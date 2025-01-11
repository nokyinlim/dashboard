<?php
class Todo {
    private $tasks = [];

    public function addTask($task) {
        $this->tasks[] = $task;
    }

    public function getTasks() {
        return $this->tasks;
    }

    public function removeTask($index) {
        if (isset($this->tasks[$index])) {
            unset($this->tasks[$index]);
            $this->tasks = array_values($this->tasks); // Re-index the array
        }
    }
}
?>