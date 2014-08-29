<? 

include("barcode.inc");		   
include("i25object.inc");
include("c39object.inc");
include("c128aobject.inc");
include("c128bobject.inc");
include("c128cobject.inc");

/* Default value */
if (!isset($_POST['output']))  $_POST['output']   = "png"; 
if (!isset($_POST['barcode'])) $_POST['barcode']  = "0123456789";
if (!isset($_POST['type']))    $_POST['type']     = "I25";
if (!isset($_POST['width']))   $_POST['width']    = "460";
if (!isset($_POST['height']))  $_POST['height']   = "120";
if (!isset($_POST['xres']))    $_POST['xres']     = "2";
if (!isset($_POST['font']))    $_POST['font']     = "5";
/*********************************/ 

if (isset($_POST['barcode']) && strlen($_POST['barcode'])>0) {
    $_POST['style']  = BCS_ALIGN_CENTER;
    $_POST['style'] |= ($_POST['output']  == "png" ) ? BCS_IMAGE_PNG  : 0;
    $_POST['style'] |= ($_POST['output']  == "jpeg") ? BCS_IMAGE_JPEG : 0;
    $_POST['style'] |= ($_POST['border']  == "on"  ) ? BCS_BORDER 	  : 0;
    $_POST['style'] |= ($_POST['drawtext']== "on"  ) ? BCS_DRAW_TEXT  : 0;
    $_POST['style'] |= ($_POST['stretchtext']== "on" ) ? BCS_STRETCH_TEXT  : 0;
    $_POST['style'] |= ($_POST['negative']== "on"  ) ? BCS_REVERSE_COLOR  : 0;

    switch ($_POST['type']) {
	case "I25":
	    $obj = new I25Object(250, 120, $_POST['style'], $_POST['barcode']);
	    break;
	case "C39":
	    $obj = new C39Object(250, 120, $_POST['style'], $_POST['barcode']);
	    break;
	case "C128A":
	    $obj = new C128AObject(250, 120, $_POST['style'], $_POST['barcode']);
	    break;
	case "C128B":
	    $obj = new C128BObject(250, 120, $_POST['style'], $_POST['barcode']);
	    break;
	case "C128C":
	    $obj = new C128CObject(250, 120, $_POST['style'], $_POST['barcode']);
	    break;
	default:
	    $obj = false;
    }
    if ($obj) {
	if ($obj->DrawObject($_POST['xres'])) {
	    echo "<table align='center'><tr><td><img src='./image.php?code=".$_POST['barcode']."&style=".$_POST['style']."&type=".$_POST['type']."&width=".$_POST['width']."&height=".$_POST['height']."&xres=".$_POST['xres']."&font=".$_POST['font']."'></td></tr></table>";
	} else echo "<table align='center'><tr><td><font color='#FF0000'>".($obj->GetError())."</font></td></tr></table>";
    }
}
?>
<br>
<form method="post" action="sample.php">
    <table align="center" border="1" cellpadding="1" cellspacing="1">
	<tr>
	    <td bgcolor="#EFEFEF"><b>Type</b></td>
	    <td><select name="type" style="WIDTH: 260px" size="1">
		    <option value="I25" <?=($_POST['type']=="I25" ? "selected" : " ")?>>Interleaved 2 of 5
		    <option value="C39" <?=($_POST['type']=="C39" ? "selected" : " ")?>>Code 39
		    <option value="C128A" <?=($_POST['type']=="C128A" ? "selected" : " ")?>>Code 128-A
		    <option value="C128B" <?=($_POST['type']=="C128B" ? "selected" : " ")?>>Code 128-B
		    <option value="C128C" <?=($_POST['type']=="C128C" ? "selected" : " ")?>>Code 128-C</select></td>
	</tr>
	<tr>
	    <td bgcolor="#EFEFEF"><b>Output</b></td>
	    <td><select name="output" style="WIDTH: 260px" size="1">
		    <option value="png" <?=($_POST['output']=="png" ? "selected" : " ")?>>Portable Network Graphics (PNG)
		    <option value="jpeg" <?=($_POST['output']=="jpeg" ? "selected" : " ")?>>Joint Photographic Experts Group(JPEG)</select></td>
	</tr>
	<tr>
	    <td rowspan="4" bgcolor="#EFEFEF"><b>Styles</b></td>
	    <td rowspan="1"><input type="Checkbox" name="border" <?=($_POST['border']=="on" ? "CHECKED" : " ")?>>Draw border</td>
	</tr>
	<tr>
	    <td><input type="Checkbox" name="drawtext" <?=($_POST['drawtext']=="on" ? "CHECKED" : " ")?>>Draw value text</td>
	</tr>
	<tr>
	    <td><input type="Checkbox" name="stretchtext" <?=($_POST['stretchtext']=="on" ? "CHECKED" : " ")?>>Stretch text</td>
	</tr>
	<tr>
	    <td><input type="Checkbox" name="negative" <?=($_POST['negative']=="on" ? "CHECKED" : " ")?>>Negative (White on black)</td>
	</tr>
	<tr>
	    <td rowspan="2" bgcolor="#EFEFEF"><b>Size</b></td>
	    <td rowspan="1">Width: <input type="text" size="6" maxlength="3" name="width" value="<?=$_POST['width']?>"></td>
	</tr>
	<tr>
	    <td>Height: <input type="text" size="6" maxlength="3" name="height" value="<?=$_POST['height']?>"></td>
	</tr>
	<tr>
	    <td bgcolor="#EFEFEF"><b>Xres</b></td>
	    <td>
		<input type="Radio" name="xres" value="1" <?=($_POST['xres']=="1" ? "CHECKED" : " ")?>>1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="Radio" name="xres" value="2" <?=($_POST['xres']=="2" ? "CHECKED" : " ")?>>2&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="Radio" name="xres" value="3" <?=($_POST['xres']=="3" ? "CHECKED" : " ")?>>3&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	    </td>
	</tr>
	<tr>
	    <td bgcolor="#EFEFEF"><b>Text Font</b></td>
	    <td>
		<input type="Radio" name="font" value="1" <?=($_POST['font']=="1" ? "CHECKED" : " ")?>>1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="Radio" name="font" value="2" <?=($_POST['font']=="2" ? "CHECKED" : " ")?>>2&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="Radio" name="font" value="3" <?=($_POST['font']=="3" ? "CHECKED" : " ")?>>3&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="Radio" name="font" value="4" <?=($_POST['font']=="4" ? "CHECKED" : " ")?>>4&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="Radio" name="font" value="5" <?=($_POST['font']=="5" ? "CHECKED" : " ")?>>5&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	    </td>
	</tr>
	<tr>
	    <td bgcolor="#EFEFEF"><b>Value</b></td>
	    <td><input type="Text" size="24" name="barcode" style="WIDTH: 260px" value="<?=$_POST['barcode']?>"></td>
	</tr>
	<tr>
	</tr>
	<tr>
	    <td colspan="2" align="center"><input type="Submit" name="Submit" value="Show"></td>
	</tr>
    </table>
</form>
</body>
</html>
