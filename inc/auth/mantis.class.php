<?php
/**
 * Mantis auth backend
 *
 * Uses external Trust mechanism to check against Mantis'
 * user cookie.
 * 13/12/2009 ~ @Tiago Gomes:
 * added modifications to make possible to set project related user rights.(http://www.mantisbt.org/bugs/view.php?id=8249)
 * added modifications so that mantis support logging in/out from DokuWiki's login page (http://www.mantisbt.org/bugs/view.php?id=8277)
 *
 * @author    Victor Boctor (http://www.futureware.biz)
 *
 */
require_once( MANTIS_ROOT . 'core.php' );

#dbg($GLOBALS);

class auth_mantis extends auth_basic {

	/**
	 * Constructor.
	 *
	 * Sets additional capabilities and config strings
	 */
	function auth_mantis() {
		$this->cando['external'] = true;
		$this->cando['logoff'] = true;
	}

	/**
	 * Authenticates the user using Mantis APIs.
	 * @param   string  $user    Username
	 * @param   string  $pass    Cleartext Password
	 * @param   bool    $sticky  Cookie should not expire
	 * @return  bool             true on successful auth
	 */
	function trustExternal( $user, $pass, $sticky = false ) {
		global $USERINFO;
		global $conf;

		$ValidUser = false;

		// Manage HTTP authentication with Negotiate protocol enabled
		$user = auth_prepare_username( $user );
		$pass = auth_prepare_password( $pass );

		// This is necessary in all cases where Authorization HTTP header is always set
		if( auth_is_user_authenticated() ) {
			$user = '';
		}

		// Has a user name been provided?
		if( !empty( $user ) ) {
			// User name provided, so login via form in progress...
			// Are the specified user name and password valid?
			if( auth_attempt_login( $user, $pass, $sticky ) ) {
				// Credential accepted...
				$_SERVER['REMOTE_USER'] = $user; // Set the user name (makes things work...)
				$ValidUser = true; // Report success.
			}
			else {
				// Invalid credentials
				if( !$silent ) {
					msg( $lang [ 'badlogin' ], -1 );
				}

				$ValidUser = false;
			}
		}
		else {
			// No user name provided.
			// Is a user already logged in?
			if(  auth_is_user_authenticated() ) {
				// Yes, a user is logged in, so set the globals...

				$t_user_id = auth_get_current_user_id();

				$USERINFO = $this->_loadUserData( $t_user_id );
				$_SERVER[ 'REMOTE_USER' ] = user_get_field( $t_user_id, 'username' );

				$ValidUser = true;
			}
			else {
				$ValidUser = false;
			}
		}

		// Is there a valid user login?
		if( !$ValidUser ) {
			// No, so make sure any existing authentication is revoked.
			auth_logoff ( );
		}

		return $ValidUser;
	}
	/**
	 * Logout from Mantis
	 */
	function logOff() {
		auth_logout();
	}

	/**
	 * Return user info
	 *
	 * @param string $user username
	 * @return array|false if user does not exist
	 */
	function getUserData( $user ) {
		return $this->_loadUserData( user_get_id_by_name( $user ) );
	}

	/**
	 * Retrieves user information from MantisBT
	 * @param int $p_user_id
	 * @return array|false if user does not exist
	 */
	private function _loadUserData( $p_user_id ) {
		if( !user_exists( $p_user_id ) ) {
			return false;
		}

		// is it a media display or a page?
		if( isset( $_REQUEST['media'] ) ) {
			//media
			$t_project_name = explode( ':', getNS( getID( "media", false ) ) );
		}
		else {
			// normal page
			$t_project_name = explode( ':', getNS( getID() ) );
		}

		$t_project_id = project_get_id_by_name( $t_project_name[1] );
		$t_access_level = access_get_project_level( $t_project_id );
		$t_access_level_string = strtoupper( MantisEnum::getLabel( config_get( 'access_levels_enum_string' ),  $t_access_level ) );
		$t_access_level_string_ex = strtoupper( $t_project_name[1] ) . '_' . $t_access_level_string;

		return array(
			'name' => user_get_name( $p_user_id ),
			'pass' => user_get_field( $p_user_id, 'password' ),
			'mail' => user_get_field( $p_user_id, 'email' ),
			'grps' => array( $t_access_level_string, $t_access_level_string_ex ),
		);
	}
}
