#LDAP AUTH AS PLUGING FOR REVIVE ADSERVER
*Version 1.0.0*

Written by Karen Mikaela, email: karen.mikaela@gmail.com

##ABOUT
LdapAuth is a Revive (Adserver) plugin that provides authentication with Active Directory. It is a contribution for Revive Community.

##REQUERIMENTS
Based on adLDAP Libary, ldapAuth requires PHP 5 and both the LDAP (http://php.net/ldap) and SSL (http://php.net/openssl) libraries.
Compatible with revive version 3.0.5 >=.

## HOW TO INSTALL A PLUGIN IN REVIVE ADSERVER
The first step is to pack our plugin, just follow the steps below.
Clone this repo and zip the contents
´´´shell
$ git clone https://github.com/karen-mikaela/ldap-auth.git ldapAuth
$ cd ldapAuth
$ zip -r ldapAuth.zip plugins  
´´´
Now to install a new plugin in your Revive Adserver installation, you need to be logged in as an Administrator to be able to install new plugins.
Follow the next steps below.

* 1. Click the “Plugins” tab to open an overview of all plugins currently installed in your Revive Adserver installation.
* 2. Just below the “Install new plugin” label, click the “Browse…” button and navigate to the folder on your computer where you stored the zip file.
* 3. Once you have found and selected the file, you will be back in the Plugins screen.
* 4. Now click the “Install” button next to it.
* 5. The system will now check and install the new plugin. This usually takes only a few seconds.
* 6. Once the installation of the plugin is complete, the screen will refresh and you will see the new plugin appearing in the overview.

##SET UP
LdapAuth supports that you configure with your  ldap parameters by  Plugin Settings section.

The parameters are
* {ldapEnable}
* {ldapRecursiveGroups}
* {ldapRealPrimarygroup}
* {ldapSSO}
* {ldapUseSSL}
* {ldapUseTLS}
* {ldapUserInGroup}
* {ldapAdPort}
* {ldapPrefix}
* {ldapAccountSufix}
* {ldapBaseDn}
* {ldapDomainController}
* {ldapAdminUsername}
* {ldapAdminPassword}
* {ldapDefaultLanguage}
* {ldapDefaultAccountID}

## HOW CREATE MY AUTHENTICATION PLUGING
Visit [My Auth Plugin](https://github.com/rhapsodyv/revive-plugins-doc/blob/master/tutorial/authentication-my-auth.md) to see more documentation about how you can start your own plugin

## TODO

* Make enhancements for change the type authentication
* Error Handling








