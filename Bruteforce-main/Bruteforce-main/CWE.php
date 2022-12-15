<?php
if( isset( $_GET[ 'Login' ] ) ) {
	$user = $_GET[ 'username' ];
	$pass = $_GET[ 'password' ];

	// CWE-327: Use of a Broken or Risky Cryptographic Algorithm
	// Использование сломанного или рискованного криптографического алгоритма (MD5), необходимо использовать SHA256
	$pass = md5( $pass ); 

	// CWE-116: необходимо экранировать входные данные
	// CWE-307: неправильное огранечение чрезмерных попыток аутентификации
	// необходимо ограничить количесто запросов 
	$query  = "SELECT * FROM `users` WHERE user = '$user' AND password = '$pass';"; 

	
	// CWE-307: неправильное огранечение чрезмерных попыток аутентификации
	// необходимо ограничить количесто запросов
	$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>' );
	#CWE-799 Неправильный контроль частоты взаимодействия
	//необходимо ограничить многократные запросы для предотвращения DOS-атак, например использовать sleep
	if( $result && mysqli_num_rows( $result ) == 1 ) {
		// Get users details
		$row    = mysqli_fetch_assoc( $result );
		$avatar = $row["avatar"];
		// Login successful

		// CWE-79: Failure to Preserve Web Page Structure ('Cross-site Scripting')
		// Неспособность сохранить структуру веб-страницы («Межсайтовый скриптинг»)

		$html .= "<p>Welcome to the password protected area {$user}</p>";
		$html .= "<img src=\"{$avatar}\" />";
	}
	else {
		// Login failed
		$html .= "<pre><br />Username and/or password incorrect.</pre>";
	}
	((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
}
?>
