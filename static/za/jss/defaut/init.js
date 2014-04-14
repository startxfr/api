
var zunoJsf = {
    Version: '0.2',
    REQUIRED_SCRIPTACULOUS: '1.8.2',
    STATICIMGURL: 'http://localhost/zunodev/static/za/img/',

    load: function(suffixPath) {
	function convertVersionString(versionString) {
	    var v = versionString.replace(/_.*|\./g, '');
	    v = parseInt(v + '0'.times(4-v.length));
	    return versionString.indexOf('_') > -1 ? v-1 : v;
	}

	if((typeof Scriptaculous=='undefined') ||
	    (typeof Element == 'undefined') ||
	    (typeof Element.Methods=='undefined') ||
	    (convertVersionString(Scriptaculous.Version) <
		convertVersionString(zunoJsf.REQUIRED_SCRIPTACULOUS)))
	    throw("zunoJsf requires the script.aculo.us JavaScript framework >= " +
		zunoJsf.REQUIRED_SCRIPTACULOUS);
    }
};

function autoCompleteHidden(text, li) {
    $(text.id+'hidden').value = li.title;
}

/**
 * Affiche l'arborescence d'une structure Javascript
 * @param arr Le tableau à afficher
 * @param level Le niveau auquel s'arrêter
 * @return  chaine de charactères avec la représentation de l'élément fournit
*/
function print_r(arr,level)
{
    var dumped_text = "";
    if(!level) level = 0;

    //The padding given at the beginning of the line.
    var level_padding = "";
    for(var j=0;j<level+1;j++) level_padding += "    ";

    if(typeof(arr) == 'object') { //Array/Hashes/Objects
	for(var item in arr) {
	    var value = arr[item];
	    if(typeof(value) == 'object') { //If it is an array,
		if(typeof(item) == 'string')
		    dumped_text += level_padding + "['" + item + "'] => ("+ typeof(value) + ") \n";
		else
		    dumped_text += level_padding + "[" + item + "] => ("+ typeof(value) + ") \n";
		dumped_text += level_padding + "( \n";
		dumped_text += print_r(value,level+1);
		dumped_text += level_padding + ") \n";
	    }
	    else {
		dumped_text += level_padding + "[" + item + "] => \"" + value + "\"\n";
	    }
	}
    }
    else { //Stings/Chars/Numbers etc.
	dumped_text = '(' + typeof(arr) + ') => "'+arr+'"';
    }
    return dumped_text;
}

//////////////////////////////////////////////
//  FONCTIONS Find Object in JS
function MM_findObj(n, d)
{
    var p,i,x;
    if(!d) d=document;
    if((p=n.indexOf("?"))>0&&parent.frames.length) {
	d=parent.frames[n.substring(p+1)].document;
	n=n.substring(0,p);
    }
    if(!(x=d[n])&&d.all) x=d.all[n];
    for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
    for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
    if(!x && document.getElementById) x=document.getElementById(n);
    return x;
}


//////////////////////////////////////////////
//  FONCTIONS Change Element Property
function MM_changeProp(objName,x,theProp,theValue)
{
    var obj = MM_findObj(objName);
    if (obj && (theProp.indexOf("style.")==-1 || obj.style)) eval("obj."+theProp+"='"+theValue+"'");
}


//////////////////////////////////////////////
//FONCTIONS STARTX
//////////////////////////////////////////////

//////////////////////////////////////////////
//ROLL OVER STARTX
function SX_RollIMG(ImgName,NewImg)
{
    MM_changeProp(ImgName,'','src',NewImg,'IMG');
}


//////////////////////////////////////////////
//ROLL OVER DE DIV
function SX_RollDIV(DIVName,DIVStatus)
{
    MM_changeProp(DIVName,'','style.display',DIVStatus,'DIV');
}

function sxGetElementById(idDiv)
{
    if (document.getElementById) doc = document.getElementById(idDiv);
    if ((document.all)&&(!document.getElementById)) doc = document.all[idDiv];
    if (document.layers)  doc = document.layers[idDiv];
    return doc;
}

function switchDisplayDiv(idDiv)
{
    doc = sxGetElementById(idDiv);
    if (doc.style.display == 'none') MM_changeProp(idDiv,'','style.display','','DIV');
    else MM_changeProp(idDiv,'','style.display','none','DIV');
}


function toggleCheckbox(formName)
{
    var form=$(formName);
    var i=form.getElements('checkbox');
    i.each(function(item)
    {
	if (item.checked){
	    item.checked=false;
	}
	else {
	    item.checked=true;
	}
    }
    );
}




//////////////////////////////////////////////
//FONCTIONS TIM HUYNH
//////////////////////////////////////////////


/*
Attend un nom de champs ou une valeur, ex : [i_frame.document.form1.champs_a_verifier],"Jean", "", etc...
Et 'alerter' (true -defaut-/false) si l'on veut qu'une popup s'affiche lors de l'erreur
Si le paramètre est un objet:
Modifie la valeur du champs si incorrecte et Retourne 'true' si le masque de mail est correcte, sinon, renvoie 'false'.
Si le paramètre est une valeur:
Retourne la valeur modifiée dans la propriété 'val' et le 'statut' true/false dans.
ex acceptés: "nom@soc.com", "nom.prenom@soc-pays.division.com,etc...
refusés : nom.prénom@soc.fr, fêtedesmère@société.fr, etc...
Utilisation : testEmail(document.form1.champs).val ou testEmail(this).statut...
*/
function testEmail(obj) {
    var reg = /^[a-z0-9._-]+@[a-z0-9.-]{2,}[.][a-z]{2,3}$/
    if(obj.value == "") /*si pas d'email entrer*/
    {
	alert("Veuillez entrer votre adresse email");
	obj.value = '';
	return false;
    }
    else if(reg.test(obj.value)==true) /*si l'email est valide*/
	return true;
    else /*si l'email n'est pas valid*/
    {
	alert("L\'email saisi n\'est pas valide !")
	obj.value = '';
	return false;
    }
}


