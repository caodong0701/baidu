<?php

class DBOperator {

    /**
     * 当前数据库链接的句柄
     *
     * @var resources
     */
    public $link = null;

    /**
     * 静态变量，所有数据库链接的句柄的集合
     *
     * @var array
     */
    private static $links = null;

    function __construct($host = "127.0.0.1", $username = "root", $passwd = "caodong0701", $dbname = "ucdz") {

        //建立数据库连接

        $this->link = mysqli_init();

        //设置超时时间为2s,防止数据库离线后用户长时间数据库连接长时间无法返回
        mysqli_options($this->link, MYSQLI_OPT_CONNECT_TIMEOUT, 2);
        @mysqli_real_connect($this->link, $host, $username, $passwd, $dbname);

        //判断是否连接失败
        if (mysqli_connect_errno()) {
            $this->link = false;
        }
        if($this->link){
            @$this->link->query("set names utf8;");
        }
    }

    function get_caller_method() {
        $traces = debug_backtrace();
        $ret_str = "";
        if (isset($traces[1]['file']) && isset($traces[1]['function']) && isset($traces[1]['line'])) {
            $ret_str .= $traces[1]['file'] . "--" . $traces[1]['function'] . "--" . $traces[1]['line'];
        }
        if (isset($traces[2]['file']) && isset($traces[2]['function']) && isset($traces[2]['line'])) {
            $ret_str .="--" . $traces[2]['file'] . "--" . $traces[2]['function'] . "--" . $traces[2]['line'];
        }
        return $ret_str;
    }

    function reConnecting($host, $username, $passwd, $dbname) {
        //建立数据库连接
        $this->link = mysqli_init();
        //设置超时时间为2s,防止数据库离线后用户长时间数据库连接长时间无法返回
        mysqli_options($this->link, MYSQLI_OPT_CONNECT_TIMEOUT, 2);
        @mysqli_real_connect($this->link, $host, $username, $passwd, $dbname);

        if ($endTime - $startTime > THRESHOLD_MYSQL) {
        }
        //判断是否连接失败
        if (mysqli_connect_errno()) {
            $this->link = false;
            AB_Log::error($host . "-" . $dbname . "-" . $username . "  " . date('Y-m-d H:i:s') . " ReConnet Failed Code:" . mysqli_connect_errno());
        }
        //连接失败，写session;成功则保存
        if ($this->link == false) {
            self::$links["$host-$dbname-$username-$passwd"] = null;
            $_SESSION['DB_MISS'] = time();
            AB_Log::error($host . "-" . $dbname . "-" . $username . "  " . ($endTime - $startTime) . " MySQL ReConnet Failed.");
        } else {
            self::$links["$host-$dbname-$username-$passwd"] = $this->link;
            unset($_SESSION['DB_MISS']);
        }
    }

    /**
     * run a single query from the link DBManager specified
     * static
     * @access public
     */
    function runQuery($query) {
        if ($this->link == false)
            return false;

        return $this->_query($query);
    }

    /**
     * run a multi query from the link DBManager specified
     * static
     * @access public
     */
    function runMultiQuery($query) {
        if ($this->link == false) {
            return false;
        }
        return $this->link->multi_query($query);
    }

    /**
     * run a trascation
     *
     * @param array $queries
     * @return int
     */
    function runTransaction($queries) {
        if ($this->link == false || count($queries) == 0) {
            return false;
        }
        $this->link->autocommit(false);
        $result = true;
        foreach ($queries as $query) {
            $result = $this->_query($query);
            if (!$result) {
                $this->link->rollback();
                break;
            }
        }
        if ($result) {
            $result = $this->link->commit();
        }
        $this->link->autocommit(true);
        return $result;
    }

    /**
     * get the 2-d array by executing the query
     * the 1st-d is the records specified by number, the the 2nd-d is the record specified by column name
     * static
     * @access public
     */
    function getPropertiesByQuery($query, &$attrs) {
        if ($this->link == false) {
            $attrs = null;
            return false;
        }
        $attrs = null;
        if (($result = $this->_query($query))) {
            if (($num = $result->num_rows) > 0) {
                $attrs = array();
                $i = 0;
                $attrs[$i] = array();
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    foreach ($row as $key => $value) {
                        $attrs[$i][$key] = $value;
                    }
                    $i++;
                }
                return QUERY_SUCC;
            }
            return NO_FETCH_RESULT;
        }
        return QUERY_ERR;
    }

    function getTableFromSql($query) {
        if ($this->link == false) {
            return null;
        }
        $attrs = null;
        if (($result = $this->_query($query))) {
            if (($num = $result->num_rows) > 0) {
                $attrs = array();
                $i = 0;
                $attrs[$i] = array();
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    foreach ($row as $key => $value) {
                        $attrs[$i][$key] = $value;
                    }
                    $i++;
                }
                return $attrs;
            }
            return null;
        }
        return null;
    }

    /**
     * 取得SQL语句的一个返回值
     * 如果SQL语句返回了多行多列，则只取第一行第一列。
     * @access public
     * @param query 一条select语句
     * @return 如果query语句取回数据，则取第一行第一列；如果取不到数据，则返回0；否则返回-1；
     */
    public function getOneFromSql($query) {
        if ($this->link == false) {
            return null;
        }

        if (($result = $this->_query($query))) {
            if (($num = $result->num_rows) > 0) {
                $row = $result->fetch_array(MYSQLI_NUM);
                return $row[0];
            } else {
                return null;
            }
        }
        return null;
    }

    public function getArrayFromSql($query) {
        if ($this->link == false)
            return null;

        if (($result = $this->_query($query))) {
            if (($num = $result->num_rows) > 0) {
                $row = $result->fetch_array();
                return $row;
            } else {
                return null;
            }
        }
        return null;
    }

    public function getInsertId() {
        if ($this->link == false)
            return null;
        else
            return $this->link->insert_id;
    }

    /**
     * 获取最近一次执行影响的结果条数
     *
     * @return int
     */
    public function getAffectedRows() {
        if ($this->link == false)
            return null;
        else
            return $this->link->affected_rows;
    }

    public function close() {
        if ($this->link == false)
            return null;
        else
            return $this->link->close();
    }

    /**
     * 对输入字符串的字符进行转义。考虑了数据库的编码。
     *
     * @param string $str
     * @return string
     */
    public function escape($str) {
        if (!$str)
            return '';
        if ($this->link == false)
            return $str;
        else
            return $this->link->real_escape_string($str);
    }

    public function _query($query) {

        $result = @$this->link->query($query);
        return $result;
    }

    //开始事务
    public function startTransation() {
        if ($this->link != false) {
            $this->link->autocommit(false);
        }
    }

    //提交事务
    public function commitTransation() {
        if ($this->link != false) {
            $result = $this->link->commit();
            $result = $this->link->autocommit(true);
        }
    }

    //结束事务
    public function endTransation() {
        if ($this->link != false) {
            $this->link->autocommit(true);
        }
    }

    //回滚事务
    public function rollbackTransation() {
        if ($this->link != false) {
            $this->link->rollback();
        }
    }

}

?>
