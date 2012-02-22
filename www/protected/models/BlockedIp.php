<?php

Yii::import('application.models._base.BaseBlockedIp');

class BlockedIp extends BaseBlockedIp
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function rules() {
    // this regular expression test ip4 IP addresses
    $exp_ip = '/^(\*)$|';
    $exp_ip .= '^(([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.\*)$|';
    $exp_ip .= '^(([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5]))\.\*)$|';
    $exp_ip .= '^(([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){2}(\.(\*|([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5]))))$';
    $exp_ip .= '$/';
    
    return array(
      array('ip, created, modified', 'required'),
      array('ip', 'length', 'max'=>45),
      array('ip', 'match', 'pattern'=>$exp_ip, 'allowEmpty'=>false),
      array('type', 'length', 'max'=>5),
      array('type', 'default', 'setOnEmpty' => true, 'value' => null),
      array('id, ip, type, created, modified', 'safe', 'on'=>'search'),
    );
  }
}



