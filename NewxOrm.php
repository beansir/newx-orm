<?php
/**
 * @author bean
 * @version 1.0
 */

define('NEWX_ORM_PATH', __DIR__); // 根目录

require NEWX_ORM_PATH . '/base/BaseOrm.php';

class NewxOrm extends \newx\orm\base\BaseOrm {}

NewxOrm::init();