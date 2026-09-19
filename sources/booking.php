<?php 
	if(!defined('SOURCES')) die("Error");

	if(!empty($_POST['submit-booking']))
	{
        $responseCaptcha = $_POST['recaptcha_response_booking'];
        $resultCaptcha = $func->checkRecaptcha($responseCaptcha);
        $scoreCaptcha = (!empty($resultCaptcha['score'])) ? $resultCaptcha['score'] : 0;
        $actionCaptcha = (!empty($resultCaptcha['action'])) ? $resultCaptcha['action'] : '';
        $testCaptcha = (!empty($resultCaptcha['test'])) ? $resultCaptcha['test'] : false;
        $dataBooking = (!empty($_POST['dataBooking'])) ? $_POST['dataBooking'] : null;

        /* Valid data */
		if(empty($dataBooking['fullname']))
		{
			$response['messages'][] = hotenkhongduoctrong;
		}

		if(empty($dataBooking['phone']))
		{
			$response['messages'][] = sodienthoaikhongduoctrong;
		}

		if(!empty($dataBooking['phone']) && !$func->isPhone($dataBooking['phone']))
		{
			$response['messages'][] = sodienthoaikhonghople;
		}

		if(empty($dataBooking['content']))
		{
			$response['messages'][] = noidungkhongduoctrong;
		}

		if(!empty($response))
		{
			/* Flash data */
			if(!empty($dataBooking))
			{
				foreach($dataBooking as $k => $v)
				{
					if(!empty($v))
					{
						$flash->set($k, $v);
					}
				}
			}

			/* Errors */
			$response['status'] = 'danger';
			$message = base64_encode(json_encode($response));
			$flash->set("message", $message);
			$func->redirect($_POST['url-current']);
		}

		/* Save data */
        if(($scoreCaptcha >= 0.5 && $actionCaptcha == 'Booking') || $testCaptcha == true)
		{
			$data = array();
			$data['fullname'] = htmlspecialchars($dataBooking['fullname']);
			$data['phone'] = htmlspecialchars($dataBooking['phone']);
			$data['subject'] = htmlspecialchars($dataBooking['subject']);
			$data['content'] = htmlspecialchars($dataBooking['content']);
			$data['ngaykham'] = htmlspecialchars($dataBooking['ngay']."-".$dataBooking['gio']);
		    $data['date_created'] = time(); 
		    $data['type'] = "booking";
		    $data['numb'] = 1;

		    if($d->insert('newsletter',$data))
            {
				$id_insert = $d->getLastInsertId();

				if($func->hasFile("file_attach"))
				{
					$fileUpdate = array();
					$file_name = $func->uploadName($_FILES['file_attach']["name"]);

					if($file_attach = $func->uploadImage("file_attach", '.doc|.docx|.pdf|.rar|.zip|.ppt|.pptx|.DOC|.DOCX|.PDF|.RAR|.ZIP|.PPT|.PPTX|.xls|.xlsx|.jpg|.png|.gif|.JPG|.PNG|.GIF', UPLOAD_FILE_L, $file_name))
					{
						$fileUpdate['file_attach'] = $file_attach;
						$d->where('id', $id_insert);
						$d->update('contact', $fileUpdate);
						unset($fileUpdate);
					}
				}
            }
            else
            {
                $func->transfer( datlichhenthatbai , $_POST['url-current'], false);
            }
			
		    /* Gán giá trị gửi email */
		    $strThongtin = '';
		    $emailer->set('tennguoigui',$data['fullname']);
		    $emailer->set('emailnguoigui',"no-email@gmail.com");
		    $emailer->set('dienthoainguoigui',$data['phone']);
		    $emailer->set('diachinguoigui',"");
		    $emailer->set('tieudelienhe',"Đặt lich hẹn");
		    $emailer->set('noidunglienhe',$data['content']);
		    if($emailer->get('tennguoigui'))
		    {
		    	$strThongtin .= '<span style="text-transform:capitalize">'.$emailer->get('tennguoigui').'</span><br>';
		    }
		    if($emailer->get('emailnguoigui'))
		    {
		    	$strThongtin .= '<a href="mailto:'.$emailer->get('emailnguoigui').'" target="_blank">'.$emailer->get('emailnguoigui').'</a><br>';
		    }
		    if($emailer->get('diachinguoigui'))
		    {
		    	$strThongtin .= ''.$emailer->get('diachinguoigui').'<br>';
		    }
		    if($emailer->get('dienthoainguoigui'))
		    {
		    	$strThongtin .= 'Tel: '.$emailer->get('dienthoainguoigui').'';
		    }
		    $emailer->set('thongtin',$strThongtin);

		    /* Defaults attributes email */
		    $emailDefaultAttrs = $emailer->defaultAttrs();

		    /* Variables email */
		    $emailVars = array(
		    	'{emailTitleSender}',
		    	'{emailInfoSender}',
		    	'{emailSubjectSender}',
		    	'{emailContentSender}'
		    );
		    $emailVars = $emailer->addAttrs($emailVars, $emailDefaultAttrs['vars']);

		    /* Values email */
		    $emailVals = array(
		    	$emailer->get('tennguoigui'),
		    	$emailer->get('thongtin'),
		    	$emailer->get('tieudelienhe'),
		    	$emailer->get('noidunglienhe')
		    );
		    $emailVals = $emailer->addAttrs($emailVals, $emailDefaultAttrs['vals']);

			/* Send email admin */
			$arrayEmail = null;
			$subject = "Thư liên hệ từ ".$setting['name'.$lang];
			$message = str_replace($emailVars, $emailVals, $emailer->markdown('contact/admin'));
			$file = 'file_attach';

			if($emailer->send("admin", $arrayEmail, $subject, $message, $file))
			{
				/* Send email customer */
				$arrayEmail = array(
					"dataEmail" => array(
						"name" => $emailer->get('tennguoigui'),
						"email" => $emailer->get('emailnguoigui')
					)
				);
				$subject = "Thư liên hệ từ ".$setting['name'.$lang];
				$message = str_replace($emailVars, $emailVals, $emailer->markdown('contact/customer'));
				$file = 'file_attach';

				if($emailer->send("customer", $arrayEmail, $subject, $message, $file)) $func->transfer(datlichthanhcong, $_POST['url-current']);
			}
			else $func->transfer( datlichhenthatbai, $_POST['url-current'], false);
		}
		else
		{
			$func->transfer(datlichhenthatbaivuilongthulaisauvaiphut, $_POST['url-current'], false);
		}
	}
?>