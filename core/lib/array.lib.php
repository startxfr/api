<?php
/*#########################################################################
#
#   name :       array.inc
#   desc :       library for array management
#   categorie :  core module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


if (!function_exists('array_chunk')) {
    function array_chunk( $input, $size, $preserve_keys = false) {
	@reset( $input );
	$i = $j = 0;
	while( @list( $key, $value ) = @each( $input ) ) {
	    if(!(isset($chunks[$i])))
		$chunks[$i] = array();
	    if( count( $chunks[$i] ) < $size ) {
		if( $preserve_keys ) {
		    $chunks[$i][$key] = $value;
		    $j++;
		}
		else  $chunks[$i][] = $value;
	    }
	    else {
		$i++;
		if( $preserve_keys ) {
		    $chunks[$i][$key] = $value;
		    $j++;
		}
		else {
		    $j = 0;
		    $chunks[$i][$j] = $value;
		}
	    }
	}
	return $chunks;
    }
}


function array_chunk_vertical($input, $size, $preserve_keys = false, $size_is_horizontal = true) {
    $chunks = array();
    if ($size_is_horizontal)
	$chunk_count = ceil(count($input) / $size);
    else  $chunk_count = $size;

    for ($chunk_index = 0; $chunk_index < $chunk_count; $chunk_index++)
	$chunks[] = array();

    $chunk_index = 0;
    foreach ($input as $key => $value) {
	if ($preserve_keys)
	    $chunks[$chunk_index][$key] = $value;
	else  $chunks[$chunk_index][] = $value;

	if (++$chunk_index == $chunk_count)
	    $chunk_index = 0;
    }
    return $chunks;
}


//call them with array_walk for e.g.
//array_walk ($array, 'trim_array');
function trim_array(&$array, $key) {
    if(is_array($array))
	array_walk($array,'trim_array');
    else  $array = trim($array);
}

function stripslashs($value) {
    $value = is_array($value) ?
	    array_map('stripslashs', $value) :
	    stripslashes($value);
    return $value;
}

?>
