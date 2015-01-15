<?php
require_once LIB_PATH . '/Plugin/Component.php';
require_once LIB_PATH . '/Extension/authentication/authentication.php';
require_once "lib/adLDAP/src/adLDAP.php";

class Plugins_Authentication_Auth_AuthComponent extends Plugins_Authentication{

    var $ldapEnable;
    var $ldapRecursiveGroups;
    var $ldapRealPrimarygroup;
    var $ldapSSO;
    var $ldapUseSSL;
    var $ldapUseTLS;
    var $ldapUserInGroup;
    var $ldapAdPort;
    var $ldapPrefix;
    var $ldapAccountSufix;
    var $ldapBaseDn;
    var $ldapDomainController;
    var $ldapAdminUsername;
    var $ldapAdminPassword;
    var $ldapDefaultLanguage;
    var $ldapDefaultAccountID;

    function Plugins_Authentication_Auth_AuthComponent(){
        $this->_loadSettings();
    }


    function _loadSettings(){
        $aConf = $GLOBALS['_MAX']['CONF'];
        $aAdLdapConf = $aConf['auth'];

        $this->ldapEnable           = !empty($aAdLdapConf['ldapEnable']);
        $this->ldapPrefix           = trim($aAdLdapConf['ldapPrefix']);
        $this->ldapAccountSufix     = trim($aAdLdapConf['ldapAccountSufix']);
        $this->ldapBaseDn           = trim($aAdLdapConf['ldapBaseDn']);
        $this->ldapDomainController = trim($aAdLdapConf['ldapDomainController']);
        $this->ldapAdminUsername    = trim($aAdLdapConf['ldapAdminUsername']);
        $this->ldapAdminPassword    = trim($aAdLdapConf['ldapAdminPassword']);
        $this->ldapAdPort           = trim($aAdLdapConf['ldapAdPort']);
        $this->ldapRealPrimarygroup = !empty($aAdLdapConf['ldapRealPrimarygroup']);
        $this->ldapRecursiveGroups  = !empty($aAdLdapConf['ldapRecursiveGroups']);
        $this->ldapUseSSL           = !empty($aAdLdapConf['ldapUseSSL']);
        $this->ldapUseTLS           = !empty($aAdLdapConf['ldapUseTLS']);
        $this->ldapSSO              = !empty($aAdLdapConf['ldapSSO']);
        $this->ldapUserInGroup      = trim($aAdLdapConf['ldapUserInGroup']);
        $this->ldapDefaultLanguage  = trim($aAdLdapConf['ldapDefaultLanguage']);
        $this->ldapDefaultAccountID  = trim($aAdLdapConf['ldapDefaultAccountID']);
    }

    function isAdapi($username){
        return $username == "adapi";
    }

    function checkPassword($username, $password){
        $username = strtolower($username);
        if(($this->isAdapi($username, $password)) || !$this->ldapEnable){
            return parent::checkPassword($username, $password);
        }else{
            $adLdapConnection  =  $this->authByLdap($username,$password);
            if($adLdapConnection){
                if(!($adLdapConnection->user()->inGroup($username,$this->ldapUserInGroup))){
                    return false;
                }
                $doUser = OA_Dal::factoryDO('users');
                $doUser->username = $username;
                $doUser->find();
                if($doUser->fetch()){
                    return $doUser;
                }else{
                    $newUser = $this->createAndSaveUserDo($adLdapConnection,$username,$password);
                    return $newUser;
                }
            }else{
                return false;
            }
        }
    }

    /**
    * Search data from Active Directory. Using LDAP
    * @param string $username
    * @param string $password
    * @return boolean true if connected
    */
    function authByLdap($username,$password){
        $param_adldap = array(
                'account_suffix'     => $this->ldapAccountSufix,
                'base_dn'            => $this->ldapBaseDn,
                'domain_controllers' => array($this->ldapDomainController),
                'real_primarygroup'  => $this->ldapRealPrimarygroup,
                'use_ssl'            => $this->ldapUseSSL,
                'use_tls'            => $this->ldapUseTLS,
                'recursive_groups'   => $this->ldapRecursiveGroups,
                'ad_port'            => $this->ldapAdPort,
                'sso'                => $this->ldapSSO
                );
        if(($this->ldapAdminUsername!= "") && ($this->ldapAdminPassword!="") ){

            $param_adldap["admin_username"] = $this->ldapPrefix."\\".$this->ldapAdminUsername;
            $param_adldap["admin_password"] = $this->ldapAdminPassword;
        }
        $adLdapConnection = new adLDAP($param_adldap);
        $connectionEstablished = $adLdapConnection->authenticate($this->ldapPrefix."\\".$username, $password);
        if($connectionEstablished){
            return $adLdapConnection;
        }
        return false;
    }

    function createAndSaveUserDo($adLdapConnection,$username,$password){
        $infoCollection = $adLdapConnection->user()->infoCollection($username,array("displayName","mail"));
        $user_id = $this->saveUserDo(
                OA_Dal::factoryDO('users'),
                $username,
                $password,
                $infoCollection->displayName,
                $infoCollection->mail,
                $this->ldapDefaultLanguage,
                $this->ldapDefaultAccountID
                );
        if($user_id){
            $doUser = OA_Dal::factoryDO('users');
            $doUser->user_id = $user_id;
            $doUser->find();
            OA_Permission::setAccountAccess($this->ldapDefaultAccountID,
                                            $user_id);
            OA_Permission::storeUserAccountsPermissions(array(),
                                                    $this->ldapDefaultAccountID,
                                                    $user_id,
                                                    array());
            return $doUser;
        }else{
            return false;
        }
    }
}

?>