


function zuno.ajax.get.html(url,param,target) {
	var targetAjah  = target;
	var myAjax		= new Ajax.Request(
		url, { method: 'get', parameters: param, onComplete: function(xhr)
			{
			    if (xhr.status == 200)
			         $(targetAjah).innerHTML = xhr.responseText;
			    else $(targetAjah).innerHTML = xhr.status;
			}
		}
	);
}
// use zuno.ajax.get.html('myscript.php','foo=bar','output-div-id');



function zuno.ajax.post.html(url,param,target) {
	var targetAjah  = target;
	var myAjax		= new Ajax.Request(
		url, { method: 'post', postBody: param, onComplete: function(xhr)
			{
					if (xhr.status == 200)
			         $(targetAjah).innerHTML = xhr.responseText;
			    else $(targetAjah).innerHTML = xhr.status;
			}
		}
	);
}
// use zuno.ajax.post.html('myscript.php','foo=bar','output-div-id');




function zuno.ajax.get.json(url,param,callback) {
	var myAjax = new Ajax.Request( url, { method: 'get', parameters: param, onComplete: callback } );
	return true;
}
// use zuno.ajax.get.json('myscript.php','foo=bar',function (xhr,json) { ... });



function zuno.ajax.post.json(url,param,callback) {
	var myAjax = new Ajax.Request( url, { method: 'post', parameters: param, onLoading: function(x,e) { document.body.style.cursor = 'wait'; }, onComplete: callback, onException: function (x,e) { alert("Erreur AjajPost :"+e); } } );
	return true;
}
// use zuno.ajax.post.json('myscript.php','foo=bar',function (xhr,json) { ... });