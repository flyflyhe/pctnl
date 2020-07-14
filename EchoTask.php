<?php

namespace x;

class EchoTask extends Task
{
    public function run()
    {
        while (1) {
            $time = time();
            echo "{$this->pid} echo {$time}".PHP_EOL;
            sleep(1);
        }
    }
}