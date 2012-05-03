<?PHP

include_once 'init.php';
include_once 'function/ldap.func.php';

// TODO: use readline
function _read_input($tip = 'input: ')
{
	if (PHP_OS == 'WINNT') {
	  echo $tip;
	  $line = stream_get_line(STDIN, 1024, PHP_EOL);
	} else {
	  $line = readline($tip);
	}
	return $line;
}
//$line = _read_input();
//var_dump($line);

isset($argv) || $argv = $_SERVER['argv'];
if(!isset($argv[1])) {
	echo "Usage: ", $argv[0], " username", PHP_EOL;
	return;
}

// connect to ldap server
$ldapconn = ldap_connect("team.eben.cn")
    or die("Could not connect to LDAP server.");

if ($ldapconn) {

	// Set some ldap options for talking to
	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

	$username = $argv[1];
	// using ldap bind
	$ldaprdn  = sprintf('uid=%s,ou=people,dc=eben,dc=net', $username);     // ldap rdn or dn
	$ldapbind = null;
	
	$i = 0;
	do {
		$password = _read_input('current password: ');
		$i ++;
	
	    // binding to ldap server
	    $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $password);
	    // verify binding
	    if ($ldapbind) {
			echo "LDAP bind successful...", PHP_EOL;
			break;
		} else {
			echo "LDAP bind failed...", PHP_EOL;
			continue;
	    }
	} while ( $i < 6 && ! $ldapbind);
	
	$i = 0;
	do {
		$new_password = _read_input('new password: ');
		$i ++;
		if (strlen($new_password) < 8) {
			echo 'too short, try again.'
			continue;
		}
		if ($ldapbind) {
			$userdata = array('userPassword' => password_hash($new_password, 'sha'));
			if (ldap_modify($ldapconn, $ldaprdn, $userdata)) {
				echo 'Change password successful', PHP_EOL;
				break;
			} else {
				echo 'Change password failed', PHP_EOl;
				break;
			}
		
	    }
	} while ( $i < 6);

}

