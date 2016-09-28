<?php  
require_once('inc/Bootstrap.php');
$PageName = "contact";
$web_content = $oASimple->get_record_by_ID('tbl_web_content','content_id','9');	
$meta_title = $web_content['content_meta_title'];
$meta_keywords = $web_content['content_meta_keywords'];
$meta_description = $web_content['content_meta_description'];
$Error="";
if($_POST['raise_my_query'])
{
	foreach ($_POST as $key => $val) 
	{
		$data_in["params"][$key]=addslashes(trim($val));
	}
	if((strtoupper(trim($_SESSION['security_code']))!=strtoupper(trim($_POST['security_code_confirm']))) && trim($_SESSION['security_code'])!="" && 
	trim($_POST['security_code_confirm'])!="")
	{
		$Error='Incorrect security code! Please try again.';
	}
	else
	{
		if(!empty($data_in["params"]["property_slug"]))
		{
			$property_name=GetOneValue('tbl_property','property_slug',$data_in["params"]["property_slug"],'property_name');
			$mailcontent.='
			<table width="670" align="center" cellpadding="2" cellspacing="2" border="0" style="font-family:Arial, Helvetica, sans-serif;font-size:13px;">
			<tr>
				<td colspan="4">
					<table width="670" align="center" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="320" align="left" valign="middle"><img src="'.WEB_ROOT.'images/logo.png"></td>
						<td width="350" align="right" valign="top">
							<table width="350" align="center" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td align="right" style="color:#232D8C;">Inquiry for Property</td>
								</tr>
								<tr>
									<td align="right">'.$property_name.'</td>
								</tr>								
							</table>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="4">
				<p>
				Dear Admin,<br /><br>
				Please find inquiry for property name "'.$property_name.'".
				</td>
			</tr>
			<tr>        	
				<td width="134" style="color:#232D8C;">Contact Person</td>
				<td width="200" style="text-align:left;">'.$data_in["params"]["query_by_name"].'</td>
				<td width="134" style="color:#232D8C;">Email Address</td>
				<td width="200" style="color:#232D8C;">'.$data_in["params"]["query_by_email"].'</td>
			</tr>
			<tr>        	
				<td width="134" style="color:#232D8C;">Mobile Number</td>
				<td width="200" style="text-align:left;">'.$data_in["params"]["query_by_contact_no"].'</td>
				<td width="134">&nbsp;</td>
				<td width="200">&nbsp;</td>
			</tr>
			<tr>        	
				<td colspan="4" style="color:#232D8C;">Query Subject</td>
			</tr>
			<tr>        	
				<td colspan="4" style="text-align:left;">'.$data_in["params"]["query_by_subject"].'</td>
			</tr>
			<tr>        	
				<td colspan="4" style="color:#232D8C;">Query Description</td>
			</tr>
			<tr>        	
				<td colspan="4" style="text-align:left;">'.$data_in["params"]["query_by_description"].'</td>
			</tr>			
			<tr>
				<td colspan="4" align="left"><br />Thank you,<br />'.SITE_NAME_EMAIL.'</td>
			</tr>
			</table>
			';
			$setting_admin=$oASimple->get_record_by_ID('tbl_admin','admin_id',"1");			
			$to=$setting_admin['admin_email'];
			$subject="Inquiry for Property - ".SITE_NAME;	
			$from=no_reply_emailaddress;				
			SendHTMLMail($to,$subject,$mailcontent,$from);
			redirect(WEB_ROOT."contact/".$data_in["params"]["property_slug"]."/success/");
		}
		else
		{
			$query_insert_query= "INSERT INTO `tbl_contact_us` SET `query_by_name`='".$data_in["params"]["query_by_name"]."',
			`query_by_contact_no`='".$data_in["params"]["query_by_contact_no"]."',`query_by_email`='".$data_in["params"]["query_by_email"]."',
			`query_by_subject`='".$data_in["params"]["query_by_subject"]."',`query_by_description`='".$data_in["params"]["query_by_description"]."',
			`query_answered_by_admin`='',
			`query_on_date`=now(),is_query_answered='0'";	
			$oASimple->justSQLQueryExecuter($query_insert_query);					
			redirect(WEB_ROOT."contact/m/success/");
		}
	}
}
$office_address=$oASimple->get_record_by_ID('tbl_admin','admin_id',"1");
$get_lat_lon=get_latitude_longitude_by_zipcode($office_address['office_address_zipcode']);
$office_lat=$get_lat_lon['lat'];$office_lon=$get_lat_lon['lng'];


