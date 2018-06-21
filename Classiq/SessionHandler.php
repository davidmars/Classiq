<?php

namespace Classiq;


use Classiq\Models\Session;
use SessionHandlerInterface;

/**
 * Class SessionHandler
 * @package Classiq
 */
class SessionHandler implements SessionHandlerInterface
{
    /**
     * @var bool juste pour éviter de faire un touch plusieurs fois
     */
    private static $touched=false;

    public function __construct(){


        // Set handler to overide SESSION
        session_set_save_handler(
            [$this, "open"],
            [$this, "close"],
            [$this, "read"],
            [$this, "write"],
            [$this, "destroy"],
            [$this, "gc"]
        );
        register_shutdown_function('session_write_close');
        session_name();
        session_start();
    }
    public function __destruct()
    {
        $this->close();
    }

    public function open($savePath, $sessionName)
    {
        $sessid=session_id();
        $s=Session::getBySessid($sessid);
        if(!$s){
            return false;
        }
        if($s->willExpire()){
            $s->touch();
        }
        //pov()->log->info("open session");
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($sessid)
    {
        $s=Session::getBySessid($sessid);
        if(!$s->data){
           return "";
        }
        return $s->data;
    }

    /**
     * En fait on ne l'utilise jamais on attaque directement
     * @param string $sessid
     * @param string $data
     * @return bool
     */
    public function write($sessid, $data)
    {
        $s=Session::getBySessid($sessid);
        if($data){
            $s->data=$data;
            if($s->changes()){
                db()->store($s);
            }

        }
        return true;
    }

    public function destroy($sessid)
    {
        $s=Session::getBySessid($sessid);
        db()->trash($s);
        return true;
    }

    public function gc($maxlifetime)
    {
        /*
        $maxlifetime=3600;
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }
        */
        return true;
    }
}