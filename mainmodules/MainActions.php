<?php
require_once APP_ROOT.'/model/User.php';
require_once APP_ROOT.'/model/Config.php';
require_once APP_ROOT.'/model/Package.php';

class MainActions extends mfwActions
{
	const TEMPLATEDIR = '/data/templates';
	const BLOCKDIR = '/data/blocks';

	const SESKEY_URL_BEFORE_LOGIN = 'url_before_login';

	/**
	 * @var User
	 */
	protected $login_user = null;

	public function initialize()
	{
		if(($err=parent::initialize())){
			return $err;
		}

/*
		// いくつかのapiはAPI Key認証なのでログイン不要
		if($this->module==='api'){
			if(in_array($this->action,array('upload','package_list','delete', 'create_token'))){
				return null;
			}
*/
		// API,Ajaxは認証を派生クラスに任せる
		if(in_array($this->module,array('api','ajax'))){
			return null;
		}

		// package/install_plist はセッションが使えないため別途認証する.
		if($this->module==='package' && $this->action==='install_plist'){
			return null;
		}

		// QR CodeはpublicなActionに
		if($this->module === "qr") {
			return null;
		}

		if ( Config::get('enable_request_ios_udid') ) {
              		static $pf = array(
                        	'android' => Package::PF_ANDROID,
                        	'ios' => Package::PF_IOS,
                        	'all' => null,
                        	);

                	$platform = mfwRequest::param('pf');
                	if ( !in_array($platform, array('android', 'ios', 'all')) ) {
                        	$ua = mfwRequest::userAgent();
                       		if ( $ua->isAndroid() ) {
                                	$platform = 'android';
                        	}
                        	elseif ( $ua->isIOS() ) {
                                	$platform = 'ios';
                        	}
                        	else {
                                	$platform = 'unknown';
                        	}
                	}

			$device_uuid = mfwRequest::param('device_uuid');
			if ( $platform == 'ios' && !empty($device_uuid) ) {
				User::loginWithUUID($device_uuid);
			}
		}

		$this->login_user = User::getLoginUser();
		if(!$this->login_user && $this->getModule()!='login'){
			$scheme = Config::get('enable_https')?'https':null;
			$this->saveUrlBeforeLogin($scheme);
			return $this->redirect(mfwRequest::makeUrl('/login',$scheme));
		}

		if($this->login_user){
			apache_log('user',$this->login_user->getMail());
		}

		if ( Config::get('enable_request_ios_udid') ) {
			if ( in_array($this->module, array('profile')) ) {
				return null;
			}

			if ( $this->login_user ) {
				$device_uuid = $this->login_user->getDeviceUUID();
		  		if ( $platform == "ios" && empty($device_uuid) ) {
                        		return $this->redirect(mfwRequest::makeUrl('/profile'));
				}
			}
		}
		return null;
	}

	protected function build($params=array(),$headers=array())
	{
		$params['login_user'] = $this->login_user;
		$params['module'] = $this->module;
		$params['action'] = $this->action;
		$params['title_prefix'] = Config::get('title_prefix');
		return parent::build($params,$headers);
	}


	protected function buildErrorPage($message,$headers=array())
	{
		$params = array(
			'message' => $message,
			);
		$this->setTemplateName('_error');
		return $this->build($params,$headers);
	}

	protected function response($status,$message=null)
	{
		return array(array($status),$message);
	}

	protected function jsonResponse($status,$contents)
	{
		$header = array(
			$status,
			'Content-type: application/json',
			);
		return array($header,json_encode($contents));
	}

	protected function saveUrlBeforeLogin()
	{
		if(mfwRequest::method()==='GET'){
			$url = mfwRequest::url();
			if(Config::get('enable_https')){
				$url = preg_replace('/^http:/','https:',$url);
			}
			mfwSession::set(self::SESKEY_URL_BEFORE_LOGIN,$url);
		}
		else{
			mfwSession::clear(self::SESKEY_URL_BEFORE_LOGIN);
		}
	}

	protected function redirectUrlBeforeLogin()
	{
		$url = mfwSession::get(self::SESKEY_URL_BEFORE_LOGIN);
		if(!$url){
			$url = '/';
		}
		return $this->redirect($url);
	}

	protected function clearUrlBeforelogin()
	{
		mfwSession::clear(self::SESKEY_URL_BEFORE_LOGIN);
	}

}
