<?php
class LogUtils
{
    function _log($msg)
    {
        $logfile = AS_LOG_DIR . date('Y-m-d', time()) . '/mapi.log';
        if(!is_dir($dir = dirname($logfile))){
            @mkdir($dir, 755);
        }
        $msg = date('H:i:s',time()) . "\t" . $msg . "\r\n";
        file_put_contents ( $logfile, $msg, FILE_APPEND );
    }
    
    
    function log_str($msg)
    {
        if (AS_DEBUG) LogUtils::_log($msg);
    }
    
    function log_obj($obj)
    {
        if (AS_DEBUG)
        {
            ob_start();
            var_dump($obj);
            $msg = ob_get_contents();
            ob_end_clean();
            LogUtils::_log($msg);
        }
    }
    
    function clear_log()
    {
        $logfile = AS_LOG_DIR . date('Y-m-d', time()) . '/assis.log';
        if (file_exists($logfile))
        {
            unlink($logfile);
        }
    }
}
?>
