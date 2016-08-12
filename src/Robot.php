<?php
namespace Robot;
/**
 * Robot.php
 * @author  lmh <991564110@qq.com|Q:991564110>
 * @link http://www.991564110.com/
 * @copyright 2015-2016 991564110.com
 * @package Robot\Robot
 * @since 1.0
 * @date: 2016/8/11- 20:59
 */
class Robot
{

    private static $_instance;


    protected $data = [];

    /**
     * 消息类型
     * @var array
     */
    private $type = [
        1,//	私聊信息 	11/来自好友 1/来自在线状态 2/来自群 3/来自讨论组
        2,// 	群消息 	目前固定为1
        4,//	讨论组信息 	目前固定为1
        11,// 	上传群文件 	目前固定为1
        101,//	群管理员变动 	1/被取消管理员 2/被设置管理员
        102,//	群成员减少 	1/群员离开 2/群员被踢 3/自己(即登录号)被踢
        103,//	群成员增加 	1/管理员已同意 2/管理员邀请
        201,//	好友已添加 	目前固定为1
        301,//	请求添加好友 	目前固定为1
        302
    ];
    /**
     * 消息子类型
     * @var array
     */
    private $subType = [

    ];
    /**
     *Type    整数型(int)    消息类型
     * SubType    整数型(int)    消息子类型
     * QQ    长整数型(int64)    发送消息的QQ号
     * Group    长整数型(int64)    消息来源的群号
     * Discuss    长整数型(int64)    消息来源的讨论组号
     * OtherQQ    长整数型(int64)    可以理解成被动的操作对象(被禁言的人等)
     * Parm    文本型(text)    附加参数,如处理标识,多个参数时使用一个竖线分割
     * Msg    文本型(text)    发送的消息内容,也有可能是某些事件(如加群)的请求理由
     * 提交参数说明
     * @var array
     */
    private $msgField = [
        'Type' => 'Type',
        'SubType' => 'SubType',
        'QQ' => 'QQ',
        'Group' => 'Group',
        'Discuss' => 'Discuss',
        'OtherQQ' => 'OtherQQ',
        'Parm' => 'Parm',
        'Msg' => 'Msg'
    ];
    /**
     *
     * Type    整数型(int)    将要处理的消息类型(或者说是API调用代码),不得缺少
     * Group    长整数型(int64)    群/讨论组号,仅在使用与此有关API下可用
     * QQ    长整数型(int64)    QQ号,发送私聊信息,群相关操作,处理好友时可用
     * Msg    文本型(text)    要发送的信息/拒绝理由,可拼接
     * Send    整数型(int)    是否在此处发出信息(调用API时不需要),固定为1(发送)
     * Time    整数型(int)    时间,单位为秒,用于设置禁言时间/头衔有效时间
     * Name    文本型(text)    名片,设置群名片/头衔/好友备注时可用
     * Skn1    长整数型(int64)    第二类型,有多种含义,视具体情况而定
     * Skn2    文本型(text)    处理标识,用于审批入群/加好友
     * Skn3    整数型(int)    请求类型,常见类型 群添加(1) 或 群邀请(2)
     * URL    文本型(text)    文件地址,发送图片以及语音时使用
     * FileType    文本型(text)    文件后缀名,一般不超过4位
     * 返回参数说明
     * @var array
     */
    private $returnField = [
        'Type' => 'Type',
        'Group' => 'Group',
        'QQ' => 'QQ',
        'Msg' => 'Msg',
        'Send' => 'Send',
        'Time' => 'Time',
        'Name' => 'Name',
        'Skn1' => 'Skn1',
        'Skn2' => 'Skn2',
        'Skn3' => 'Skn3',
        'URL' => 'URL',
        'FileType' => 'FileType'
    ];

    /**
     * CQ码类API
     */
    protected $CQ = [
        '-1',//发送图片 (-1)
        '-2',//发送语音 (-2)
        '-3',//     发送表情 (-3)
        '-4',//     发送Emoji表情 (-4)
        '-5',//     发送音乐 (-5)
        '-6',//     发送窗口抖动 (-6)
        '-7'// 艾特某人 (-7)

    ];

    protected $interCode = [
        '1',//发送私聊信息 (1)
        '2',//发送群信息 (2)
        '3',//发送讨论组信息 (3)
        '4',//置全群禁言 (4)
        '5',//置群成员禁言 (5)
        '6',//     置群成员移除 (6)
        '7',//置群成员名片 (7)
        '8',//置群成员专属头衔 (8)
        '9',//     置群管理员 (9)
        '10',//开启/关闭匿名功能 (10)
        '11',//  置群退出 (11)
        '12',//赞他人QQ名片 (12)
        '13',//         置群添加请求 (13)
        '14',//     置好友添加请求 (14)
        '15'//    置讨论组退出 (15)


    ];

    protected $msgArray = array();

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function setData()
    {
        //接收提交的所有POST数据
        $input = file_get_contents("php://input");
        //对提交的POST数据解码
        $input = urldecode($input);
        file_put_contents('as.txt', $input . "\r\n", FILE_APPEND);
        //对解码后的数据进行Json解析
        $this->data = json_decode($input, true);
    }

    private function __construct()
    {
    }

    /**
     * @return Robot
     */
    public static function init()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     *
     */
    public function sendMsg()
    {
        $return = json_encode($this->getMsgData());
        file_put_contents('as.txt', $return . "\r\n", FILE_APPEND);
        die($return);
    }

    /**
     * @return array|void
     */
    public function getMsgData()
    {
        //群消息
        if (isset($this->data[$this->msgField['Group']])) {
            return $this->groupMessages();
        }
        //讨论组消息
        if (isset($this->data[$this->msgField['Discuss']])) {
            return $this->discussMessages();
        }
        return $this->privateMessages();


    }


    /**
     *
     */
    protected function groupMessages()
    {
        return ['data' => array(
            [
                $this->msgField['Type'] =>-7,
                //$this->msgField['Group'] => $this->data[$this->returnField['Group']],
               // $this->msgField['QQ'] => $this->data[$this->returnField['QQ']],
                //$this->msgField['Msg'] => $this->returnMsg(),
                'Skn1' => $this->data[$this->returnField['QQ']]
            ]
        )];
    }

    protected function discussMessages()
    {
        return ['data' => array(
            [
                $this->msgField['Type'] => $this->data[$this->returnField['Type']],
                $this->msgField['Discuss'] => $this->data[$this->returnField['Discuss']],
                $this->msgField['QQ'] => $this->data[$this->returnField['QQ']],
                $this->msgField['Msg'] => $this->returnMsg(),
            ]
        )];
    }


    protected function privateMessages()
    {
        return ['data' => array(
            [
                $this->msgField['Type'] => $this->data[$this->returnField['Type']],
                $this->msgField['QQ'] => $this->data[$this->returnField['QQ']],
                $this->msgField['Msg'] => $this->returnMsg(),

            ]
        )];
    }


    protected function returnMsg()
    {

        return '我来了';
    }

}
