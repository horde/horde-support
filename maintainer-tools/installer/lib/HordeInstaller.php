<?php

class HordeInstaller
{
    private $log;

    public function initLogger($logfile)
    {
        $this->log = new Horde_Log_Logger(new Horde_Log_Handler_Null());
        $this->log->addHandler(new Horde_Log_Handler_Stream($logfile));
    }

    public function pear($cmd)
    {
        $this->log->notice("PEAR COMMAND: $cmd\n");
        exec($cmd, $output);
        foreach ($output as $val) {
            $this->log->info($val);
        }
    }

}