/*
Attend un nom de champs ou une valeur, ex : [i_frame.document.form1.champs_a_verifier],"Jean", "", etc...
Et 'alerter' (true -defaut-/false) si l'on veut qu'une popup s'affiche lors de l'erreur
Si le paramètre est un objet:
Modifie la valeur du champs si incorrecte et Retourne 'true' si le masque de tel est accepté, sinon, renvoie 'false'.
Si le paramètre est une valeur:
Retourne la valeur modifiée dans la propriété 'val' et le 'statut' true/false dans.
ex acceptés: "033.01.42.72.38.47", "06.61.51.49.33", "0001.01.42.35.6476" => "00.01.42.35.64.76", "06 61 51 49 33" => "06.61.51.49.33",etc...
refusés :  "(0001)/01|42\35[6476]", "ABCDEFG",etc...
Utilisation : testPhone(document.form1.champs).val ou testPhone(this).statut ou testPhone("0001.01.42.35.6476").val, etc...
*/
function testPhone(obj,alerter)
{
    try {
	val=obj.value;
	if(trim(val)!="") {
	    alpha =/[a-z]|[A-Z]/;
	    res_alpha=alpha.test(val);//test si c'est de l'alpahanumerique sans espace, retour chariot, tabulation, etc...
	    car=/\d{4,10}/;
	    while (car.test(val)==true) {
		res=car.exec(val);
		part1=res[0].substr(0,2);
		part2=res[0].substr(2,res[0].length-2);
		val=val.replace(car,part1+" "+part2);
	    }
	    car=/\;|\:|\,|\-|\.|\s{2,}/;
	    while (car.test(val)==true) {
		val=val.replace(car," ");
	    }
	    car=/^\.|\.$|\.+/;
	    while (car.test(val)==true) {
		val=val.replace(car," ");
	    }
	    if(typeof obj == 'object') {
		obj.value = val;
	    }

	    num =/([0-9]|\.)+/;
	    res_num= num.test(val);
	    interdit1 = /(^\.|\.$|\.\.+)/;//Interdit un mail qui commence ou finit . ou n . d'affiler, etc
	    interdit2 = /[éèàçêîûïëüùôö\[\]\{\}\##\&\;\:\/\!\$\*\!\+\=\?\|\/\é\(\)\{\[\]\}\\\@]/;//ces caractères sont interdits
	    if(res_alpha==false && res_num==true &&  interdit1.test(val)==false && interdit2.test(val)==false ) {
		statut=true;
	    }
	    else {
		statut=false;
		alert('Erreur dans la saisie du numero de téléphone');
		if(typeof obj == 'object')
		    obj.focus();
	    }
	}
	else {
	    statut=true;
	}
    }
    catch(er) {
	alert(er.message);
	statut=false;
    }
    return this;
}


/*
Renvoie la chaîne sans les espaces du début et de la fin
Utilisation : this.form.champs=trim(this.form.champs)
*/
function trim(obj)
{
    try {
	val=new String(obj);
	re=new RegExp("\^\\s+\|\\s+\$")
	while (re.test(val)) {
	    val=val.replace(re,"");
	}
    }
    catch(er) {
	alert("Une erreur est survenue  dans la fonction trim !"+"\nNom:"+er.name+"\nMsg:"+er.message);
    }
    return val;
}


/*Transforme le contenu de l'objet 'obj' en Majuscule.
*/
function ToUpCase(obj)
{
    text=obj.value;
    obj.value=text.toUpperCase();
}


function formatCurrency(num) {
    num = num.toString().replace(/\$|\,/g,'');
    if(isNaN(num))
	num = "0";
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num*100+0.50000000001);
    cents = num%100;
    num = Math.floor(num/100).toString();
    if(cents<10)
	cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
	num = num.substring(0,num.length-(4*i+3))+' '+
	num.substring(num.length-(4*i+3));
    return (((sign)?'':'-') + num + '.' + cents + " &euro;");
}





function znAjax() {

    this.get = function () {

	this.get.html = function (url,param,target) {
	    var targetAjah  = target;
	    var myAjax		= new Ajax.Request(
		url, {
		    method: 'get',
		    parameters: param,
		    onComplete: function(xhr) {
			if (xhr.status == 200)
			    $(targetAjah).innerHTML = xhr.responseText;
			else $(targetAjah).innerHTML = xhr.status;
		    }
		}
		);
	}
	// use zuno.ajax.get.html('myscript.php','foo=bar','output-div-id');


	this.get.json = function (url,param,callback) {
	    var myAjax = new Ajax.Request( url, {
		method: 'get',
		parameters: param,
		onComplete: callback,
		onLoading: function(x,e) {
		    document.body.style.cursor = 'wait';
		},
		onException: function (x,e) {
		    alert("Erreur zuno.ajax.get.json :"+e);
		},
		onSuccess: function(x,e) {
		    document.body.style.cursor = '';
		},
		onFailure: function(x,e) {
		    document.body.style.cursor = '';
		}
	    } );
	    return true;
	}
	// use zuno.ajax.get.json('myscript.php','foo=bar',function (xhr,json) { ... });

	this.get.synchrone = function (url,param,target) {
	    var targetAjah  = target;
	    var myAjax		= new Ajax.Request(
		url, {
		    method: 'get',
		    parameters: param,
		    asynchronous: false,
		    onComplete: function(xhr)

		    {
			if (xhr.status == 200)
			    $(targetAjah).innerHTML = xhr.responseText;
			else $(targetAjah).innerHTML = xhr.status;
		    }
		}
		);
	}


    }


    this.post = function () {

	this.post.html = function (url,param,target) {
	    var targetAjah  = target;
	    var myAjax		= new Ajax.Request(
		url, {
		    method: 'post',
		    postBody: param,
		    onComplete: function(xhr)

		    {
			if (xhr.status == 200)
			    $(targetAjah).innerHTML = xhr.responseText;
			else $(targetAjah).innerHTML = xhr.status;
		    }
		}
		);
	}
	// use zuno.ajax.post.html('myscript.php','foo=bar','output-div-id');


	this.post.json = function (url,param,callback) {
	    var myAjax = new Ajax.Request( url, {
		method: 'post',
		parameters: param,
		onComplete: callback,
		onLoading: function(x,e) {
		    document.body.style.cursor = 'wait';
		},
		onException: function (x,e) {
		    alert("Erreur zuno.ajax.post.json :"+e);
		},
		onSuccess: function(x,e) {
		    document.body.style.cursor = '';
		},
		onFailure: function(x,e) {
		    document.body.style.cursor = '';
		}
	    } );
	    return true;
	}
    // use zuno.ajax.post.json('myscript.php','foo=bar',function (xhr,json) { ... });

    }

    this.get();
    this.post();
}



function ajajPost(url,param,callback) {
    var myAjax = new Ajax.Request( url, {
	method: 'post',
	parameters: param,
	onLoading: function(x,e) {
	    document.body.style.cursor = 'wait';
	},
	onComplete: callback,
	onException: function (x,e) {
	    alert("Erreur AjajPost :"+e);
	}
    } );
    return true;
}
// use ajajPost('myscript.php','foo=bar',function (xhr,json) { ... });






