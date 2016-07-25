<?php
/**
 *  (简述）
 * （详细的功能描述）
 * @copyright Copyright (c) 2016, http://www.hbdata.com. All Rights Reserved.
 * @author：Anewnoob
 */

/**
 * 对/cache目录的缓存文件进行删除修改操作
 * Class fzz_cache
 */
class fzz_cache
{
    /**
     * @var int     $limit_time 限制时间
     * @var string  $cache_dir  缓存目录路径
     */
    public $limit_time = 1000;
    public $cache_dir = CACHE_DIR;

    /**
     * 执行set($key, $val)函数
     * @param $key  缓存的文件名
     * @param $val  缓存的内容
     */
    function __set($key, $val)
    {
        $this->set($key, $val);
    }

    /**
     * 把缓存写入$key."php"并更新文件的修改时间
     * @param $key 创建的PHP文件名
     * @param $val 写入缓存的内容
     * @param null $limit_time
     */
    function set($key, $val, $limit_time = null)
    {
        $limit_time = $limit_time ? $limit_time : $this->limit_time;
        if (is_dir($this->cache_dir)) {
            $file = $this->cache_dir . "/" . $key . ".php";
            $val = serialize($val);
            @file_put_contents($file, $val) or $this->error(__line__, "文件写入失败");
            @touch($file, time() + $limit_time) or $this->error(__line__, "更改文件时间失败");
        }
    }

    /**
     * 执行get($key)函数
     * @param $key
     * @return mixed
     */
    function __get($key)
    {
        return $this->get($key);
    }

    /**
     * 比较缓存文件的上次修改时间与系统当前时间，小于则删除
     * @deprecated      看不懂这个函数
     * @param $key      PHP文件名
     * @return mixed    读出的缓存内容
     */
    function get($key)
    {
        $file = $this->cache_dir . "/" . $key . ".php";
        if (@filemtime($file) >= time()) {
            return unserialize(file_get_contents($file));
        } else {
            @unlink($file);
        }
    }

    /**
     * 执行_unset($key)函数
     * @param $key
     * @return bool
     */
    function __unset($key)
    {
        return $this->_unset($key);
    }

    /**
     * 如果缓存文件删除成功，返回true,否则返回false
     * @param $key   缓存文件名
     * @return bool  布尔值
     */
    function _unset($key)
    {
        if (@unlink($this->cache_dir . "/" . $key . ".php")) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 执行_isset($key)函数
     * @param $key
     * @return bool
     */
    function __isset($key)
    {
        return $this->_isset($key);
    }

    /**
     * 如果缓存文件的上次修改时间大于等于系统当前时间，返回true，否则删除缓存文件，返回false
     * @param $key  缓存文件名
     * @return bool 布尔值
     */
    function _isset($key)
    {
        $file = $this->cache_dir . "/" . $key . ".php";
        if (@filemtime($file) >= time()) {
            return true;
        } else {
            @unlink($file);
            return false;
        }
    }

    /**
     * 清除/cache目录下的所有被修改过的缓存文件
     */
    function clear()
    {
        $files = scandir($this->cache_dir);
        foreach ($files as $val) {
            if (filemtime($this->cache_dir . "/" . $val)) {
                @unlink($this->cache_dir . "/" . $val);
            }
        }
    }

    /**
     * 清除/cache目录下的所有缓存文件
     */
    function clear_all()
    {
        $files = scandir($this->cache_dir);
        foreach ($files as $val) {
            @unlink($this->cache_dir . "/" . $val);
        }
    }

    /**
     * 输出错误信息
     * @param $line 出错行
     * @param $msg  错误信息
     */
    function error($line, $msg)
    {
        die("出错文件：" . __file__ . "\n出错行：$line\n错误信息：$msg");
    }
}

?>