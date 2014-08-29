<?php

/**
 *
 */
class HtmlFormIphone
{
	/**
	 * HTML Select without label before
	 */
	static function Select($name, $list, $select = '', $withBlank = true, $otherAtr = '')
	{
		$taillemax = 17;
		if(is_array($list) and count($list) > 0)
		{
			$opt = ($withBlank) ? '<option value=""> </option>' : '';
			foreach($list as $k => $v)
			{
				$s = ($k == $select) ? ' selected="selected"' : '';
				$opt .= '<option value="'.$k.'"'.$s.'>'.$v.'</option>';
			}
		}
		$out = '<select name="'.$name.'"'.$otherAtr.'>'.$opt.'</select><br class="clear"/>';
		return $out;
	}

	/**
	 * HTML Select with Label before
	 */
	static function SelectLabel($name, $list, $select = '', $title = '', $withBlank = true, $otherAtr = '')
	{
		$title = ($title != '') ? $title : $name;
		$otherAtr .= ' style="margin-top:-20px" ';
		return '<label>'.$title.'</label>'.self::Select($name,$list,$select ,$withBlank,$otherAtr);
	}

	/**
	 * HTML Select with Label before
	 */
	static function Radio($name, $list, $select = '', $title = '', $withBlank = true, $otherAtr = '')
	{
		$title = ($title != '') ? $title : $name;
		if(is_array($list) and count($list) > 0)
		{
			$opt = ($withBlank) ? '<label><input type="radio" value="" name="'.$name.'"/>&nbsp;</label>' : '';
			foreach($list as $k => $v)
			{
				$s = ($k == $select) ? ' checked="checked"' : '';
				$opt .= '<label><input type="radio"'.$s.' value="'.$k.'" name="'.$name.'" '.$otherAtr.' /> '.$v.'</label>';
			}
		}
		return '<li class="iRadio">'.$title.$opt.'</li>';
	}


	/**
	 * HTML input without label before.
	 */
	static function Input($name, $value = '', $title = '',$otherAtr = '', $type = 'text')
	{
		$autocorrect = ($_SESSION['user']['config']['autocorrect'] != 'ok') ? ' autocorrect = "off" ' : ' autocorrect = "on" ';
		$majauto = ($_SESSION['user']['config']['autocapitalize'] != 'ok') ?' autocapitalize = "off" ' : ' autocapitalize = "on" ';
		$otherAtr	.= ($value != '') ? ' value="'.$value.'"' : '';
		$title 	 = ($title != '') ? $title : $name;
		$otherAtr .= ' onkeyup="montrerCroix(this, \'sans\');" ';
		$out = '<input class="InputCroix" type="'.$type.'" name="'.$name.'" placeholder="'.$title.'"'.$otherAtr.$autocorrect.$majauto.' />';
		$out .= '<img class="croix" src="'.getStaticUrl('img').'croix.png" alt="croix" onclick="croixEfface(this, \'sans\');" />';
		return $out;
	}


	/**
	 * HTML input with a label before
	 */
	static function InputLabel($name, $value = '', $title = '',$otherAtr = '', $type = 'text')
	{
		$autocorrect = ($_SESSION['user']['config']['autocorrect'] != 'ok') ? ' autocorrect = "off" ' : ' autocorrect = "on" ';
		$majauto = ($_SESSION['user']['config']['autocapitalize'] != 'ok') ?' autocapitalize = "off" ' : ' autocapitalize = "on" ';
		$otherAtr	.= ($value != '') ? ' value="'.$value.'"' : '';
		$title 	 = ($title != '') ? $title : $name;
		$otherAtr .= ' onkeyup="montrerCroix(this, \'label\');" ';
		$out = '<label>'.$title.'</label><input class="InputCroix" type="'.$type.'" name="'.$name.'"'.$otherAtr.$autocorrect.$majauto.' />';
		$out .= '<img class="croixLabel" src="'.getStaticUrl('img').'croix.png" alt="croix" onclick="croixEfface(this, \'label\');"/>';
		return $out;
	}
	static function InputLabelWnoku($name, $value = '', $title = '',$otherAtr = '', $type = 'text')
	{
		$otherAtr	.= ($value != '') ? ' value="'.$value.'"' : '';
		$title 	 = ($title != '') ? $title : $name;
		$out = '<label>'.$title.'</label><input type="'.$type.'" name="'.$name.'"'.$otherAtr.' autocorrect = "off" />';
		$out .= '<img class="croixLabel" src="'.getStaticUrl('img').'croix.png" alt="croix" onclick="croixEfface(this, \'label\');"/>';
		return $out;
	}
		static function InputWnoku($name, $value = '', $title = '',$otherAtr = '', $type = 'text')
	{
		$otherAtr	.= ($value != '') ? ' value="'.$value.'"' : '';
		$title 	 = ($title != '') ? $title : $name;
		$out = '<input type="'.$type.'" name="'.$name.'" placeholder="'.$title.'" '.$otherAtr.' autocorrect = "off" />';
		$out .= '<img class="croixLabel" src="'.getStaticUrl('img').'croix.png" alt="croix" onclick="croixEfface(this, \'label\');"/>';
		return $out;
	}


	/**
	 * HTML input with a label before
	 */
	static function Inputdate($name, $value = '', $dateFormat = '', $title = '',$otherAtr = '', $type = 'text')
	{
		$otherAtr	.= ($value != '') ? ' value="'.$value.'"' : '';
		$title 	 = ($title != '') ? $title : $name;
		$button_id   = substr(md5(time().rand(0,10000)),0,12);
		$dateFormat  = ($dateFormat != '') ? $dateFormat : '%d/%m/%Y';

		$out = '<label class="inputDate">'.$title.'</label>
			<div class="inputDate">
			<input type="'.$type.'" value="'.$value.'" id="'.$name.'ID" name="'.$name.'" class="inputDate"'.$otherAtr.'/>
			<a id="'.$button_id.'" title="Calendar" href="javascript:void" class="inputDate">
						<img alt="calendar" src="'.getStaticUrl('img').'calendar.png"/>
						</a>
						</div><br class="clear"/>
			<script> Calendar.setup({inputField     :    "'.$name.'ID",
				dateFormat       :    "'.$dateFormat.'",
				trigger         :    "'.$button_id.'",
				animation		: false
				});
				</script>';
		return $out;
	}

	/**
	 * HTML input with a label before
	 */
	static function Checkbox($name, $titre = '',$choice = '', $value = '',$otherAtr = '')
	{
		$id 		 = 'id'.$name.rand(0,10000);
		$titre 	 = ($titre != '') ? $titre : 'choix';
		$choice 	 = ($choice != '') ? $choice : 'OUI|NON';
		$otherAtr 	.= ($value == 'ok' or $value == '1') ? ' checked="checked"' : ' ';
		$out = '<input type="checkbox" name="'.$name.'" id="'.$id.'" title="'.$choice.'" value="ok" class="iToggle"'.$otherAtr.'/><label for="'.$id.'">'.$titre.'</label>';
		return $out;
	}

	/**
	 * HTML textarea without label before.
	 */
	static function Textarea($name, $value = '', $otherAtr = '')
	{
		$out = '<textarea name="'.$name.'"'.$otherAtr.'>'.$value.'</textarea><br class="clear"/>';
		return $out;
	}


	/**
	 * HTML textarea with a label before
	 */
	static function TextareaLabel($name, $value = '', $otherAtr = '',$title = '')
	{
		$title 	 = ($title != '') ? $title : $name;
		$out = '<label>'.$title.'</label>'.self::Textarea($name,$value,$otherAtr);
		return $out;
	}

}

?>