function znTools() {

    this.setCookie = function (name,value,expiredays) {
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie=name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
    }

    this.getCookie = function (name) {
	if (document.cookie.length>0) {
	    var start=document.cookie.indexOf(name + "=");
	    if (start!=-1) {
		start=start + name.length+1;
		var end=document.cookie.indexOf(";",start);
		if (end==-1) end=document.cookie.length;
		return unescape(document.cookie.substring(start,end));
	    }
	}
	return "";
    }

    this.checkCookie = function (name) {
	var cookie=this.getCookie(name);
	if (cookie!=null && cookie!="")
	    return true;
	else return false;
    }

    this.getBrowser = function ()
    {
	var strChUserAgent = navigator.userAgent;
	var intSplitStart = strChUserAgent.indexOf("(",0);
	var intSplitEnd = strChUserAgent.indexOf(")",0);
	var strChStart = strChUserAgent.substring(0,intSplitStart);
	var strChMid = strChUserAgent.substring(intSplitStart, intSplitEnd);
	var strChEnd = strChUserAgent.substring(strChEnd);

	if(strChMid.indexOf("MSIE 7") != -1)
	    return "ie7";
	else if(strChMid.indexOf("MSIE 6") != -1)
	    return "ie6";
	else if(strChEnd.indexOf("Firefox/2") != -1)
	    return "firefox2";
	else if(strChEnd.indexOf("Firefox") != -1)
	    return "firefox";
	else if(strChEnd.indexOf("Netscape/7") != -1)
	    return "netscape7";
	else if(strChEnd.indexOf("Netscape") != -1)
	    return "netscape";
	else if(strChStart.indexOf("Opera/9") != -1)
	    return "opera9";
	else if(strChStart.indexOf("Opera") != -1)
	    return "opera";
	else return "autre";
    }


    this.goToUrl = function (url) {
	window.document.location = url;
    }
}







function znWorkspace() {
    this.divId			= 'ZunoWorkspace';
    this.div			= $(this.divId);
    this.cookieName		= 'zunoDisplayParams';
    this.cookieExpire	= 30; // en jours

    this.getWindowSize = function (){
	var w			= window.innerWidth;
	var h			= window.innerHeight;
	return new Array(w,h);
    }

    this.changeSize = function (){
	var size 					= this.getWindowSize();
	var w						= size[0];//-60;
	var h						= size[1];//-30;
	this.sizeName   			= w+'x'+h;
	this.div.style.width		= w+"px";
	this.div.style.minHeight	= h+"px";
	return true;
    }


    this.checkDisplay = function () {
	if(zuno.tools.checkCookie(this.cookieName)) {
	    var cookieSize = zuno.tools.getCookie(this.cookieName).split('x');
	    var windowSize = this.getWindowSize();
	    this.changeSize();
	}
	else {
	    this.changeSize();
	}
    }

}






/**
 * Gestion Javascript des popup
*/
function znPopup() {

    /**
	 * Récupère le contenu d'une page (Ajax) et ouvre une Popup
	 * @param	url de la ressource
         * @param       param Les paramètres
	 * @param	width longeur de la fenêtre
	 * @param	height hauteur de la fenêtre
         * @param       gauche Précise la position relative au coin gauche de l'écran
         * @param       haut Précise la position relative au coin haut de l'écran
	 * @param	option paramètres spécifiques à une configuration donnée.
         * @param       name Le nom de la popup
	 * @return  booléen si la popup a pu s'ouvrir
	*/
    this.open = function (url,param,width,height,gauche,haut,option,name) {
	var targetAjah  = 'ZunoPopupWindow';
	this.ajaxObject = new Ajax.Request(
	    url, {
		method: 'get',
		parameters: param,
		onComplete: function(xhr) {
		    var txt = '';
		    var title = '';
		    var footer = '';
		    if (xhr.status == 200) {
			txt = xhr.responseText;
			zuno.popup.xhtmlContent.innerHTML = txt;
			var newTitle = $$('#ZunoPopupWindowContent .header .title h3 a[id]');
			if(newTitle[0] != undefined)
			    title = newTitle[0].innerHTML;
			else title = name;
			var newFooter = $$('#ZunoPopupWindowContent .ZBox .footer div.content');
			if(newFooter[0] != undefined)
			    footer = newFooter[0].innerHTML;
			else footer = '';
		    }
		    else {
			txt   = xhr.status;
			title = "Une erreur est survenue";
			footer = "Une erreur est survenue";
		    }
		    return zuno.popup.doOpen(txt,title,width,height,footer);
		}
	    }
	    );
	return false;
    }

    /**
	 * Ouvre une Popup
	 * @param 	text de contenu de la popup
	 * @param	title Titre de la popup
	 * @param	width longueur de la fenêtre
	 * @param	height hauteur de la fenêtre
	 * @param	footer ied de page
	 * @return  booléen si la popup a pu s'ouvrir
	*/
    this.doOpen = function (text,title,width,height,footer) {
	zuno.popup.xhtmlContent.innerHTML = '';
	zuno.popup.xhtmlOpacity.style.display = 'block';
	var window = zuno.workspace.getWindowSize();
	zuno.popup.xhtmlContent.innerHTML = text;
	var e = $$("#ZunoPopupWindowContent script");
	for (var i=0; i<e.length; i++) {
	    var s = e[i];
	    if (s.src && s.src!="")
		eval(getFileContent(s.src));
	    else eval(s.innerHTML);
	}
	zuno.popup.xhtmlTitle.innerHTML = title;
	zuno.popup.xhtmlFooter.innerHTML = footer;

	var newTitle = $$('#ZunoPopupWindowContent .header .title h3 a[id]');
	if(newTitle[0] != undefined)
	    newTitle[0].parentNode.removeChild(newTitle[0]);
	var newFooter = $$('#ZunoPopupWindowContent .ZBox .footer div.content');
	if(newFooter[0] != undefined)
	    newFooter[0].parentNode.removeChild(newFooter[0]);

	zuno.popup.xhtmlWindow.style.display = 'block';
	if(width != undefined)
	    zuno.popup.xhtmlWindow.style.width	 = width+"px";
	if(height != undefined){
	    zuno.popup.xhtmlWindow.style.height	 = height+"px";
	}
	zuno.popup.xhtmlWindow.style.left	 = Math.round((window[0]-this.xhtmlWindow.offsetWidth)/2)+"px";
	zuno.popup.xhtmlWindow.style.top	 = Math.round((window[1]-this.xhtmlWindow.offsetHeight)/2)+"px";
	zuno.popup.draggableObject	= new Draggable('ZunoPopupWindow',{
	    handle: 'ZunoPopupWindowDragbar'
	});
	zuno.popup.moveableObject	= new Resizeable('ZunoPopupWindow');
	return true;
    }


    /**
	 * Ferme une Popup
	 * @return  booléen si la popup a pu se fermer
	*/
    this.close = function () {
	zuno.popup.xhtmlOpacity.style.display = 'none';
	zuno.popup.xhtmlWindow.style.display  = 'none';
	zuno.popup.draggableObject.destroy();
	zuno.popup.moveableObject.destroy();
	return true;
    }

    /**
	 * Ferme une Popup
	 * @return  booléen si la popup a pu se fermer
	*/
    this.init = function () {
	this.draggableObject = undefined;
	this.moveableObject  = undefined;
	this.ajaxObject      = undefined;
	this.xhtmlOpacity = $('ZunoPopupOpacity');
	this.xhtmlWindow  = $('ZunoPopupWindow');
	this.xhtmlTitle   = $('ZunoPopupWindowTitle');
	this.xhtmlFooter   = $('ZunoPopupFooter');
	this.xhtmlContent = $('ZunoPopupWindowContent');
	return true;
    }

    this.init();

}


