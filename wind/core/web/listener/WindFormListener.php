<?php

L::import('WIND:core.filter.WindHandlerInterceptor');
/**
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindFormListener extends WindHandlerInterceptor {

	/**
	 * @var WindHttpRequest
	 */
	private $request = null;

	private $formPath = '';

	private $errorMessage = null;

	/**
	 * @param WindHttpRequest $request
	 * @param string $formPath
	 */
	public function __construct($request, $formPath) {
		$this->request = $request;
		$this->formPath = $formPath;
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		//TODO 实现基于表单对象的表单验证机制，错误处理，表单对象集成WindEnableValidateModule
		$className = L::import($this->formPath);
		if (!class_exists($className)) throw new WindException('the form <' . $this->formPath . '> is not exists!');
		$form = new $className();
		$methods = get_class_methods($form);
		foreach ($methods as $method) {
		    $tmp = strtolower($method);
		    if (($pos = strpos($tmp, 'set')) !== 0 ) continue;
		    $tmp = substr($tmp, 3);
		    $value = $this->request->getPost($tmp) ? $this->request->getPost($tmp) : $this->request->getGet($tmp);
		    if ($value === null) continue;
		    call_user_func_array(array($form, $method), array($value));
		}
		call_user_func_array(array($form, 'validate'), array($form));
		if (($error = $form->getErrors())) {
		    $this->errorMessage = new WindErrorMessage($error);
		    $this->errorMessage->sendError();
		}
		$this->request->setAttribute('formData', $form);
	}
	
	private function getParentClass($form) {
	    
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {

	}

}

?>