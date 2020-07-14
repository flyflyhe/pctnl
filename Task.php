<?php

namespace x;

abstract class Task
{
    protected $pid;

    protected $pipe;

    protected $status;

    public function init()
    {
        $this->pid = posix_getpid();
    }

    public function __construct()
    {
    }

    abstract function run();

    /**
     * @return mixed
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @param mixed $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * @return mixed
     */
    public function getPipe()
    {
        return $this->pipe;
    }

    /**
     * @param mixed $pipe
     */
    public function setPipe($pipe)
    {
        $this->pipe = $pipe;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}