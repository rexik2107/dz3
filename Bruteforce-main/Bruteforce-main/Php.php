<?php

if( isset( $_GET[ 'Login' ] ) ) {
	// Get username
	$user = ($_GET[ 'username' ]);

	// Get password
	$pass = ($_GET[ 'password' ]);

	// Выполняем запрос в БД: найти пользователя с никнеймом $user
	$query  = "SELECT * FROM `users` WHERE user = '$user';";
	$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query );

	if( $result && mysqli_num_rows( $result ) == 1 ) {
		// Get users details
		$row    = mysqli_fetch_assoc( $result );
		$account_locked = False; 
		$last_login = strtotime( $row["last_login"] ); 
		if ($row["failed_login"] >= 5) { 
			if (time() < $last_login + (5 * 60)) 
				$account_locked = True; 
		}

    if (!$account_locked){
      $query  = "SELECT * FROM `forbidden_passws` WHERE password='$pass';";
      $result = mysqli_query($GLOBALS["___mysqli_ston"],  $query );
      if( $result && mysqli_num_rows( $result ) == 1 ) {
        $account_locked = True; 
        $query  = "UPDATE `users` SET failed_login = 5, last_login = now() WHERE user = '$user';";
        $result = mysqli_query($GLOBALS["___mysqli_ston"],  $query );
      }
    }

		if ($row["password"] == md5( $pass ) && !$account_locked){
			$avatar = $row["avatar"];

			// Login successful
			$html .= "<p>Welcome to the password protected area {$user}</p>";
			$html .= "<img src=\"{$avatar}\" />";
			$query  = "UPDATE `users` SET failed_login=0, last_login = now() WHERE user = '$user';";
			$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query );
		}
		
		else{
			// Login failed
		  if ($account_locked)
				$html .= "<pre><br />Account locked.</pre>";
			else
				$html .= "<pre><br />Username and/or password incorrect.</pre>";
			$query  = "UPDATE `users` SET failed_login = (failed_login + 1), last_login = now() WHERE user = '$user';";
			$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query );
		}
	}
	else {
		$html .= "<pre><br />Username and/or password incorrect.</pre>";
	}

	((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res); // закрывает соединение
}

?>
