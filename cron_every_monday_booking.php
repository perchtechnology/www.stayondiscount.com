<?php
require_once('inc/Bootstrap.php');

$check_monday=0;
if(date('D', time()) === 'Mon') 
	$check_monday=1;
else
	$check_monday=0;	

$currentDate=date("Y-m-d",time());
$day_after_current_date=date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));

$query_booking_paid="SELECT * FROM `tbl_booking_master` WHERE payment_status='1' AND cancelled='0' AND deleted='0'";
$data_booking_paid=$oASimple->getSelectMultipleRecordSet($query_booking_paid);
if(count($data_booking_paid) > 0)
{
	foreach($data_booking_paid as $data_booking_paid_details)
	{
		$booking_id=$data_booking_paid_details['booking_id'];		
		$query_booking_detail="SELECT * FROM `tbl_booking_detail` WHERE booking_master_id='".$booking_id."' AND '".$currentDate."' <= booking_from_date 
		AND booking_status='CO' AND cancelled='0' AND deleted='0'";
		
		$data_booking_detail=$oASimple->getSelectMultipleRecordSet($query_booking_detail);
		if(count($data_booking_detail) > 0)
		{
			foreach($data_booking_detail as $data_booking_detail_details)
			{		
				$booking_from_date=$data_booking_detail_details['booking_from_date'];
				$booking_to_date=$data_booking_detail_details['booking_to_date'];
				$booking_guest_id=$data_booking_paid_details['booking_guest_id'];
				$booking_guest_details=$oASimple->get_record_by_ID('tbl_guest_user','user_id',$booking_guest_id,"*");				
				$one_day_before_booking_date=date('Y-m-d', strtotime('-1 day', strtotime($booking_from_date)));
				//CHECKING A DAY BEFORE CHECKIN DATE START				
				if($currentDate == $one_day_before_booking_date)
				{
					//EMAIL TO PROPERTY OWNER STARTS					
					$property_name=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_name');
					$property_type_id=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_type_id');		
					$property_type_name=GetOneValue('tbl_property_type','type_id',$property_type_id,'type_name');
					$property_address_1=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_address_1');
					$property_address_2=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_address_2');
					$property_city_id=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_city_id');		
					$property_city_name=GetOneValue('tbl_cities','city_id',$property_city_id,'city_name');
					$propert_state=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'propert_state');
					$property_postal_code=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_postal_code');		
					$property_checkin_time=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_checkin_time');		
					$property_checkout_time=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_checkout_time');		
					$property_location="";
					if(!empty($property_address_1)){$property_location.=$property_address_1;}
					if(!empty($property_address_2)){$property_location.=", ".$property_address_2;}
					if(!empty($property_city_name)){$property_location.=", ".$property_city_name;}
					if(!empty($property_postal_code)){$property_location.=", ".$property_postal_code;}
					if(!empty($propert_state)){$property_location.=", ".$propert_state;}
					//PROPERTY OWNER DETAILS
					$property_owner_id=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_owner_id');
					$property_owner_name=GetOneValue('tbl_property_owner','owner_id',$property_owner_id,'owner_name');
					$property_owner_email=GetOneValue('tbl_property_owner','owner_id',$property_owner_id,'owner_email');
					$property_owner_phone=GetOneValue('tbl_property_owner','owner_id',$property_owner_id,'owner_phone');
					//PROPERTY OWNER DETAILS
					$mailcontent.="
					<table width=\"670\" align=\"center\" cellpadding=\"2\" cellspacing=\"2\" border=\"0\" style=\"font-family:Arial, Helvetica, sans-serif;font-size:13px;\">
					<tr>
						<td colspan=\"4\">
							<table width=\"670\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
							<tr>
								<td width=\"320\" align=\"left\" valign=\"middle\"><img src=\"".WEB_ROOT."images/logo.png\"></td>
								<td width=\"350\" align=\"right\" valign=\"top\">
									<table width=\"350\" align=\"center\" cellpadding=\"2\" cellspacing=\"2\" border=\"0\">
										<tr>
											<td align=\"right\" style=\"color:#232D8C;\"><strong>YOUR UPCOMING BOOKINGS</strong></td>
										</tr>							
									</table>
								</td>
							</tr>
							<tr>
								<td colspan=\"2\">&nbsp;</td>
							</tr>
							<tr>
								<td colspan=\"2\">Hello ".$booking_guest_details["user_name"].",<br>
								Just a small reminder of your upcoming trip with ".$property_name."
								</td>
							</tr>
							<tr>
								<td colspan=\"2\">&nbsp;</td>
							</tr>
							<tr>
								<td colspan=\"2\">
								<p>								
								Please find your booking schedule for <strong>".$property_name."(".$property_type_name.")</strong>.<br><br>
								<h3 style=\"color:#232D8C;\">ADDRESS | CONTACT DETAILS</h3>".$property_location."-INDIA<br><br>
								<strong>Contact Person:</strong><br>
								".$property_owner_name."<br>
								".$property_owner_email."<br>
								".$property_owner_phone."
								</p>
								</td>
							</tr>
							<tr>
								<td colspan=\"2\">&nbsp;</td>
							</tr>
							<tr>
								<td colspan=\"2\">
									<table width=\"670\" align=\"center\" cellpadding=\"2\" cellspacing=\"2\" border=\"0\">
										<tr>
											<td width=\"250\">Guest Name</td>
											<td width=\"420\">".$booking_guest_details["user_name"]."</td>
										</tr>
										<tr>
											<td width=\"250\">Checkin</td>
											<td width=\"420\">".convDateDMY($booking_from_date)."</td>
										</tr>
										<tr>
											<td width=\"250\">Checkout</td>
											<td width=\"420\">".convDateDMY($booking_to_date)."</td>
										</tr>
										<tr>
											<td width=\"250\">Checkin Time</td>
											<td width=\"420\">".$property_checkin_time."</td>
										</tr>
										<tr>
											<td width=\"250\">Checkout Time</td>
											<td width=\"420\">".$property_checkout_time."</td>
										</tr>							                   
									</table>
								</td>
							</tr>
							<tr>
								<td colspan=\"2\">&nbsp;</td>
							</tr>
							<tr>
								<td colspan=\"2\">In case of anything, please do not hesitate to contact us!</td>
							</tr>
							<tr>
								<td colspan=\"2\">&nbsp;</td>
							</tr>
							<tr>
								<td colspan=\"2\">Best regards,<br />".SITE_NAME_EMAIL."</td>
							</tr>
							</table>
						</td>
					</tr>
					</table>
					";			
					$subject = "Your upcoming trip with ".$property_name." - ".SITE_NAME;
					SendHTMLMail($booking_guest_details["user_email"],$subject,$mailcontent,no_reply_emailaddress);
					//EMAIL TO PROPERTY OWNER ENDS
					echo "Email raised Day Before";
				}
				//CHECKING A DAY BEFORE CHECKIN DATE ENDS
				//CHECKING CURRENT DATE IS GRATER THEN BOOKING FROMDATE AND IS IT MONDAY START
				if($currentDate >= $booking_from_date && $check_monday==1)				
				{
					//EMAIL TO PROPERTY OWNER STARTS					
					$property_name=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_name');
					$property_type_id=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_type_id');		
					$property_type_name=GetOneValue('tbl_property_type','type_id',$property_type_id,'type_name');
					$property_address_1=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_address_1');
					$property_address_2=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_address_2');
					$property_city_id=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_city_id');		
					$property_city_name=GetOneValue('tbl_cities','city_id',$property_city_id,'city_name');
					$propert_state=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'propert_state');
					$property_postal_code=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_postal_code');		
					$property_checkin_time=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_checkin_time');		
					$property_checkout_time=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_checkout_time');		
					$property_location="";
					if(!empty($property_address_1)){$property_location.=$property_address_1;}
					if(!empty($property_address_2)){$property_location.=", ".$property_address_2;}
					if(!empty($property_city_name)){$property_location.=", ".$property_city_name;}
					if(!empty($property_postal_code)){$property_location.=", ".$property_postal_code;}
					if(!empty($propert_state)){$property_location.=", ".$propert_state;}
					//PROPERTY OWNER DETAILS
					$property_owner_id=GetOneValue('tbl_property','property_id',$data_booking_paid_details['booking_property_id'],'property_owner_id');
					$property_owner_name=GetOneValue('tbl_property_owner','owner_id',$property_owner_id,'owner_name');
					$property_owner_email=GetOneValue('tbl_property_owner','owner_id',$property_owner_id,'owner_email');
					$property_owner_phone=GetOneValue('tbl_property_owner','owner_id',$property_owner_id,'owner_phone');
					//PROPERTY OWNER DETAILS
					$mailcontent.="
					<table width=\"670\" align=\"center\" cellpadding=\"2\" cellspacing=\"2\" border=\"0\" style=\"font-family:Arial, Helvetica, sans-serif;font-size:13px;\">
					<tr>
						<td colspan=\"4\">
							<table width=\"670\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
							<tr>
								<td width=\"320\" align=\"left\" valign=\"middle\"><img src=\"".WEB_ROOT."images/logo.png\"></td>
								<td width=\"350\" align=\"right\" valign=\"top\">
									<table width=\"350\" align=\"center\" cellpadding=\"2\" cellspacing=\"2\" border=\"0\">
										<tr>
											<td align=\"right\" style=\"color:#232D8C;\"><strong>YOUR UPCOMING BOOKINGS</strong></td>
										</tr>							
									</table>
								</td>
							</tr>
							<tr>
								<td colspan=\"2\">&nbsp;</td>
							</tr>
							<tr>
								<td colspan=\"2\">Hello ".$booking_guest_details["user_name"].",<br>
								Just a small reminder of your upcoming bookings.
								</td>
							</tr>
							<tr>
								<td colspan=\"2\">&nbsp;</td>
							</tr>
							<tr>
								<td colspan=\"2\">
								<p>								
								Please find your booking schedule for <strong>".$property_name."(".$property_type_name.")</strong>.<br><br>
								<h3 style=\"color:#232D8C;\">ADDRESS | CONTACT DETAILS</h3>".$property_location."-INDIA<br><br>
								<strong>Contact Person:</strong><br>
								".$property_owner_name."<br>
								".$property_owner_email."<br>
								".$property_owner_phone."
								</p>
								</td>
							</tr>
							<tr>
								<td colspan=\"2\">&nbsp;</td>
							</tr>
							<tr>
								<td colspan=\"2\">
									<table width=\"670\" align=\"center\" cellpadding=\"2\" cellspacing=\"2\" border=\"0\">
										<tr>
											<td width=\"250\">Guest Name</td>
											<td width=\"420\">".$booking_guest_details["user_name"]."</td>
										</tr>
										<tr>
											<td width=\"250\">Checkin</td>
											<td width=\"420\">".convDateDMY($booking_from_date)."</td>
										</tr>
										<tr>
											<td width=\"250\">Checkout</td>
											<td width=\"420\">".convDateDMY($booking_to_date)."</td>
										</tr>
										<tr>
											<td width=\"250\">Checkin Time</td>
											<td width=\"420\">".$property_checkin_time."</td>
										</tr>
										<tr>
											<td width=\"250\">Checkout Time</td>
											<td width=\"420\">".$property_checkout_time."</td>
										</tr>							                   
									</table>
								</td>
							</tr>
							<tr>
								<td colspan=\"2\">&nbsp;</td>
							</tr>
							<tr>
								<td colspan=\"2\">Please contact us in case of any issues.</td>
							</tr>
							<tr>
								<td colspan=\"2\">&nbsp;</td>
							</tr>
							<tr>
								<td colspan=\"2\">Best regards,<br />".SITE_NAME_EMAIL."</td>
							</tr>
							</table>
						</td>
					</tr>
					</table>
					";			
					$subject = "Your upcoming bookings - ".SITE_NAME;									
					SendHTMLMail($booking_guest_details["user_email"],$subject,$mailcontent,no_reply_emailaddress);
					//EMAIL TO PROPERTY OWNER ENDS
					echo "Email raised on Monday";
				}
				//CHECKING CURRENT DATE IS GRATER THEN BOOKING FROMDATE AND IS IT MONDAY ENDS
			}
		}
	}
}
?>