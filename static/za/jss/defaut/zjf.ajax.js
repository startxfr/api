


zuno.ajax.get.html = function(url,param,target) {
    var targetAjah  = target;
    var myAjax		= new Ajax.Request(
    url, { method: 'get', parameters: param, onComplete: function(xhr)
	{
	    if (xhr.status === 200)
		$(targetAjah).innerHTML = xhr.responseText;
	    else $(targetAjah).innerHTML = xhr.status;
	}
    }
);
};
// use zuno.ajax.get.html('myscript.php','foo=bar','output-div-id');



zuno.ajax.post.html = function(url,param,target) {
    var targetAjah  = target;
    var myAjax		= new Ajax.Request(
    url, { method: 'post', postBody: param, onComplete: function(xhr)
	{
	    if (xhr.status === 200)
		$(targetAjah).innerHTML = xhr.responseText;
	    else $(targetAjah).innerHTML = xhr.status;
	}
    }
);
};
// use zuno.ajax.post.html('myscript.php','foo=bar','output-div-id');




zuno.ajax.get.json = function(url,param,callback) {
    var myAjax = new Ajax.Request( url, { method: 'get', parameters: param, onComplete: callback } );
    return true;
};
// use zuno.ajax.get.json('myscript.php','foo=bar',function (xhr,json) { ... });



zuno.ajax.post.json = function(url,param,callback) {
    var myAjax = new Ajax.Request( url, { method: 'post', parameters: param, onLoading: function(x,e) { document.body.style.cursor = 'wait'; }, onComplete: callback, onException: function (x,e) { alert("Erreur AjajPost :"+e); } } );
    return true;
};
// use zuno.ajax.post.json('myscript.php','foo=bar',function (xhr,json) { ... });