/**
 * Gestion Javascript des popup
*/
function znContextBox() {


    /**
	 * Ouvre une Popup
	 * @param	link to opener element
	 * @param	divToOpen div id to open
	*/
    this.open = function (handler,divToOpen) {

	if($(divToOpen)) {
	    this.close();
	    this.hasOpen		 = true;
	    this.currentlyOpen	 = divToOpen;
	    Effect.SlideDown(divToOpen);
	    if(handler) {
		this.currentHandler = handler;
		this.currentHandler.setAttribute('onclick',"zuno.contextBox.close();");
	    }
	    return true;
	}
	return false;
    }

    /**
	 * Ferme une Popup
	 * @return  booléen si la popup a pu se fermer
	*/
    this.close = function () {
	if(this.hasOpen) {
	    this.hasOpen		 = false;
	    if(this.currentHandler) {
		this.currentHandler.setAttribute('onclick',"zuno.contextBox.open(this,'"+zuno.contextBox.currentlyOpen+"');");
		this.currentHandler = undefined;
	    }
	    Effect.SlideUp(this.currentlyOpen);
	    this.currentlyOpen	 = undefined;
	    return true;
	}
	return false;
    }

    /**
	 * Ferme une Popup
	 * @return  booléen si la popup a pu se fermer
	*/
    this.init = function () {
	this.hasOpen		 = false;
	this.currentlyOpen	 = undefined;
	this.currentHandler	 = undefined;
    }

    this.init();

}











var curentBox = undefined;

function Zbox(id,state,title1) {

    var curentBox		= id;
    this.boxId 			= id;
    this.state 			= state;

    this.title1 		= new String(title1);
    this.title1.replace("\'", "'");
    this.buttonDetailId = this.boxId+'OptDetail';
    this.buttonCloserId = this.boxId+'OptCloser';
    this.BodyId 		= this.boxId+'Body';
    this.Body1Id 		= this.boxId+'Body1';
    this.TitleId 		= this.boxId+'Title';
    this.title 			= $(this.TitleId).innerHTML;

    this.initOpen = function (){
	var buttonCloser = $(this.buttonCloserId);
	var linkTitle = $(this.TitleId);
	buttonCloser.title = 'Fermer la boite de dialogue';
	buttonCloser.innerHTML = 'x';
	buttonCloser.onclick = function() {
	    eval(curentBox+"ZBox"+".close()");
	}
	linkTitle.onclick = function() {
	    eval(curentBox+"ZBox"+".close()");
	}
	this.initDetail();
	return true;
    }

    this.initClose = function (){
	var buttonCloser = $(this.buttonCloserId);
	var linkTitle = $(this.TitleId);
	buttonCloser.title = 'Ouvrir la boite de dialogue';
	buttonCloser.innerHTML = 'o';
	buttonCloser.onclick = function() {
	    eval(curentBox+"ZBox"+".open()");
	}
	linkTitle.onclick = function() {
	    eval(curentBox+"ZBox"+".open()");
	}
	this.initDetail();
	return true;
    }

    this.initDetail = function (){
	var buttonDetail = $(this.buttonDetailId);
	if(buttonDetail != undefined) {
	    this.switchDetailOptions();
	    buttonDetail.onclick = function() {
		eval(curentBox+"ZBox"+".switchDetail()");
	    }
	}
	return true;
    }

    this.switchDetailOptions = function (){
	var buttonDetail = $(this.buttonDetailId);
	if(buttonDetail != undefined) {
	    if(this.state == 'open1') {
		buttonDetail.title = 'Moins de détail';
		buttonDetail.innerHTML = '-';
	    }
	    else {
		buttonDetail.title = 'Plus de détail';
		buttonDetail.innerHTML = '+';
	    }
	    return true;
	}
	return false;
    }

    this.close = function (){

	if(this.state == 'open1')
	    Effect.SlideUp(this.Body1Id,{
		duration:0.2,
		queue: 'end'
	    });
	else Effect.SlideUp(this.BodyId,{
	    duration:0.2,
	    queue: 'end'
	});
	this.state = 'close';
	this.initClose();
	this.memorizeState();
	return true;
    }

    this.open = function (){
	if(this.state == 'open1')
	    Effect.SlideDown(this.Body1Id,{
		duration:0.2,
		queue: 'end'
	    });
	else Effect.SlideDown(this.BodyId,{
	    duration:0.2,
	    queue: 'end'
	});
	this.state = 'open';
	this.initOpen();
	this.memorizeState();
	return true;
    }

    this.switchDetail = function (){
	if(this.state == 'open') {
	    Effect.SlideUp(this.BodyId,{
		duration:0.2,
		queue: 'end'
	    });
	    this.state = 'open1';
	    this.switchTitle(this.title1);
	    this.switchDetailOptions();
	    this.initOpen();
	    Effect.SlideDown(this.Body1Id,{
		duration:0.5,
		queue: 'end'
	    });
	}
	else if(this.state == 'open1') {
	    Effect.SlideUp(this.Body1Id,{
		duration:0.2,
		queue: 'end'
	    });
	    this.state = 'open';
	    this.switchTitle(this.title);
	    this.switchDetailOptions();
	    this.initOpen();
	    Effect.SlideDown(this.BodyId,{
		duration:0.5,
		queue:'end'
	    });
	}
	else {
	    Effect.SlideDown(this.BodyId,{
		duration:0.2,
		queue: 'end'
	    });
	    this.state = 'open';
	    this.switchTitle(this.title);
	    this.switchDetailOptions();
	    this.initClose();
	}
	this.memorizeState();
	return true;
    }


    this.memorizeState = function (){
	var param 	= new Object();
	param.action= 'doSaveZBoxState';
	param.zboxid  = this.boxId;
	param.state   = this.state;
	zuno.ajax.post.json(zuno.contextPath+'ajaxWorkspace.php',param);
	return true;
    }

    this.switchTitle = function (newTitle){
	document.getElementById(this.TitleId).innerHTML = newTitle;
	return true;
    }

    if(this.state == 'open') {
	this.initOpen()
	if($(this.Body1Id) != undefined)
	    $(this.Body1Id).style.display = 'none';
    }
    else if(this.state == 'open1') {
	this.initOpen()
	if($(this.BodyId) != undefined)
	    $(this.BodyId).style.display  = 'none';
    }
    else {
	this.state == 'close';
	this.initClose();
	if($(this.BodyId) != undefined)
	    $(this.BodyId).style.display  = 'none';
	if($(this.Body1Id) != undefined)
	    $(this.Body1Id).style.display = 'none';
    }

}









