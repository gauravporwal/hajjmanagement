<?php 
include("../common/functions.php");
include('../html2pdf/html2pdf.class.php');
$path = BASEURL.'image_content/carriers/carriers_images_th/';
$db = new Database();
$current_val=0;
$start =0;
$nor = 20;
$limit = $nor;

if(isset($_GET['val']))
{
	$current_val=$_GET['val'];
	$start = ($nor*$current_val);	
	$limit = ($current_val+1)*$nor;
}
$run_disp = $db->query("SELECT SQL_CALC_FOUND_ROWS DISTINCT(passport_no) FROM custom_eticket WHERE createdby='".$_SESSION['uid']."' ORDER BY id DESC LIMIT $start, $nor");
$num_disp = $db->total();
$run_total = $db->query("SELECT FOUND_ROWS() num;");
$row_total = $db->fetch($run_total);
$total_val = $row_total['num'];

$nop = ceil($total_val/$nor);
if(isset($_GET['val']))
{
	$current_val=$_GET['val'];
	$start = ($nor*$current_val);	
	$limit = ($current_val+1)*$nor;
}

$show_disp = array();
while($row_disp = $db->fetch($run_disp))
{
	$show_disp[] = $row_disp;
}

if(count($show_disp)==0)
{
	exit();	
}