?>
<!DOCTYPE HTML>
<html>
<head>
<?php  require_once('header_inc.php'); ?>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
</head>
<body>
<!--header-->
<?php  require_once('header.php'); ?>
<!-- / header-->
<!-- middle start-->
<div class="container">
    <div class="row">
        <div class="search-listing-midpage container-fluid">
            <div class="mid-main">    
                <div class="account-main">
                    <div class="row">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                        <li><a href="<?php  echo WEB_ROOT; ?>">Home</a></li>
                        <li><i class="fa fa-arrow-right">&#xf105;</i></li>
                        <li><a href="javascript:;">Contact us</a></li>                
                        </ul>
                    </div>
                    <div class="col-md-1 alpha">&nbsp;</div>
                    <div class="col-sm-12">                                        
                    	<div class="main-title"><h1>Contact us</h1></div>
                    	<div class="col-md-7 col-xs-12">
                            <div class="well well-lg">
                                <div class="acc_rightcon">
                                    <form name="req_site_query" id="req_site_query" method="post" action="">
                                    <?php
									if(!empty($_REQUEST['property_slug']))
									{										
										$property_slug=$_REQUEST['property_slug'];										
										?>
                                        <input type="hidden" name="property_slug" id="property_slug" value="<?php echo $property_slug;?>">
                                        <?php
									}
									?>
                                    <ul>
                                    <li>
                                    		<div class="col-xs-12"><h2>Describe Your Query</h2></div>                                    	
                                    </li>
                                    <?php  
                                    if($_REQUEST['msg'] == 'success')
                                    {
                                    ?>
                                    <li>
                                        	<div class="col-xs-12 mid-text text-success">Your query is saved!! <br>You will be answered soon.</div>                                       
                                    </li>
                                    <?php 																		
									}
									if(!empty($Error))
                                    {
									?>
                                    <li>
                                        	<div class="col-xs-12 mid-text text-danger"><?php  echo $Error;?></div>                                       
                                    </li>
                                    <?php 
									} 
									?>
                                    <li>
                                       
                                        <div class="col-md-4"><span class="line-h">Contact Person Name</span></div>
                                        <div class="col-md-7"><input name="query_by_name" id="query_by_name" type="text" class="form-control" 
                                        value="<?php echo stripslashes($_POST['query_by_name']);?>"></div>
                                    	
                                    </li>                                    
                                    <li>
                                        
                                        <div class="col-md-4"><span class="line-h">Email Address</span></div>
                                        <div class="col-md-7"><input name="query_by_email" id="query_by_email" type="text" class="form-control" 
                                        value="<?php echo stripslashes($_POST['query_by_email']);?>"></div>
                                   
                                    </li>
                                    <li>
                                       
                                        <div class="col-md-4"><span class="line-h">Mobile No</span></div>
                                        <div class="col-md-7"><input name="query_by_contact_no" id="query_by_contact_no" type="text" class="form-control" 
                                        value="<?php echo stripslashes($_POST['query_by_contact_no']);?>"></div>
                                       
                                    </li>
                                    <li>
                                        
                                        <div class="col-md-4"><span class="line-h">Query Subject</span></div>
                                        <div class="col-md-7"><input name="query_by_subject" id="query_by_subject" type="text" class="form-control" 
                                        value="<?php echo stripslashes($_POST['query_by_subject']);?>"></div>
                                       
                                    </li>
                                    <li>
                                       
                                        <div class="col-md-4"><span class="line-h">Describe you Query</span></div>
                                        <div class="col-md-7"><textarea name="query_by_description" id="query_by_description" 
                                        class="form-textarea"><?php echo stripslashes($_POST['query_by_description']);?></textarea></div>
                                       
                                    </li>                                    
                                    <li>
                                       
                                        <div class="col-md-4"><span class="line-h">Security code</span></div>                                        
                                        <div class="col-md-7">
                                        <img id="imgCaptcha" src="<?php echo WEB_ROOT?>CaptchaSecurityImages.php?width=100&height=30&characters=5" />
                                        <a href="javascript:;" onclick="refreshCaptcha();"><img id="imgCaptcha" src="<?php echo WEB_ROOT?>images/refresh_captcha.png"/></a>
                                        <input name="security_code_confirm" id="security_code_confirm" type="text" class="form-control cont_capcha">
                                        </div>
                                        <!--<div class="col-md-4 captcha-fild pad-fl"></div>-->
                                       
                                    </li>                                    
                                    <li>
                                       
                                        <div class="clear"></div>
                                        <div class="col-xs-12 text-center btn-space"><input class="btn btn-default" type="submit" name="raise_my_query" 
                                        id="raise_my_query" value="Submit"></div>
                                        
                                    </li>
                                    </ul>
                                    </form>
                                </div>
                            </div>
                        </div>
                    	<div class="col-md-5 col-xs-12">
                        	<div class="address">
                        	<h2>Our Address</h2>
                            <ul class="con-ul">
                            <li><span class="icon"><i class="fa fa-map-marker"></i></span>
                            <span class="col-xs-11">
							<?php echo stripslashes($office_address['office_address_1']);?> <br> <?php echo stripslashes($office_address['office_address_2']);?>,
                            <?php echo stripslashes($office_address['office_address_city']);?>
                            <br><?php echo stripslashes($office_address['office_address_state']);?> <?php echo stripslashes($office_address['office_address_zipcode']);?><br>
                            <?php echo stripslashes($office_address['office_address_country']);?></span></li>
                            <li><span class="icon"><i class="fa fa-phone-square"></i></span><span class="col-xs-11"><?php echo stripslashes($office_address['office_phone']);?>
                            </span></li>
                            <li><span class="icon"><i class="fa fa-envelope"></i></span><span class="col-xs-11">
                            <a href="mailto:<?php echo stripslashes($office_address['office_address_email']);?>"><?php echo stripslashes($office_address['office_email']);?>
                            </a></span></li>
                            <li><span class="icon"><i class="fa fa-globe"></i></span><span class="col-xs-11"><a href="<?php  echo WEB_ROOT; ?>">
							<?php  echo WEB_ROOT; ?></a></span></li>
                            </ul>
                            </div>
                            <div class="map">                            	
                            	<h2>Where to find us.</h2>
                                <div id="map-canvas-address" style="width:100%;height:230px;"></div>
                            </div>
                        </div>
                        </div>                
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- / page middle-->
<!-- Footer part start -->
<?php  require_once('footer.php'); ?>
<!-- /Footer part start -->
<?php  require_once('footer_inc.php'); ?>
<script type="text/javascript">
var locations=[ ['<?php echo stripslashes($office_address['office_address_1']);?>,<?php if(!empty($office_address['office_address_2'])){echo "<br>".stripslashes($office_address['office_address_2']);}?>,<?php if(!empty($office_address['office_address_city'])){echo "<br>".stripslashes($office_address['office_address_city']);}?><?php if(!empty($office_address['office_address_city'])){echo "-".stripslashes($office_address['office_address_zipcode']);}?><?php if(!empty($office_address['office_address_state'])){echo "<br>".stripslashes($office_address['office_address_state'])." -INDIA";}?>', <?php echo $office_lat;?>, <?php echo $office_lon;?>] ];

var map = new google.maps.Map(document.getElementById('map-canvas-address'), {
  scrollwheel: false,
  zoomControl: false,
  navigationControl: false,
  mapTypeControl: false,
  scaleControl: false,
  draggable: true,
  zoom: 15,
  center: new google.maps.LatLng(<?php echo $office_lat;?>, <?php echo $office_lon;?>),
  mapTypeId: google.maps.MapTypeId.ROADMAP
});

var infowindow = new google.maps.InfoWindow();

var marker, i;

for (i = 0; i < locations.length; i++) { 
  marker = new google.maps.Marker({
	position: new google.maps.LatLng(locations[i][1], locations[i][2]),
	map: map
  });

  google.maps.event.addListener(marker, 'click', (function(marker, i) {
	return function() {
	  infowindow.setContent(locations[i][0]);
	  infowindow.open(map, marker);
	}
  })(marker, i));
}
function refreshCaptcha()
{
	document.getElementById("imgCaptcha").src = '<?php echo WEB_ROOT;?>CaptchaSecurityImages.php?width=100&height=30&characters=5&rand='+Math.random();	
}
</script>
</body>
</html>