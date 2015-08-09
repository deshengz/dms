<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status   数据状态
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_list_count($category, $status = 1){
    static $count;
    if(!isset($count[$category])){
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_nav_url($url){
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url = U($url);
            break;
    }
    return $url;
}
/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map  映射关系二维数组  array(
 *                                          '字段名1'=>array(映射关系数组),
 *                                          '字段名2'=>array(映射关系数组),
 *                                           ......
 *                                       )
 * @author 朱亚杰 <zhuyajie@topthink.net>
 * @return array
 *
 *  array(
 *      array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *      ....
 *  )
 *
 */
function int_to_string(&$data,$map=array('status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿'))) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        foreach ($map as $col=>$pair){
            if(isset($row[$col]) && isset($pair[$row[$col]])){
                $data[$key][$col.'_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}
/**
 * 显示对话框
 * 大没
 */
function showDialog($message = '', $url = '', $alert_type = 'error', $extrajs = '', $time = 2) {
	$message = str_replace ( "'", "\\'", strip_tags ( $message ) );

	$paramjs = '';
	if ($url == 'reload') {
		$paramjs = 'window.location.reload()';
	} elseif ($url != '') {
		$paramjs = 'window.location.href =\'' . $url . '\'';
	}
	if ($paramjs) {
		$paramjs = 'function (){' . $paramjs . '}';
	} else {
		$paramjs = 'null';
	}
	$modes = array (
			'error' => 'alert',
			'succ' => 'succ',
			'notice' => 'notice',
			'js' => 'js'
	);
	$cover = $alert_type == 'error' ? 1 : 0;
	$extra .= 'showDialog(\'' . $message . '\', \'' . $modes [$alert_type] . '\', null, ' . ($paramjs ? $paramjs : 'null') . ', ' . $cover . ', null, null, null, null, ' . (is_numeric ( $time ) ? $time : 'null') . ', null);';
	$extra = $extra ? '<script type="text/javascript" reload="1">' . $extra . '</script>' : '';
	if ($extrajs != '' && substr ( trim ( $extrajs ), 0, 8 ) != '<script>') {
		$extrajs = '<script type="text/javascript" reload="1">' . $extrajs . '</script>';
	}
	$extra .= $extrajs;
	ob_end_clean ();
	@header ( "Expires: -1" );
	@header ( "Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE );
	@header ( "Pragma: no-cache" );
	@header ( "Content-type: text/xml; charset='UTF-8'" );

	$string = '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n";
	$string .= '<root><![CDATA[' . $message . $extra . ']]></root>';
	echo $string;
	exit ();
}