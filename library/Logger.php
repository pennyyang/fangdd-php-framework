<?php
/**
* 日志
*/
class Logger
{
    public function info($str)
    {
        return $this->log($str, 'info');
    }
    public function debug($str)
    {
        return $this->log($str, 'debug');
    }
    public function error($str)
    {
        return $this->log($str, 'error');
    }

    public function log($str, $level)
    {
        $log_root = APP_ROOT.'log';
        if (!file_exists($log_root)) {
            mkdir($log_root);
        }
        $level_root = $log_root.'/'.$level;
        if (!file_exists($level_root)) {
            mkdir($level_root);
        }
        $fpath = $level_root.'/'.date('Ymd');
        $f = fopen($fpath, 'a');
        $str = date('Y-m-d H:i:s').' '.strtoupper($level).': '.$str."\n";
        fwrite($f, $str);
        fclose($f);
    }

}
