<?php
namespace Common\Model;

class MongoModel extends \Think\Model\MongoModel
{
	public function __construct($name = '', $tablePrefix = '')
	{
		parent::__construct($name, $tablePrefix, "MONGODB_CONNECTION");
	}
}
