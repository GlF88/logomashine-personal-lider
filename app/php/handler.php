<?php

function CRMExchange( $url, $params = null )
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_ENCODING ,"");
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function LeadMail( $from, $server, $to, $subject, $message )
{
	$header="Date: ".date("D, j M Y G:i:s")." +0300\r\n";
	$header.="From: =?utf-8?Q?".str_replace("+","_",str_replace("%","=",urlencode(''.$from["name"].'')))."?= <".$from["e-mail"].">\r\n";
	$header.="Reply-To: =?utf-8?Q?".str_replace("+","_",str_replace("%","=",urlencode(''.$from["name"].'')))."?= <".$from["e-mail"].">\r\n";
	$header.="X-Priority: 3 (Normal)\r\n";
	$header.="Message-ID: <172562218.".date("YmjHis")."@".$server["name"].">\r\n";
	$header.="To: <".$to.">\r\n";
	$header.="Subject: =?utf-8?Q?".str_replace("+","_",str_replace("%","=",urlencode($subject)))."?=\r\n";
	$header.="MIME-Version: 1.0\r\n";
	$header.="Content-Type: text/html; charset=utf-8\r\n";
	$header.="Content-Transfer-Encoding: 8bit\r\n";

	$smtp_conn = fsockopen($server["name"], 25,$errno, $errstr, 10);
	$data = get_data($smtp_conn);

	fputs($smtp_conn,"EHLO server\r\n");
	$data = get_data($smtp_conn);

	fputs($smtp_conn,"AUTH LOGIN\r\n");
	$data = get_data($smtp_conn);

	fputs($smtp_conn,base64_encode($server["login"])."\r\n");
	$data = get_data($smtp_conn);

	fputs($smtp_conn,base64_encode($server["password"])."\r\n");
	$data = get_data($smtp_conn);

	fputs($smtp_conn,"MAIL FROM:".$from["e-mail"]."\r\n");
	$data = get_data($smtp_conn);

	fputs($smtp_conn,"RCPT TO:".$to."\r\n");
	$data = get_data($smtp_conn);

	fputs($smtp_conn,"DATA\r\n");
	$data = get_data($smtp_conn);

	fputs($smtp_conn,$header."\r\n".$message."\r\n.\r\n");
	$data = get_data($smtp_conn);

	$code = substr($data,0,3);

	fputs($smtp_conn,"QUIT\r\n");
	$data = get_data($smtp_conn);

	$answer = ($code == 250) ? 1 : 0;
	return $answer;
}

function SetSubject( $action )
{
	$action = (!empty($action)) ? $action : "default";

	$subject = array(
			 "customer" => "Интерес к аренде персонала",
			 "worker"   => "Отклик на вакансию",
			 "default"  => "Заявка с сайта"
			);
	$result = (!empty($subject[$action])) ? $subject[$action] : $subject["default"];
	return $result;
}

function Mailer( $data )
{
	if ($data->action == "worker") {
		$to = "vacancy@mkcgarant-staff.ru";
	} else {
		$to = "client@mkcgarant-staff.ru";
	}
	$from = array("name"=>"Гарант","e-mail"=>"site@mkcgarant-staff.ru");
	$server = array("name"=>"smtp.timeweb.ru", "login"=>"site@mkcgarant-staff.ru", "password"=>"9BZgT8rU");

	$logo = array("url"=>"http://mkcgarant-staff.ru/img/logo.png", "size"=>array("width"=>133, "height"=>44));

	$subject = SetSubject($data->action);

	$dopInfo = "
	<table>
		<tbody>
			<tr>
				<tr><td style='padding:5px;'><p><strong>Возраст: </strong>".$data->workerAge."</p><td></tr>
			</tr>
		</tbody>
	</table>
		<p>Информация о работнике занесена в CRM-систему.</p>
		<p>Для уточнения данных свяжитесь с работником<br>и занесите полученные сведения в карточку работника.</p>
	";

	$dopInfo = ($data->action == "worker") ? $dopInfo : "";

	$message =  "
    			<html>
    				<head>
    					<meta charset='utf-8'>
    					<title>".$subject."</title>
    				</head>
    				<body>
    					<table>
        					<tbody>
         						<tr>
                						<td style='padding:5px;' colspan=2><strong>".$subject."</strong></td>
            						</tr>
												<tr>
                						<tr><td style='padding:5px;'><strong>Телефон</strong></td>
                						<td style='padding:5px;'>".$data->telEmail."</td></tr>
														<tr><td style='padding:5px;'><strong>ФИО</strong></td>
                						<td style='padding:5px;'>".$data->customerName." ".$data->workerFamily." ".$data->workerName." ".$data->workerSurname."</td></tr>
            						</tr>
        					</tbody>
    					</table>
            				".$dopInfo."
            				<img style='margin-top:10px; border:none;' src='".$logo["url"]."' width='".$logo["width"]."' height='".$logo["height"]."'>
    				</body>
    			</html>
		";

	return LeadMail($from, $server, $to, $subject, $message);
}

function get_data($smtp_conn)
{
  $data="";
  while($str = fgets($smtp_conn,515))
  {
    $data .= $str;
    if(substr($str,3,1) == " ") { break; }
  }
  return $data;
}

/*интеграционный ключ*/

$key = "1f97511fe6068d71f98c0d08f03d4e8ab4a76c86";

/*********************/

	$data = json_decode(file_get_contents('php://input'));

	if(!empty($data))
	{
		$result = Mailer($data);
		echo $result;
	}
	/*После установки на хостинг раскоментить*/
	$params = json_encode(array("key"=>$key, "action"=>$data->action, "tel"=>$data->tel, "age"=>$data->workerAge, "object_city"=>$data->workerAddress, "citizenship"=>$data->citizenship, "name"=>$data->workerName, "surname"=>$data->workerSurname, "gender"=>$data->workerGender, "family"=>$data->workerFamily, "sanitary_card"=>$data->sanitary_card, "job_night"=>$data->job_night, "weekend"=>$data->holiday_work, "worker_data_creat"=>$data->worker_data_creat, "worker_update_date"=>$data->worker_update_date));
	$result = CRMExchange("http://crm.yastaff.ru/server/inter/inter.php", $params);
?>
