<?php
include ("function.php");

$tripID = $_REQUEST["tripID"];
$eveID = $_REQUEST["eveID"];

$tripID = 320;
$eveID = 939;

if($tripID && $eveID)
{
	$conn = connectToDB();
	
	$toDay = date("Y-m-d H:i:s");
	
	$sql = "insert into EVE.dbo.T_CpCustTrip (CpCustID, CpTripID, CpLastUpdatedDate, CpCreationDate) VALUES (" . $eveID . "," . $tripID . ", '" . $toDay . "', '" . $toDay . "')";
	
	
}


?>

<html>
 <head>
  <title></title>
 </head>
 <body>
<form action="tilmelding.php" method="post" name="myForm">
<input type="hidden" value="<?php echo "" . substr($Dato,0,10) . ""; ?>" name="date" />
<input type="hidden" value="<?php echo "" . $Pris . ""; ?>" name="prisToDB" />
<table width="760" border="0" class="textInfo" cellspacing="0">
 <tr>
  <td>Navn</td>
  <td><input type="text" name="navn" value="<?php echo "" . getNameFromEVE($eveID) . ""; ?>"/></td>
  <td rowspan="5"><img src="<?php echo "" . $img . ""; ?>" />
 </tr>

 <tr>
  <td>Telefon</td>
  <td><input type="text" name="telefon" value="<?php echo "" . getMobileFromEVE($eveID) . ""; ?>" /></td>
 </tr>

 <tr>
  <td>Mødested</td>
  <td>
   <select name="sted">
    <option value="turSted">Ved turens mødested</option>
	<option value="butik">Ved Diving 2000</option>
  </select>
  </td>
 </tr>
  <tr>
  <td>Medlem af Odense Dykkerklub?</td>
  <td><?php echo "" . getMedlemsStatus($eveID) . ""; ?></td>
 </tr>

 <tr>
  <td><b><h3>Leje af udstyr</h3></b></td>
 </tr>
 
 
 <tr>
  <td style="border-bottom: 1px solid #000000;">Maske/snorkel</td>
  <td style="border-bottom: 1px solid #000000;"><input onchange="calc(15,10);" type="checkbox" value="1" name="maske" /></td>
  <td style="border-bottom: 1px solid #000000;">15 Kr. (10 kr.)</td>
 </tr>
 <tr>
  <td style="border-bottom: 1px solid #000000;">Tank inkl. luft</td>
  <td style="border-bottom: 1px solid #000000;"><input onchange="calc();" type="checkbox" value="1" name="tank" /></td>
  <td style="border-bottom: 1px solid #000000;">50 Kr. (0 kr.)</td>
 </tr>
 <tr>
  <td style="border-bottom: 1px solid #000000;">Finner</td>
  <td style="border-bottom: 1px solid #000000;">
   <select name="finner"> 
    <option value="intet">Vælg størrelse</option>
	<option value="S">S</option>
	<option value="M">M</option> 
	<option value="L">L</option> 
	<option value="XL">XL</option>
   </select> 
  </td>
  <td style="border-bottom: 1px solid #000000;">15 Kr. (10 kr.)</td>
 </tr>
 <tr>
   <td style="border-bottom: 1px solid #000000;">Dragt</td>
   <td style="border-bottom: 1px solid #000000;">
    <select name="dragt">
	 <option value="intet">Vælg størrelse</option>
	 <option value="160">160</option>
	 <option value="170">170</option>
	 <option value="180">180</option>
	 <option value="190">190</option> 
	 <option value="200">200</option>
	</select>
   </td>
   <td style="border-bottom: 1px solid #000000;">110 Kr. (80 kr.)</td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">Handsker</td>
   <td style="border-bottom: 1px solid #000000;">
    <select name="handsker">
	 <option value="intet">Vælg størrelse</option>
	<option value="S">S</option>
	<option value="M">M</option> 
	<option value="L">L</option> 
	<option value="XL">XL</option>
   </select> 
   </td>
   <td style="border-bottom: 1px solid #000000;">6 Kr. (5 kr.)</td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">Støvler</td>
   <td style="border-bottom: 1px solid #000000;">
    <select name="boots">
	 <option value="intet">Vælg størrelse</option>
	 <option value="36">36</option>
	 <option value="37">37</option>
	 <option value="38">38</option>
	 <option value="39">39</option>
	 <option value="40">40</option>
	 <option value="41">41</option>
	 <option value="42">42</option>
	 <option value="43">43</option>
	 <option value="44">44</option>
	 <option value="45">45</option>
	 <option value="46">46</option>
	</select>
   </td> 
   <td style="border-bottom: 1px solid #000000;">6 Kr. (5 kr.)</td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">Bly</td>
   <td style="border-bottom: 1px solid #000000;"><input onclick="checkChoice(this, 15, 10);" type="checkbox" value="1" name="bly"/></td>
   <td style="border-bottom: 1px solid #000000;">15 Kr. (10 kr.)</td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">Regulator</td>
   <td style="border-bottom: 1px solid #000000;"><input onclick="checkChoice(this, 120, 90);" type="checkbox" value="1" name="regulator"/></td>
   <td style="border-bottom: 1px solid #000000;">120 Kr. (90 kr.)</td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">BCD</td>
   <td style="border-bottom: 1px solid #000000;">
    <select name="bcd">
	 <option value="intet">Vælg størrelse</option>
     <option value="s">S</option>
	 <option value="m">M</option>
	 <option value="l">L</option>
	 <option value="xl">XL</option>
	</select>
   </td>
   <td style="border-bottom: 1px solid #000000;">120 Kr. (90 kr.)</td>
  </tr>
  
  <tr>
   <td colspan="4"><b>Del på Facebook:</b> <div class="fb-like" data-send="true" data-layout="button_count" data-width="200" data-show-faces="false" data-colorscheme="light"></div></td>
  </tr>
  <tr>
   <td colspan="2" align="left"><input type="hidden" name="tripID" value="<?php echo "" . $tripID . ""; ?>" /><input type="hidden" name="eveID" value="<?php echo "" . $eveID . ""; ?>" />
   <input type="submit" value="Tilmeld" /></td>
      
  </tr>
</table>

</form>


</body>
</html>