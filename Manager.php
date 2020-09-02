<?php

namespace x;

declare(ticks=1);
class Manager
{
    protected $pidMap;

    protected $pid;

    protected $pipeMode = 0600;

    public $taskArr = [];

    public $taskFailed = [];

    public function __construct()
    {
        $this->pid = posix_getpid();
    }

    public function registerSignal()
    {
        foreach ([SIGQUIT, SIGHUP, SIGUSR1, SIGCHLD] as $signal) {
            pcntl_signal($signal, [$this, "signalHandle"]);
        }
    }

    public function signalHandle(int $signal)
    {
        switch ($signal) {
            case SIGQUIT:
                echo "Caught SIGTERM...\n";
                exit;
                break;
            case SIGHUP:
                echo "Caught SIGHUP...\n";
                //处理SIGHUP信号
                break;
            case SIGUSR1:
                echo "Caught SIGUSR1...\n";
                break;
            case SIGCHLD:
                echo "子进程退出\n";
            default:
                // 处理所有其他信号
        }
    }

    public function run()
    {
        $this->registerSignal();
        if (empty($this->taskArr)) {
            throw new \Exception("任务不能为空");
        }
        foreach ($this->taskArr as $key => $task) {
            if (!$task instanceof Task) {
                throw new \Exception("task is not instanceof Abstract Task");
            }
            $pipe = "/tmp/ff_".$key;
            if (!$this->createPipe($pipe)) {
                throw new \Exception("pipe create failed");
            }

            $task->setPipe($pipe);
            $pid = $this->runTask($task);
            if ($pid === -1) {
                $this->taskFailed[] = $task;
            } else {
                $this->pidMap[$pid] = $pipe;
            }
        }

        var_export($this->pidMap);
        while (count($this->pidMap)) {
            echo "主进程 {$this->pid} 执行中".PHP_EOL;
            foreach ($this->pidMap as $pid => $_) {
                $status = null;
                $res = pcntl_waitpid($pid, $status, WNOHANG);
                if ($res > 0 || $res == -1) {
                    unset($this->pidMap[$pid]);
                }
                sleep(2);
            }
        }

        echo "执行完毕".PHP_EOL;
    }

    public function createPipe(string $pipe, int $mode = null)
    {
        if (file_exists($pipe)) {
            unlink($pipe);
        }
        if (!posix_mkfifo($pipe, $mode ?: $this->pipeMode)) {
            return false;
        }

        return  true;
    }

    public function runTask(Task $task)
    {
        $pid = pcntl_fork();
        if ($pid === -1) {
            return -1;
        } else if ($pid === 0) {
            $task->init();
            $task->run();
            exit();
        } else {
            return $pid;
        }
    }

    public function setTask(...$tasks)
    {
        $this->taskArr = $tasks;
    }
}