var menu;
var menuOriginalHeight;
var menuOriginalWidth;
function initMenu() {
    menu = new Menu('menuTree', 'menu',function () {
	this.closeDelayTime = 500;
    });
}


function initMenuSize(){
    menuOriginalHeight 	= $('ZSBMenu').getHeight()-2;
    menuOriginalWidth 	= $('ZSBMenu').getWidth()-2;
}

Event.observe(window, 'load', initMenu, false);
Event.observe(window, 'load', initMenuSize, false);





















function placerLiens(id, nom)
{
    $('path_hidden_form_cp').value = id.substr(1,id.length-1);
    $('nom_rep_form_cp').value = nom;
}
function afficherContenu2 (url, param, id, nom)
{
    if($(id).getAttribute('ouvert') == 'false')
    {
	placerLiens(id, nom);
    }
    afficherContenu(url, param, id);
}
/*
function afficherContenu (url, param, id)
{
    if($(id).getAttribute('ouvert') == 'false')
    {
        $(id).setAttribute('ouvert', 'encours');
        var div = document.createElement('div');
        div.setAttribute('style', 'display:none;');
        div.setAttribute('id', 'retourAjax');
        div.setAttribute('cible', id);
        $(id).appendChild(div);
        zuno.ajax.get.synchrone(url, param, div.getAttribute('id'));
        var div = $('retourAjax');
        var id = div.getAttribute('cible');
        var parent = $(id).parentNode;
        var suivant = $(id).nextSibling;
        var total = div.childNodes.length;
        for( var k = total-1; k>=0; k--)
        {
            parent.insertBefore(div.childNodes[k], suivant);
            suivant = $(id).nextSibling;
        }
        $(id).removeChild(div);
        $(id).setAttribute('ouvert', 'true');
    }
    else if($(id).getAttribute('ouvert') == 'true')
    {
        $(id).setAttribute('ouvert', 'encours');
        var taille = id.length;
        var parent = $(id).parentNode;
        var suivant = $(id).nextSibling;
        while(suivant.getAttribute('id').substr(0, taille) == id)
        {
            parent.removeChild(suivant);
            suivant = $(id).nextSibling;
            if(suivant == null)
            {
                break;
            }
        }
        $(id).setAttribute('ouvert', 'false');
    }
    else
    {
        alert('Erreur car pas d\'attribut OUVERT ou FERME sur cet élément');
    }
}
*/
function afficherContenu (url, param, id)
{
    if($(id).getAttribute('ouvert') == 'false')
    {
	$(id).setAttribute('ouvert', 'encours');
	$('ZunoPopupOpacity').style.display='block';
	$('ZunoPopupOpacity').style.backgroundImage ='url('+zunoJsf.STATICIMGURL+'ajax-loader2.gif)';
	$('ZunoPopupOpacity').style.backgroundRepeat='no-repeat';
	$('ZunoPopupOpacity').style.backgroundAttachment='fixed';
	$('ZunoPopupOpacity').style.backgroundPosition='center';
	var div = document.createElement('div');
	div.setAttribute('style', 'display:none;');
	div.setAttribute('id', 'retourAjax');
	div.setAttribute('cible', id);
	$(id).appendChild(div);
	zuno.ajax.get.synchrone(url, param, div.getAttribute('id'));
	var div = $('retourAjax');
	var id = div.getAttribute('cible');
	var parent = $(id).parentNode;
	var suivant = $(id).nextSibling;
	var total = div.childNodes.length;
	for( var k = total-1; k>=0; k--)
	{
	    parent.insertBefore(div.childNodes[k], suivant);
	    suivant = $(id).nextSibling;
	}
	$(id).removeChild(div);
	$('ZunoPopupOpacity').style.background="";
	$('ZunoPopupOpacity').style.display='none';
	$(id).setAttribute('ouvert', 'true');
    }
    else if($(id).getAttribute('ouvert') == 'true')
    {
	$(id).setAttribute('ouvert', 'encours');
	var taille = id.length;
	var parent = $(id).parentNode;
	var suivant = $(id).nextSibling;
	while(suivant.getAttribute('id').substr(0, taille) == id)
	{
	    parent.removeChild(suivant);
	    suivant = $(id).nextSibling;
	    if(suivant == null)
	    {
		break;
	    }
	}

	$(id).setAttribute('ouvert', 'false');
    }
    else
    {
	alert($(id));
    }

}


























