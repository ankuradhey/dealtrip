<?php
class Zend_View_Helper_LinkTo
{
    protected static $baseUrl = null;
    public function linkTo($path)
    {
        if (self::$baseUrl === null) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $root = '/' . trim($request->getBaseUrl(), '/');
            if ($root == '/') {
                $root = '';
            }
            self::$baseUrl = $root . '/';
        }
        return self::$baseUrl . ltrim($path, '/');
    }
}
?>