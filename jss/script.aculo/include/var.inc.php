<?php 

function FormatVar ($array)
	{
	if (is_array($array))
		{
		foreach ($array as $key => $elem)
			{
			if (!is_array($elem))
				{
				$array[$key] = trim(stripslashs(ereg_replace("\"","&quot;",$elem)));
				}
			else
				{
				$array[$key] = FormatVar ($elem);
				}
			global ${$key};
			${$key} = $array[$key];
			}
		}
	return $array;
	}

FormatVar($_GET);
FormatVar($_POST);

?>