function znBusinessForm(){

    this.sendAjaxForm = function (formName,toUrl) {
	param = new Array();
	input = $$('form[name='+formName+'] input, form[name='+formName+'] textarea');
	for(i = 0; i < input.length; i++) {
	    k = new String(input[i].getAttribute('name'));
	    if( input[i].type == "checkbox" && input[i].checked != true)
	    {
		continue;
	    }
	    else	{
		v = input[i].value;
	    }
	    param[k] = v;
	}
	select = $$('form[name='+formName+'] select');
	for(i = 0; i < select.length; i++) {
	    k = new String(select[i].getAttribute('name'));
	    selected = new Array();
	    for (var ii = 0; ii < select[i].options.length; ii++)
		if (select[i].options[ii].selected)
		    selected.push(select[i].options[ii].value);
	    param[k] = selected;
	}
	messageBox  = $$('form[name='+formName+'] span.important')[0];
	var d = zuno.ajax.post.json(zuno.contextPath+toUrl,new Object(param),
	    function(xhr,json)
	    {
		if (xhr.status == 200) {
		    if(json.code == true) {
			zuno.popup.close();
			window.event.preventDefault();
		    }
		    else  {
			messageBox.innerHTML = json.mess;
			messageBox.className = 'important';
			messageBox.style.display = 'block';
			window.event.preventDefault();
		    }
		}
		else {
		    messageBox.innerHTML = xhr.statusText;
		    messageBox.className = 'important';
		    messageBox.style.display = 'block';
		    window.event.preventDefault();
		}
	    }
	    );
    }

    this.sendFormAjah = function (formName,toUrl, idRetour, popup) {
	param = new Array();
	input = $$('form[name='+formName+'] input, form[name='+formName+'] textarea');
	for(i = 0; i < input.length; i++) {
	    k = new String(input[i].getAttribute('name'));
	    if( (input[i].type == "checkbox" || input[i].type == "radio") && input[i].checked != true)
	    {
		continue;
	    }
	    else	{
		v = input[i].value;
	    }
	    param[k] = v;
	}
	select = $$('form[name='+formName+'] select');
	for(i = 0; i < select.length; i++) {
	    k = new String(select[i].getAttribute('name'));
	    selected = new Array();
	    for (var ii = 0; ii < select[i].options.length; ii++)
		if (select[i].options[ii].selected)
		    selected.push(select[i].options[ii].value);
	    param[k] = selected;
	}
	var d = zuno.ajax.post.json(zuno.contextPath+toUrl,param,
	    function(xhr)
	    {
		document.body.style.cursor = 'auto';
		if (xhr.status == 200)
		{
		    var div = document.createElement('div');
		    div.innerHTML = xhr.responseText;
		    if(div.getElementsByTagName('redirection')[0] != undefined)
		    {
			window.location = div.getElementsByTagName('redirection')[0].innerHTML;
		    }
		    if(div.getElementsByTagName('erreur')[0] != undefined)
		    {
			var retour = div.getElementsByTagName('erreur')[0].innerHTML;
			div.removeChild(div.getElementsByTagName('erreur')[0]);
			$(retour).innerHTML = div.innerHTML;
			if($$('#'+retour+' span.important')[0] != undefined)
			    setTimeout(function() {
				$$('#'+retour+' span.important')[0].style.display='none';
			    }, 2000);
		    }
		    else
		    {
			$(idRetour).innerHTML = xhr.responseText;
			var e = $$("#BodyContent script");
			for (var i=0; i<e.length; i++) {
			    var s = e[i];
			    if (s.src && s.src!="")
				eval(getFileContent(s.src));
			    else eval(s.innerHTML);
			}
			if($$('#'+idRetour+' span.important')[0] != undefined)
			    setTimeout(function() {
				for (i in $$('#'+idRetour+' span.important') ){
				    if($$('#'+idRetour+' span.important')[i].style != undefined)
					$$('#'+idRetour+' span.important')[i].style.display='none';
				}
			    }, 2000);
			if(popup == 'popup2') {
			    zuno.popup.close();
			}
		    }
		}
	    }
	    );
    }

    this.getParamFromForm = function (formName)
    {
	param = new Array();
	input = $$('form[name='+formName+'] input, form[name='+formName+'] textarea');
	for(i = 0; i < input.length; i++) {
	    k = new String(input[i].getAttribute('name'));
	    if( (input[i].type == "checkbox" || input[i].type == "radio") && input[i].checked != true)
		continue;
	    else v = input[i].value;
	    param[k] = v;
	}
	select = $$('form[name='+formName+'] select');
	for(i = 0; i < select.length; i++) {
	    k = new String(select[i].getAttribute('name'));
	    selected = new Array();
	    for (var ii = 0; ii < select[i].options.length; ii++)
		if (select[i].options[ii].selected)
		    selected.push(select[i].options[ii].value);
	    param[k] = selected;
	}
	return param;
    }

    this.sendGetAjah = function (param,toUrl, idRetour, displayDuration) {
	var d = ajajPost(zuno.contextPath+toUrl,param,
	    function(xhr) {
		document.body.style.cursor = 'auto';
		if (xhr.status == 200) {
		    if($$('#'+idRetour+' span.important')[0] != undefined) {
			divTo = $$('#'+idRetour+' span.important')[0];
		    }
		    else {
			var div = document.createElement('span');
			div.className = 'important';
			$(idRetour).appendChild(div);
			divTo = $$('#'+idRetour+' span.important')[0];
		    }
		    divTo.style.display = 'block';
		    divTo.innerHTML = xhr.responseText;
		    ddur = (displayDuration != undefined) ? displayDuration : 2000;
		    setTimeout(function() {
			$$('#'+idRetour+' span.important')[0].style.display='none';
		    }, ddur);
		}
	    }
	    );
    }
}


function znBusiness() {


    this.init = function () {
	this.formTools 	= new znBusinessForm();
    }

    this.init();
}








function znAuthentification(){

    this.doLogin = function () {
	param = new Object();
	param.login = $F('authentification.connect.login');
	param.pwd   = $F('authentification.connect.pwd');
	param.action= 'doLogin';
	messageBox  = $('authentification.connect.message');
	var d = zuno.ajax.post.json(zuno.contextPath+'ajaxAuthentification.php',param,
	    function(xhr,json)
	    {
		if (xhr.status == 200) {
		    if(json.code == true) {
			messageBox.style.display = 'none';
			messageBox.className = '';
			location.href = 'index.php';
		    }
		    else  {
			messageBox.innerHTML = json.mess;
			messageBox.className = 'important';
			messageBox.style.display = 'block';
		    }
		}
		else {
		    messageBox.innerHTML = xhr.statusText;
		    messageBox.className = 'important';
		    messageBox.style.display = 'block';
		}
		return true;
	    }
	    );
	return false;
    }

    this.doReset = function () {
	$F('authentification.connect.login').value = '';
	$F('authentification.connect.pwd').value = '';
	zuno.contextBox.close();
	return false;
    }

    this.redirectLogout = function () {
	location.href = 'index.php';
	return true;
    }

    this.disconnect = new Object();

    this.disconnect.doLogout = function (doSave) {
	param = new Object();
	param.action= 'doLogout';
	param.doSave= doSave;
	var d = zuno.ajax.post.json(zuno.contextPath+'ajaxAuthentification.php',param,
	    function(xhr,json) {
		if (xhr.status == 200) {
		    if(json.code == true) {
			zuno.popup.doOpen('vous êtes maintenant deconnecté','Merci de votre visite');
			setTimeout("zuno.auth.redirectLogout()", 1500);
		    }
		    else return zuno.popup.doOpen('Erreur de deconnexion','L\'erreur suivante est survenue lors de votre deconnexion: '+ json.mess);
		}
		else return zuno.popup.doOpen('Erreur de deconnexion','L\'erreur suivante est survenue lors de votre deconnexion: '+ xhr.statusText);
	    }
	    );
	return false;
    }

    this.disconnect.save = function () {
	this.doLogout(true);
    }

    this.disconnect.noSave = function () {
	this.doLogout(false);
    }

}


