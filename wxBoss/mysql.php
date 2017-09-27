<?php
/**
 * Created by JetBrains PhpStorm.
 * User:JAE
 * Date: 13-8-13
 * Time: ����5:15
 * Blog:http://blog.jaekj.com
 * QQ:734708094
 * ͨ�����ݿ������
 * �汾:V1.1
 */

/* ���ݿ�����
  return array(
    'DB_CONFIG' => array(
        //���ݿ�����
        'DB_HOST'=>'127.0.0.1',    //��������ַ
        'DB_NAME' => 'tmp', // ���ݿ���
        'DB_USER' => 'root', // �û���
        'DB_PWD' => '', // ����
        'DB_ENCODE'=>'utf8',//����
        'DB_PREFIX' => 'dmtx_' // ���ݿ��ǰ׺
    )
);
 */
class M
{

    private $link; //���ݿ�����
    private $table; //����
    private $prefix; //��ǰ׺
    private $db_config; //���ݿ�����
    /**
     * ����:���� ���ݿ��������� �� ���ݿ������ļ�·��
     * @param $table
     * @param string $db_config_arr_path
     */
    function __construct($table, $db_config_arr_path = 'dbconfig.php')
    {
        if (is_array($db_config_arr_path)) {
            $this->db_config = $db_config_arr_path;
        } else {
            $this->db_config = require($db_config_arr_path);
        }
        $this->conn();
        $this->table = $this->prefix . $table;
    }

    /**
     * �������ݿ�
     */
    private function conn()
    {
        $db_config = $this->db_config;
        $host = $db_config["DB_CONFIG"]["DB_HOST"];
        $user = $db_config["DB_CONFIG"]["DB_USER"];
        $pwd = $db_config["DB_CONFIG"]["DB_PWD"];
        $db_name = $db_config["DB_CONFIG"]["DB_NAME"];
        $db_encode = $db_config["DB_CONFIG"]["DB_ENCODE"];
        $this->prefix = $db_config["DB_CONFIG"]["DB_PREFIX"];

        $this->link = mysql_connect($host, $user, $pwd) or die('���ݿ���������Ӵ���:' . mysql_error());
        mysql_select_db($db_name) or die('���ݿ����Ӵ���:' . mysql_error());
        mysql_query("set names '$db_encode'");
    }

    /**
     * ���ݲ�ѯ
     * ����:sql���� ��ѯ�ֶ� ʹ�õ�sql������
     * @param string $where
     * @param string $field
     * @param string $fun
     * @return array
     * ����ֵ:����� �� ���(�����ؿ��ַ���)
     */
    public function select($where = '1', $field = "*", $fun = '')
    {
        $rarr = array();
        if (empty($fun)) {
            $sqlStr = "select $field from $this->table where $where";
            $rt = mysql_query($sqlStr, $this->link);
            while ($rt && $arr = mysql_fetch_assoc($rt)) {
                array_push($rarr, $arr);
            }
        } else {
            $sqlStr = "select $fun($field) as rt from $this->table where $where";
            $rt = mysql_query($sqlStr, $this->link);
			//print_r($sqlStr);
            if ($rt) {
                $arr = mysql_fetch_assoc($rt);
                $rarr = $arr['rt'];
            } else {
                $rarr = '';
            }
        }
		//echo $sqlStr;
        return $rarr;
    }

    /**
     * ���ݸ���
     * ����:sql���� Ҫ���µ�����(�ַ��� �� ��������)
     * @param $where
     * @param $data
     * @return bool
     * ����ֵ:���ִ�гɹ���ʧ��,ִ�гɹ�������ζ�Ŷ����ݿ�������Ӱ��
     */
    public function update($where, $data)
    {
        $ddata = '';
        if (is_array($data)) {
            while (list($k, $v) = each($data)) {
                if (empty($ddata)) {
                    $ddata = "$k='$v'";

                } else {
                    $ddata .= ",$k='$v'";
                }
            }
        } else {
            $ddata = $data;
        }
        $sqlStr = "update $this->table set $ddata where $where";
		//echo $sqlStr;
        return mysql_query($sqlStr);
    }

    /**
     * �������
     * ����:����(���� �� �������� �� �ַ���)
     * @param $data
     * @return int
     * ����ֵ:��������ݵ�ID ���� 0
     */
    public function insert($data)
    {
        $field = '';
        $idata = '';
        if (is_array($data) && array_keys($data) != range(0, count($data) - 1)) {
            //��������
            while (list($k, $v) = each($data)) {
                if (empty($field)) {
                    $field = "$k";
                    $idata = "'$v'";
                } else {
                    $field .= ",$k";
                    $idata .= ",'$v'";
                }
            }
            $sqlStr = "insert into $this->table($field) values ($idata)";
        } else {
            //�ǹ������� ���ַ���
            if (is_array($data)) {
                while (list($k, $v) = each($data)) {
                    if (empty($idata)) {
                        $idata = "'$v'";
                    } else {
                        $idata .= ",'$v'";
                    }
                }

            } else {
                //Ϊ�ַ���
                $idata = $data;
            }
            $sqlStr = "insert into $this->table values ($idata)";
        }
		//echo $sqlStr;
        if(mysql_query($sqlStr,$this->link))
        {
            return mysql_insert_id($this->link);
        }
        return 0;
    }

    /**
     * ����ɾ��
     * ����:sql����
     * @param $where
     * @return bool
     */
    public function delete($where)
    {
        $sqlStr = "delete from $this->table where $where";
        return mysql_query($sqlStr);
    }
	
	
	/*
	query��ѯ
	������sql���
	���أ�array
	*/
	public function query($sql)
	{
		$rarr = array();
		$sqlStr = $sql;
		$rt = mysql_query($sqlStr, $this->link);
            while ($rt && $arr = mysql_fetch_assoc($rt)) {
                array_push($rarr, $arr);
            }
		return $rarr;
	}
	
	

    /**
     * �ر�MySQL����
     * @return bool
     */
	 
	 
    public function close()
    {
        return mysql_close($this->link);
    }

}