$content ='';
$html2pdf = new HTML2PDF('P','','en');
//$html2pdf->setDefaultFont('dejavusansbi');
//print_r($show_disp);
//exit();
foreach($show_disp as $val=>$key ) {
	
	
			
		$run_flightdate = $db->query("SELECT * FROM custom_eticket WHERE passport_no='".$key['passport_no']."' ORDER BY flight_date ASC");
		while($row_flightdate = $db->fetch($run_flightdate))
		{
			$data[] = $row_flightdate;
			$date_man = explode('-',$row_flightdate['flight_date']);
			$date_month = $month_num[$date_man[1]];
			if(count($date_man[2])==2){			
				$date_year = '20'.$date_man[2];
			}else{
				$date_year = $date_man[2];
			}
			$flightdatedetailscopy[] = $date_year.'-'.$date_month.'-'.$date_man[0];
			$flightnodetailscopy[] = $row_flightdate['flight_no'];
			
		}
		$way = $db->total();
		
		if($way==2)
		{

			if(strtotime($flightdatedetailscopy[0])>strtotime($flightdatedetailscopy[1]))
			{
				$flightdatedetails[0] = $flightdatedetailscopy[1];
				$flightdatedetails[1] = $flightdatedetailscopy[0];
				$flightnodetails[0] = $flightnodetailscopy[1];
				$flightnodetails[1] = $flightnodetailscopy[0];
//				echo 'first is greator';	
			}else
			{
				$flightdatedetails[0] = $flightdatedetailscopy[0];
				$flightdatedetails[1] = $flightdatedetailscopy[1];
				$flightnodetails[0] = $flightnodetailscopy[0];
				$flightnodetails[1] = $flightnodetailscopy[1];
//				echo 'second is greator';	
			}
			
//		print_r($flightdatedetails);
//		print_r($flightnodetails);
			
		}else{
				$flightdatedetails[0] = $flightdatedetailscopy[0];
				$flightnodetails[0] = $flightnodetailscopy[0];			
		}
		
		$run_flightinfo1 = $db->query("SELECT * FROM flights LEFT JOIN carriers ON carriers.carriers_id=flights.agency_id WHERE flight_no='".$flightnodetails[0]."' AND date1='".$flightdatedetails[0]."'");
		$get_flightinfo1 = $db->fetch($run_flightinfo1);		
?><?php		

	if($data[0]['ticket_id']=='')
	{
		$data[0]['ticket_id'] ='NA';	
	}
	if($get_flightinfo1['carriers_logo']=='')
	{
		$logo ='';	
	}else{
		
		$logo = '<img src="'.$path.$get_flightinfo1['carriers_id'].'.'.$get_flightinfo1['carriers_logo'].'" width="60" height="60"/>';
	}
    $content .= '<page backtop="10mm" backleft="20mm" orientation="paysage" format="80x230" backbottom="0mm" >';		
	$content .='<table width="700" border="0" cellspacing="2" cellpadding="2">
    <tr>
      <td height="25" colspan="4" align="center">&nbsp;</td>
      <td colspan="2"><strong>NAME</strong>&nbsp;&nbsp;'.$data[0]['title'].' '.$data[0]['first_name'].' <br /> '.$data[0]['last_name'].'</td>
    </tr>
    <tr>
      <td colspan="4" rowspan="2"><strong>NAME</strong>&nbsp;&nbsp;'.$data[0]['title'].' '.$data[0]['first_name'].' '.$data[0]['last_name'].'</td>
    </tr>
    <tr>
      <td height="22" colspan="2" ><strong>FROM</strong>&nbsp;&nbsp;'.getflightlocationname($get_flightinfo1['source']).'</td>
    </tr>
    <tr>
      <td colspan="3" width="270"><strong>FROM</strong>&nbsp;&nbsp;
           '. getflightlocationname($get_flightinfo1['source']).'   </td>
      <td width="270"><strong>TO</strong>&nbsp;&nbsp;
          '.getflightlocationname($get_flightinfo1['destination']).'  </td>
      <td colspan="2">
         <strong>TO</strong>&nbsp;&nbsp; 
         '. getflightlocationname($get_flightinfo1['destination']).'
      </td>
    </tr>
    <tr>
      <td colspan="3"><strong>TICKET NO</strong> &nbsp;&nbsp;'.$data[0]['bp_ticketid'].'</td>  
      <td><strong>PASSPORT NO.</strong>&nbsp;&nbsp;'.$data[0]['passport_no'].'</td>
      <td colspan="2" ><strong>TICKET NO</strong>&nbsp;&nbsp; '.$data[0]['bp_ticketid'].'</td>
	  </tr>
    <tr>
      <td colspan="3"></td>
      <td><strong>TIME</strong>&nbsp;&nbsp;'. $get_flightinfo1['time1'].'
      
      
      </td>
      <td colspan="2"><strong>PASSPORT NO</strong>&nbsp;&nbsp; '. $data[0]['passport_no'].'</td>
      </tr>
    <tr>
      <td><strong>FLIGHT NO</strong>&nbsp;&nbsp;'.$get_flightinfo1['flight_no'].'</td>
      <td colspan="2">&nbsp;</td>
      <td><strong>DATE</strong>&nbsp;&nbsp;'. changeDate($get_flightinfo1['date1']).'</td>
      <td colspan="2"><strong>FLIGHT NO</strong>&nbsp;&nbsp;'.$get_flightinfo1['flight_no'].'</td>
      </tr>
    <tr>
      <td width="164"><strong>GATE NO.</strong>&nbsp;&nbsp;'. $get_flightinfo1['gateno'].' </td>
      <td colspan="2">&nbsp;</td>
      <td rowspan="2" align="left" valign="bottom"><barcode value="'.$data[0]['ticket_id'].'" ></barcode></td>
      <td colspan="2"><strong>DATE</strong>&nbsp;&nbsp;'. changeDate($get_flightinfo1['date1']).'</td>
      </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td width="77" valign="center"><strong>GATE NO.</strong>&nbsp;&nbsp;'. $get_flightinfo1['gateno'].'</td>
      <td width="81" valign="center" ><strong>TIME</strong>&nbsp;&nbsp;'.$get_flightinfo1['time1'].'</td>
      </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>';		
	$content .="</page>";
	if($way ==2){
		$run_flightinfo2 = $db->query("SELECT * FROM flights LEFT JOIN carriers ON carriers.carriers_id=flights.agency_id WHERE flight_no='".$flightnodetails[1]."' AND date1='".$flightdatedetails[1]."'");
		$get_flightinfo2 = $db->fetch($run_flightinfo2);
	$content .='<page  backtop="10mm" backleft="20mm" orientation="paysage" format="80x230" backbottom="0mm" >';
				
//	$content .='<barcode type="C39E" value="gaurav porwal from lucknow" label="none"></barcode>';	
//	$content .="<barcode type=\"EAN13\" value='45' label=\"label\" style=\"width:30mm; height:6mm; color: #770000; font-size: 4mm\"></barcode>";	
	$content .='<table width="700" border="0" cellspacing="2" cellpadding="2">
    <tr>
      <td height="25" colspan="4" align="center">&nbsp;</td>
      <td colspan="2"><strong>NAME</strong>&nbsp;&nbsp;'.$data[0]['title'].' '.$data[0]['first_name'].' <br /> '.$data[0]['last_name'].'</td>
    </tr>
    <tr>
      <td colspan="4" rowspan="2"><strong>NAME</strong>&nbsp;&nbsp;'.$data[0]['title'].' '.$data[0]['first_name'].' '.$data[0]['last_name'].'</td>
    </tr>
    <tr>
      <td height="22" colspan="2" ><strong>FROM</strong>&nbsp;&nbsp;'.getflightlocationname($get_flightinfo2['source']).'</td>
    </tr>
    <tr>
      <td colspan="3" width="270"><strong>FROM</strong>&nbsp;&nbsp;
           '. getflightlocationname($get_flightinfo2['source']).'   </td>
      <td width="270"><strong>TO</strong>&nbsp;&nbsp;
          '.getflightlocationname($get_flightinfo2['destination']).'  </td>
      <td colspan="2">
         <strong>TO</strong>&nbsp;&nbsp; 
         '. getflightlocationname($get_flightinfo2['destination']).'
      </td>
    </tr>
    <tr>
      <td colspan="3"><strong>TICKET NO</strong> &nbsp;&nbsp;'.$data[0]['bp_ticketid'].'</td>  
      <td><strong>PASSPORT NO.</strong>&nbsp;&nbsp;'.$data[0]['passport_no'].'</td>
      <td colspan="2" ><strong>TICKET NO</strong>&nbsp;&nbsp; '.$data[0]['bp_ticketid'].'</td>
	  </tr>
    <tr>
      <td colspan="3"></td>
      <td><strong>TIME</strong>&nbsp;&nbsp;'. $get_flightinfo2['time1'].'
      
      
      </td>
      <td colspan="2"><strong>PASSPORT NO</strong>&nbsp;&nbsp; '. $data[0]['passport_no'].'</td>
      </tr>
    <tr>
      <td><strong>FLIGHT NO</strong>&nbsp;&nbsp;'.$get_flightinfo2['flight_no'].'</td>
      <td colspan="2">&nbsp;</td>
      <td><strong>DATE</strong>&nbsp;&nbsp;'. changeDate($get_flightinfo2['date1']).'</td>
      <td colspan="2"><strong>FLIGHT NO</strong>&nbsp;&nbsp;'.$get_flightinfo2['flight_no'].'</td>
      </tr>
    <tr>
      <td width="164"><strong>GATE NO.</strong>&nbsp;&nbsp;'. $get_flightinfo2['gateno'].' </td>
      <td colspan="2">&nbsp;</td>
      <td rowspan="2" align="left" valign="bottom"><barcode value="'.$data[0]['ticket_id'].'" ></barcode></td>
      <td colspan="2"><strong>DATE</strong>&nbsp;&nbsp;'. changeDate($get_flightinfo2['date1']).'</td>
      </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td width="77" valign="center"><strong>GATE NO.</strong>&nbsp;&nbsp;'. $get_flightinfo2['gateno'].'</td>
      <td width="81" valign="center" ><strong>TIME</strong>&nbsp;&nbsp;'.$get_flightinfo2['time1'].'</td>
      </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>';
	$content .="</page>";
	
	}
			


unset($data);	 unset($flightdatedetailscopy); unset($flightdatedetails); unset($flightnodetails); unset($flightnodetailscopy);	
  
    


}
$html2pdf->WriteHTML($content);	
$html2pdf->Output('manalairservice.pdf');

?>