function znActualite(){

    this.init = function () {
	this.pWidth = 0;
	this.speed = 5;
	this.timeout = '';
	this.go = 0;
	this.divMarquee = document.getElementById("marquee");
	this.divMarquee.style.overflow = 'hidden';
	this.divMarquee.scrollLeft = 0;

	var divSpacer = document.getElementById("marqueeSpacer");
	divSpacer.style.width = this.divMarquee.offsetWidth+'px';
	this.calculWidth();

	var startdiv = document.getElementById("marqueeStart");
	startdiv.style.width = (this.pWidth)+'px';
	this.startit();
    }
    this.startit = function () {
	this.go = 0;
	this.defil();
    }
    this.calculWidth = function () {
	this.pWidth = 0;
	var ps = $$('#marquee p');
	for(var j=0;j<ps.length;j++){
	    this.pWidth += ps[j].offsetWidth;
	}
	this.pWidth += this.divMarquee.offsetWidth;
    }
    this.defil = function () {
	clearTimeout(this.timeout);
	var el = this.divMarquee;
	if(el.scrollLeft >= this.pWidth)
	    el.scrollLeft = 0;
	el.scrollLeft = el.scrollLeft+this.speed;
	if(el.scrollLeft >= this.pWidth-el.offsetWidth) {
	    this.restart();
	    return;
	}
	if(this.go == 0)
	    this.timeout = setTimeout("zuno.filActu.defil();",40);
    }
    this.stop = function () {
	this.go = 1;
	this.timeout = '';
	return;
    }

    this.restart = function () {
	//alert("restart");
	this.addContent();
	this.go = 0;
	this.divMarquee.scrollLeft = 0;
	this.defil();
	return;
    }

    this.addContent = function () {
	var url = zuno.contextPath+'actualite.php';
	zuno.ajax.get.synchrone(url, 'action=ajax&channel='+zuno.channel, 'marqueeTemp');
	var content = '<p id="marqueeSpacer"></p>'+$('marqueeTemp').innerHTML;
	var inDiv = document.getElementById("marqueeStart");
	inDiv.innerHTML = content;
	this.init();
	return;
    }

    this.addMessage = function (message) {
	var p = document.createElement('p');
	p.innerHTML = message;
	var inDiv = document.getElementById("marqueeStart");
	inDiv.appendChild(p);
    }

    this.popupActu = function (idActu) {
	return zuno.popup.open(zuno.contextPath+'actualite.php', '&channel='+zuno.channel+'&type=popup&id='+idActu, '500', '350', '', '', 'resize', 'Actualités');
    }

    this.init();
}




function zuno() {

    this.init = function () {
	this.tools 	= new znTools();
	this.ajax	= new znAjax();
	this.workspace 	= new znWorkspace();
	this.auth 	= new znAuthentification();
	this.popup 	= new znPopup();
	this.contextBox = new znContextBox();
	this.business   = new znBusiness();
	this.filActu    = new znActualite();

	/**
		 * Gestion Javascript des tooltips
		*/
	this.tooltipStore = new Array();
	document.observe('prototip:hidden', function(event) {
	    document.body.style.cursor = 'auto';
	});
	this.tooltip = function (element,content) {
	    if(element != undefined && content != undefined ) {
		// On crée un ID s'il n'y en a pas
		if(element.id == '' || element.id == undefined) {
		    element.setAttribute('id',"labelTips"+this.tooltipStore.length);
		}
		// On regarde si l'ID à déjà été enregistré
		var hasBeenRegistered = false;
		for (i=0;i<this.tooltipStore.length;i++) {
		    if(this.tooltipStore[i] == element.id) {
			hasBeenRegistered = true;
		    }
		}
		// Et si ca n'est pas le cas, on enregistre dans la variable de stockage des tooltip déjà crée
		// Et on crée l'object tooltip
		if(!hasBeenRegistered) {
		    this.tooltipStore[this.tooltipStore.length] = element.id;
		    element.observe('prototip:shown', function(event) {
			document.body.style.cursor = 'help';
		    });
		    element.observe('prototip:hidden', function(event) {
			document.body.style.cursor = 'auto';
		    });
		    var color = '';
		    if(this.channel == 'draco')			color = '#007f00';
		    else if(this.channel == 'gnose')	color = '#7f0039';
		    else if(this.channel == 'pegase')	color = '#007f7f';
		    else if(this.channel == 'produit')	color = '#7f007f';
		    else if(this.channel == 'prospec')	color = '#827f00';
		    else if(this.channel == 'facturier')color = '#00007f';
		    else if(this.channel == 'admin')	color = '#7f0000';
		    var t = new Tip(element, content, {
			style: 'protogrey',
			borderColor: color,
			stem: 'topLeft',
			hideAfter: 0.1,
			hideOthers: true,
			hideOn: 'mouseleave'
		    });
		}
	    }
	}
    }

    this.setChannel = function (channelName) {
	this.contextPath 	= './';
	this.channel 	 	= channelName;
	if(channelName != 'normal')
	    this.contextPath 	= '../';
    }

    this.load = function (channelName) {
	this.setChannel(channelName);
	this.workspace.checkDisplay();
	window.setTimeout("zuno.filActu.defil()",100);
	if(promptForFirefox != undefined && promptForFirefox != false) {
	    var b = zuno.tools.getBrowser();
	    if(b == "ie6" || b == "ie7")
		r = zuno.popup.doOpen('<a href="http://www.mozilla-europe.org/fr/firefox/" target="_blank" tilte="télécharger Firefox"><img src="'+zunoJsf.STATICIMGURL+'zunoPreferedBrowser.png" alt="télécharger firefox" title="télécharger firefox"/></a>','Recommandation d\'utilisation');
	}

    }

    this.init();
}





//EDITABLE HTML SELECT BOX SCRIPT
//Author: Teo Cerovski
//CommentsTo: teoDOTcerovskiATgmailDOTcom
/*
Include this script to your web page.
example: <SCRIPT language="javascript" type="text/javascript" src="editableSelectBox.js"></SCRIPT>

Put event attributes onClick="beginEditing(this);" and onBlur="finishEditing();" to your select box.
example: <SELECT name="foo" id="foo" onClick="beginEditing(this);" onBlur="finishEditing();">...</SELECT>

When editing is finished option values are overwriten with text values, so only text of the selected option will be posted.
*/


var o = null;
var isNN = (navigator.appName.indexOf("Netscape")!=-1);
var selectedIndex = 0;
var pointer = "|";
var blinkDelay = null;
var pos = 0;

function beginEditing(menu) {
    finishEditing();
    if(menu.selectedIndex > -1 && menu[menu.selectedIndex].value != "read-only") {
	o = new Object();
	o.editOption = menu[menu.selectedIndex];
	o.editOption.old = o.editOption.text;
	o.editOption.text += pointer;
	selectedIndex = menu.selectedIndex;
	if(navigator.userAgent.toLowerCase().indexOf("msie") != -1) //user is using IE
	    document.onkeydown = keyPressHandler;
	else
	    document.onkeypress = keyPressHandler;
	pos = o.editOption.text.indexOf(pointer);
	blinkDelay = setTimeout("blinkPointer()", 300);
    }

    function keyPressHandler(e){
	stopBlinking();
	menu.selectedIndex = selectedIndex;
	var option = o.editOption;
	var keyCode = (window.event) ? event.keyCode : e.keyCode;
	var specialKey = true;
	if(keyCode == 0){
	    keyCode = (isNN) ? e.which : event.keyCode;
	    specialKey = false;
	}

	if(keyCode == 16)
	    return false;
	else if(keyCode == 116 && specialKey){
	    finishEditing();
	    window.location.reload(true);
	}
	else if(keyCode == 8)
	    option.text = option.text.substring(0,option.text.indexOf(pointer)-1) + pointer + option.text.substring(option.text.indexOf(pointer)+1,option.text.length);
	//        else if(keyCode == 46  && option.text.indexOf(pointer) < option.text.length)
	//            option.text = option.text.substring(0,option.text.indexOf(pointer)) + pointer + option.text.substring(option.text.indexOf(pointer)+2,option.text.length);
	else if (keyCode == 13)
	    finishEditing();
	else if(keyCode == 37 && option.text.indexOf(pointer) > 0 && specialKey)
	    option.text = option.text.substring(0,option.text.indexOf(pointer)-1) + pointer + option.text.substring(option.text.indexOf(pointer)-1,option.text.indexOf(pointer)) + option.text.substring(option.text.indexOf(pointer)+1,option.text.length);
	else if(keyCode == 39 && option.text.indexOf(pointer) < option.text.length && specialKey)
	    option.text = option.text.substring(0,option.text.indexOf(pointer)) + option.text.substring(option.text.indexOf(pointer)+1,option.text.indexOf(pointer)+2) + pointer + option.text.substring(option.text.indexOf(pointer)+2,option.text.length);
	else if(((keyCode == 37 && option.text.indexOf(pointer) <= 0) || (keyCode == 39 && option.text.indexOf(pointer) >= option.text.length) || keyCode == 40 || keyCode == 38 || keyCode == 20 || keyCode == 33 || keyCode == 34) && specialKey){
	//do nothing
	}else if(keyCode == 36 && specialKey)
	    option.text = pointer + option.text.substring(0,option.text.indexOf(pointer)) + option.text.substring(option.text.indexOf(pointer)+1,option.text.length);
	else if(keyCode == 35 && specialKey)
	    option.text = option.text.substring(0,option.text.indexOf(pointer)) + option.text.substring(option.text.indexOf(pointer)+1,option.text.length) + pointer;
	else
	    option.text = option.text.substring(0,option.text.indexOf(pointer)) + String.fromCharCode(keyCode) + pointer + option.text.substring(option.text.indexOf(pointer)+1,option.text.length);

	pos = option.text.indexOf(pointer);
	blinkDelay = setTimeout("blinkPointer()", 300);

	if(!((keyCode >= 48 && keyCode <= 90) || (keyCode >= 96 && keyCode <= 122)))
	    return false;
	return true;
    }

}

function blinkPointer(){
    if(o == null)
	return;
    pos = o.editOption.text.indexOf(pointer);
    o.editOption.text = o.editOption.text = o.editOption.text.substring(0,o.editOption.text.indexOf(pointer)) + "." + o.editOption.text.substring(o.editOption.text.indexOf(pointer)+1,o.editOption.text.length)
    blinkDelay = setTimeout("blinkPointer2()", 300);
}

function blinkPointer2(){
    o.editOption.text = o.editOption.text = o.editOption.text.substring(0,pos) + pointer + o.editOption.text.substring(pos+1,o.editOption.text.length)
    blinkDelay = setTimeout("blinkPointer()", 300);
}

function stopBlinking(){
    clearTimeout(blinkDelay);
    if(o.editOption.text.charAt(pos) != pointer)
	o.editOption.text = o.editOption.text = o.editOption.text.substring(0,pos) + pointer + o.editOption.text.substring(pos+1,o.editOption.text.length)
}

function finishEditing() {
    if(o != null) {
	stopBlinking();
	option = o.editOption;
	option.text = option.text.substring(0,option.text.indexOf(pointer)) + option.text.substring(option.text.indexOf(pointer)+1,option.text.length);

	option.value = option.text;
	document.onkeypress = null;
	document.onkeydown = null;
	o = null;
    }
}






function toggleGroupedAction(id) {
    var div= $(id);
    toggleSubGroupedAction();
    if (div.style.display == 'none')
	Effect.SlideDown(div,{
	    duration:0.2,
	    queue: 'end'
	});
    else Effect.SlideUp(div,{
	duration:0.5,
	queue: 'end'
    }) ;
    return;
}

function toggleSubGroupedAction(id) {
    var listAll = $$('#groupedSubAction fieldset');
    for(var j=0;j<listAll.length;j++)
	listAll[j].style.display = 'none';
    if($(id) != undefined)
	$(id).style.display = 'block';
    return;
}

function submitGroupAction(idForm,idAction) {
    $(idAction).value = "groupedAction";
    $(idForm).submit();
    return;
}

function makeReplace(recherche, module){
    $('champsRecherche').value = recherche;
    if(module == 'entreprise'){
	document.forms["searchProspec"].type[0].checked = true;
    }
    if(module == 'contact'){
	document.forms["searchProspec"].type[1].checked = true;
    }
    zuno.business.formTools.sendFormAjah('searchProspec', 'prospec/Recherche.php','listeResultProspec');

}

function viderCloud(module, user){

    zuno.ajax.post.json('../ajaxRef.php?action=suppCloud', 'module='+module+'&user='+user, function(xhr){
	if(xhr.status == 200){
	    var json = xhr.responseText.evalJSON();
	    if(json.error == 'false'){
		$('tags').innerHTML = "<ul><li>Cette zone se remplira au fur et à mesure de vos recherches</li></ul>";
	    }
	}
        
    });
}

