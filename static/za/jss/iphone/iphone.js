	function tabs(s) {
				WA.Header(!s, "tab1");
				return false;
			}

			function $(i){return typeof i=="string"?document.getElementById(i):i}
			function $$(t,o){return(o||document).getElementsByTagName(t)}

		
		
		
// *************************************************************************************************

addEventListener("load", function(event)
{
    var aDiv = $('authentificationToken');
	var aInput = $$('input',aDiv);
	var token = aInput[0].value;
	if(token == 'yes') return true;
	else
	{
		var urlFrom = window.location.href;
		if(urlFrom.indexOf("#")==-1)
		{
			// rien a faire, on est sur le calque par defaut 
			// alert("pas de lien interne");
		}
		else
		{
			var urlTo = urlFrom.substr(0,urlFrom.indexOf("#"));
			alert("Votre session est expirée. Vous allez être redirigé vers la fenêtre de connexion.");
			window.location = urlTo;
		}
		
	}
}, false);

//WA.AddEventListener("orientationchange", function(event)
//{
//    var img = $('homePageHeaderImg');
//	if(event.windowWidth == 396) img.src='Img/zunoHomeL.png';
//	else						     img.src='Img/zunoHomeP.png';
//} );


dynAjax = function(inputId, min, formToSubmit)
{
	var timer;
	input = $(inputId);
	input.addEventListener("keyup", timeoutFormSubmit,true);
	var e= null;
	min = min-1;
	
	function timeoutFormSubmit(event)
	{
		e = event;
		if (timer) clearTimeout(timer);
		if (input.value.length >= min) timer = setTimeout(reload, 1000);
	}

	function reload()
	{
		return WA.Submit(formToSubmit,null,e);
	}
}

// Fonction pour supprimer un element de l'arbre DOM
// utile pour la suppression des boutons iMore dans les listes de resultats
function removeElementFromDom(idName)
{
	var node = document.getElementById(idName);
	if (node != null) {
	var parent = node.parentNode;
	parent.removeChild(node);}
}

// Enregistrement d'un handler pour la fin des transitions 
// de WebApp permettant de lancer le changement de l'arrière plan
// du header en fonction de la rubrique

function getStyleForTargetLayer(targetId)
{
	var css = '';
	if(targetId)
	{
		if(targetId == "waMenuContact" || 
		   targetId == "waNewContact" ||
		   targetId == "waSearchContactPers" ||
		   targetId == "waContactSearchResult" ||
		   targetId == "waContactFichePart" ||
		   targetId == "waContactPart" ||
		   targetId == "waContactDeletePart" || 
		   targetId == "waContactPartModif" ||
		   targetId == "waContactPartAdd" ||
		   targetId == "waContactPartAffaire" || 
		   targetId == "waContactPartDevis" ||
		   targetId == "waContactPartCommande" ||
		   targetId == "waContactPartFacture" ) {
				css =  "Contact"; }
		else if(targetId == "waSearchContactEnt" ||
		   targetId == "waContactFicheEnt" ||
		   targetId == "waContactEnt" ||
		   targetId == "waContactDeleteEnt" ||
		   targetId == "waContactEntModif" || 
		   targetId == "waContactEntAdd" ||
		   targetId == "waContactEntAffaire" ||
		   targetId == "waContactEntDevis" ||
		   targetId == "waContactEntCommande" ||
		   targetId == "waContactEntFacture" ) {
				css =  "Entreprise"; }
		else if(targetId == "waMenuAffaire" ||
				targetId == "waSearchAffaire" ||
				targetId == "waAffaireSearchResult" ||
				targetId == "waActualiteAffaire" ||
				targetId == "waAffaireFiche" ||
				targetId == "waAffaireModif" ||
				targetId == "waAffaireCloner" ||
				targetId == "waAffaireAction" ||
				targetId == "waAffaireDelete" ||
				targetId == "waAffaireAdd" ||
				targetId == "waAffaireFormAvance" ||
				targetId == "waAffaireResultAvance" ||
				targetId == "waAffaireStats" ) { 
				css =  "Affaire"; }
		else if(targetId == "waMenuDevis" ||
				targetId == "waSearchDevis" ||
				targetId == "waDevisSearchResult" ||
				targetId == "waActualiteDevis" ||
				targetId == "waDevisFiche" ||
				targetId == "waDevisProduits" ||
				targetId == "waModifProduits" ||
				targetId == "waDevisModif" ||
				targetId == "waDevisDelete" ||
				targetId == "waDevisAdd" ||
				targetId == "waDevisAddPlus" ||
				targetId == "waDevisAction" ||
				targetId == "waDevisAction1" ||
				targetId == "waDevisAddExpress" ||
				targetId == "waDevisAddExpressSuite" ||
				targetId == "waModifProduitsDevis" ||
				targetId == "waAddProduitsDevis" ||
				targetId == "waStatsDevis" ) {
				css =  "Devis"; }
		else if(targetId == "waMenuCommande" ||
				targetId == "waSearchCommande" ||
				targetId == "waCommandeSearchResult" ||
				targetId == "waActualiteCommande" ||
				targetId == "waCommandeFiche" ||
				targetId == "waCommandeProduits" ||
				targetId == "waCommandeModif" ||
				targetId == "waCommandeDelete" ||
				targetId == "waCommandeAdd" ||
				targetId == "waCommandeAction" ||
				targetId == "waCommandeAction1" ||
				targetId == "waCommandeCloner" ||
				targetId == "waModifProduitsCommande" ||
				targetId == "waAddProduitsCommande" ||
				targetId == "waStatsCommande" ) {
				css =  "Commande"; }
		else if(targetId == "waMenuProduit" ||
				targetId == "waSearchProduit" ||
				targetId == "waSearchFournisseur" ||
				targetId == "waProduitSearchResult" ||
				targetId == "waFournisseurSearchResult" ||
				targetId == "waProduitFiche" ||
				targetId == "waFournisseurFiche" ||
				targetId == "waProduitModif" ||
				targetId == "waProduitDelete" ||
				targetId == "waProduitAdd" ||
				targetId == "waDellFourn" ||
				targetId == "waAddFourn" ||
				targetId == "waActifFourn" ||
				targetId == "ContactNewFourn" ||
				targetId == "waFournAdd" ) {
				css =  "Produit"; }
		else if(targetId == "waMenuFacture" ||
				targetId == "waSearchFacture" ||
				targetId == "waFactureSearchResult" ||
				targetId == "waActualiteFacture" ||
				targetId == "waFactureFiche" ||
				targetId == "waFactureProduits" ||
				targetId == "waFactureModif" ||
				targetId == "waFactureDelete" ||
				targetId == "waFactureAdd" ||
				targetId == "waFactureAction" ||
				targetId == "waFactureAction1" ||
				targetId == "waFactureCloner" ||
				targetId == "waModifProduitsFacture" ||
				targetId == "waAddProduitsFacture" ||
				targetId == "waFactureAddExpress" ||
				targetId == "waFactureAddExpressSuite" ||
				targetId == "waStatsFacture" ) {
				css =  "Facture"; }
		else if(targetId == "waMenuActualite" || 
			    targetId == "waActualiteResult" || 
			    targetId == "waActualiteFiche" ||
			    targetId == "waActualiteAdd" ||
			    targetId == "waActualiteModif" ) {
				css =  "Actualite"; }
	}
	return css;
}

function changeHeader(evtObj)
{
	// on recupère le nom du layer cible
	var header = $("iHeader");
	var wal = $("WebApp");
	WA.lastDivUsedForHeader = evtObj.context['1'][0];
	if(WA.lastDivUsedForHeader) {
		var c = new String(getStyleForTargetLayer(WA.lastDivUsedForHeader));
		header.className = c;
		wal.className = c;
	}
}
function changeHeaderOrientation()
{
	if(!(WA.lastDivUsedForHeader == undefined)) {
		var c = new String(getStyleForTargetLayer(WA.lastDivUsedForHeader));
		$("iHeader").className = c;
		$("WebApp").className = c;
	}
}
WA.AddEventListener("endslide", changeHeader);
WA.AddEventListener("orientationchange", changeHeaderOrientation);





var listeActuTriDiv = new Array('AFTgeneral','AFTaffaire','AFTdevis','AFTcommande','AFTfacture','AFTall');

function switchActualiteTri(div)
{
	for(var i in listeActuTriDiv) {
  		var value = listeActuTriDiv[i];
  		if(value == div) $(value).className = 'select';
		else $(value).className = '';
	}
}

var listeAffaireTriDiv = new Array('AFTecheance','AFTcreation','AFTentreprise','AFTcontact');

function switchAffaireTri(div)
{
	for(var i in listeAffaireTriDiv) {
  		var value = listeAffaireTriDiv[i];
  		if(value == div) $(value).className = 'select';
		else $(value).className = '';
	}
}
var listeDevisTriDiv = new Array('DTmontant','DTcreation','DTentreprise','DTcontact');

function switchDevisTri(div)
{
	for(var i in listeDevisTriDiv) {
  		var value = listeDevisTriDiv[i];
  		if(value == div) $(value).className = 'select';
		else $(value).className = '';
	}
}
var listeCommandeTriDiv = new Array('CTmontant','CTcreation','CTentreprise','CTcontact');

function switchCommandeTri(div)
{
	for(var i in listeCommandeTriDiv) {
  		var value = listeCommandeTriDiv[i];
  		if(value == div) $(value).className = 'select';
		else $(value).className = '';
	}
}

var listeFactureTriDiv = new Array('FTmontant','FTcreation','FTentreprise','FTcontact');

function switchFactureTri(div)
{
	for(var i in listeFactureTriDiv) {
  		var value = listeFactureTriDiv[i];
  		if(value == div) $(value).className = 'select';
		else $(value).className = '';
	}
}

function chargeAuto(objet,lien)
{
objet.removeAttributeNode(objet.getAttributeNode("onClick"));
simulateClick(lien);
}

function returnAjaxInputResult(fieldsName,id,val)
{
		$(fieldsName+'AId').innerHTML = val;
		$(fieldsName+'InputId').value = id;
}

function returnAjaxInputResultProdNew(fieldsName,id,val)
{
		returnAjaxInputResult(fieldsName,id,val);
		var check = $('boxmemorize'+fieldsName);
		check.style.display = 'inline';
		try { simulateClick('lienajoutformDE');
		removeElementFromDom('lienajoutformDE'); }
		catch (e) { }
}

function returnAjaxInputResultProduit(fieldsName,id,val,desc, prix, V)
{
		if(V != 'V') { V= '';}
		try { simulateClick('lienajoutformDE');
		removeElementFromDom('lienajoutformDE'); }
		catch (e) { }
		returnAjaxInputResult(fieldsName,id,val);
		var lien = $(fieldsName+'AId');
		var href = lien.getAttribute("href");
		href = href.replace('DE','');
		lien.setAttribute("href",href);
		$(fieldsName+'desc').value = desc;
		$(fieldsName+'prix').value = prix;
		$(fieldsName+'quantite').value = '1';
		$(fieldsName+'remise'+V).value = '0';
		var check = $('boxmemorize'+fieldsName);
		check.style.display = 'none';
		removeElementFromDom('ErreurProd');
}
function returnAjaxInputResultProduitExpress(fieldsName,id,val,desc, prix, nombre, tva)
{
		var i =1; var HT = 0;
		tva = parseFloat(tva);
		returnAjaxInputResultProduit(fieldsName,id,val,desc, prix, '');
		$('sstotal'+fieldsName).innerHTML = prix;
		totalOnDevisExpress(nombre, tva, fieldsName, 'i');
}
function totalOnDevisExpress(nombre, tva, fieldsName, aveci)
{
	var result = fieldsName.match('id_produitFactureExpress');
	if(result != null)
	{var partie = 'Facture'; var div = 'DEProdFacture';}
	else {var partie = 'Devis';var div = 'DEProdDevis';}
	var ladiv = $(div);
	if(ladiv.hasChildNodes())
	{
		var listChild = ladiv.childNodes;
		if(listChild.length > 1)
		{
			nombre = 0;
			var tempo = 0;
			while (tempo < listChild.length)
			{
				if(ladiv.childNodes[tempo].nodeName == 'FIELDSET')
				{nombre++;}
				tempo++;
			}
		}
	}
	if(aveci == 'i')
	{
		fieldsName = 'id_produit'+partie+'Express';
	}
	var i =1; var HT = 0;
	var valtva; var ttc = 0;
	while( i <= nombre)
		{
			HT += parseFloat($('sstotal'+fieldsName+i).innerHTML);
			i++;
		}
		valtva = tva/100*HT;
		ttc = (1+tva/100)*HT;
		valtva=(Math.round(valtva*100)/100);
		valtva=valtva.toFixed(2);
		ttc=(Math.round(ttc*100)/100);
		ttc=ttc.toFixed(2);
		$('ht'+partie+'Express').innerHTML = 'Total HT : '+HT+' €';
		$('tva'+partie+'Express').innerHTML = 'TVA : '+tva+'% ('+valtva+' €)';
		$('ttc'+partie+'Express').innerHTML = 'Total TTC : '+ttc+' €';
}
function quantiteOnDevisExpress(qtt, nombre, numero, tva)
{
	var temp = qtt*$('id_produitDevisExpress'+numero+'prix').value*(1-$('id_produitDevisExpress'+numero+'remise').value/100);
	temp=(Math.round(temp*100)/100);
	temp=temp.toFixed(2);
	$('sstotalid_produitDevisExpress'+numero).innerHTML = temp;
	totalOnDevisExpress(nombre, tva, 'id_produitDevisExpress');
}
function quantiteOnFactureExpress(qtt, nombre, numero, tva)
{
	var temp = qtt*$('id_produitFactureExpress'+numero+'prix').value*(1-$('id_produitFactureExpress'+numero+'remise').value/100);
	temp=(Math.round(temp*100)/100);
	temp=temp.toFixed(2);
	$('sstotalid_produitFactureExpress'+numero).innerHTML = temp;
	totalOnDevisExpress(nombre, tva, 'id_produitFactureExpress');
}
function remiseOnDevisExpress(rem, nombre, numero, tva)
{
	var temp = $('id_produitDevisExpress'+numero+'quantite').value*$('id_produitDevisExpress'+numero+'prix').value*(1-rem/100);
	temp=(Math.round(temp*100)/100);
	temp=temp.toFixed(2);
	$('sstotalid_produitDevisExpress'+numero).innerHTML = temp;
	totalOnDevisExpress(nombre, tva, 'id_produitDevisExpress');
}
function remiseOnFactureExpress(rem, nombre, numero, tva)
{
	var temp = $('id_produitFactureExpress'+numero+'quantite').value*$('id_produitFactureExpress'+numero+'prix').value*(1-rem/100);
	temp=(Math.round(temp*100)/100);
	temp=temp.toFixed(2);
	$('sstotalid_produitFactureExpress'+numero).innerHTML = temp;
	totalOnDevisExpress(nombre, tva, 'id_produitFactureExpress');
}
function prixOnDevisExpress(prix, nombre, numero, tva)
{
	var temp = $('id_produitDevisExpress'+numero+'quantite').value*prix*(1-$('id_produitDevisExpress'+numero+'remise').value/100);
	temp=(Math.round(temp*100)/100);
	temp=temp.toFixed(2);
	$('sstotalid_produitDevisExpress'+numero).innerHTML = temp;
	totalOnDevisExpress(nombre, tva, 'id_produitDevisExpress');
}
function prixOnFactureExpress(prix, nombre, numero, tva)
{
	var temp = $('id_produitFactureExpress'+numero+'quantite').value*prix*(1-$('id_produitFactureExpress'+numero+'remise').value/100);
	temp=(Math.round(temp*100)/100);
	temp=temp.toFixed(2);
	$('sstotalid_produitFactureExpress'+numero).innerHTML = temp;
	totalOnDevisExpress(nombre, tva, 'id_produitFactureExpress');
}
function updateComboBox (idSelect,data,selectedKey) 
{
	try {
	var monSelect = document.getElementById(idSelect);
	monSelect.options.length = 0;
	var selected;
	i=0;
	for (var key in data) {
		// permet de choisir le champs à definir par defaut
		if (selectedKey == key) {
			selected = i;
		}
		monSelect.options[monSelect.length] = new Option(data[key],key);
		i++;
	}
	//permet de positionner le select sur la bonne option
	monSelect.selectedIndex = selected;
	var out = monSelect.options[0].value;
	return out;
	} catch(err) {return 0;}
}

function returnAjaxInputResultProduitCommande(fieldsName,id,val,desc, prix)
{
		var qtt = $(fieldsName+'quantite').value;
		if (qtt == '') {qtt = 1;} 
		var remiseV = $(fieldsName+'remiseV').value;
		var prixV = prix;
		returnAjaxInputResultProduit(fieldsName,id,val,desc, prix, 'V');
		var selectval = updateComboBox(fieldsName+'fourn', afffourn[id], 1);
		try{
			var remise = fournisseur[id][selectval]; 
			prix = fournisseur[id][selectval+'P'];
			$(fieldsName+'prixF').innerHTML = "Px F : "+prix+" &euro;";
			$(fieldsName+'prixF_hidden').value = prix;
			$(fieldsName+'remiseF').value = remise;
			}
		catch(err){remise = 0;}
		var totalF = qtt*prix*(1-remise/100);
		var totalV = qtt*prixV*(1-remiseV/100);
		var marge = totalV-totalF;
		totalF = Math.round((totalF*100)/100);
		totalF =totalF.toFixed(2);
		totalV = Math.round((totalV*100)/100);
		totalV =totalV.toFixed(2);
		marge = Math.round((marge*100)/100);
		marge =marge.toFixed(2);
		$(fieldsName+'totalV').innerHTML = 'TT V : '+totalV+' &euro;';
		try{
			$(fieldsName+'totalF').innerHTML = 'TT F : '+totalF+' &euro;';
			$(fieldsName+'marge').innerHTML = 'Marge : '+marge+' &euro;';
			}
		catch(err){}
		var test = $('supprimer_produit');
		if(test != null)
		{$('supprimer_produit').style.display = 'none';}
}
function qttoncommand(fieldsName, quantite)
{
		var totalV = qttonfacture(fieldsName, quantite);
		var prixF = $(fieldsName+'prixF').innerHTML;
		var taillepf = prixF.length;
		prixF = prixF.substring(7,taillepf-2);
		var remiseF = $(fieldsName+'remiseF').value;
		var totalF = quantite*prixF*(1-remiseF/100);
		var marge = totalV-totalF;
		totalF = Math.round((totalF*100)/100);
		totalF=totalF.toFixed(2);
		marge = Math.round((marge*100)/100);
		marge=marge.toFixed(2);
		$(fieldsName+'totalF').innerHTML = 'TT F : '+totalF+' &euro;';
		$(fieldsName+'marge').innerHTML = 'Marge : '+marge+' &euro;';
}
function qttonfacture(fieldsName, quantite, signe)
{
		var prixV = $(fieldsName+'prix').value;
		var remiseV = $(fieldsName+'remiseV').value;
		var totalV = quantite*prixV*(1-remiseV/100);
		totalV = Math.round((totalV*100)/100);
		totalV=totalV.toFixed(2);
		$(fieldsName+'totalV').innerHTML = 'TT V : '+signe+totalV+' &euro;';
		return totalV;
}

function fournoncommand(fieldsName, fourn)
{
	var idp = $(fieldsName+'InputId').value;
	var remise = fournisseur[idp][fourn];
	var prix = fournisseur[idp][fourn+'P'];
	var quantite = $(fieldsName+'quantite').value;
	var totalF = prix*(1-remise/100)*quantite;
	var totalV = $(fieldsName+'totalV').innerHTML;
	var tailletv = totalV.length;
	totalV = totalV.substring(7,tailletv-2);
	var marge = totalV-totalF;
	totalF = Math.round((totalF*100)/100);
	totalF=totalF.toFixed(2);
	marge = Math.round((marge*100)/100);
	marge=marge.toFixed(2);
	$(fieldsName+'totalF').innerHTML = 'TT F : '+totalF+' &euro;';
	$(fieldsName+'marge').innerHTML = 'Marge : '+marge+' &euro;';
	$(fieldsName+'remiseF').value = remise;
	$(fieldsName+'prixF').innerHTML = "Px F : "+prix+" &euro;";
	$(fieldsName+'prixF_hidden').value = prix;
}
function remisefoncommand(fieldsName, remiseF)
{
	var idp = $(fieldsName+'InputId').value;
	var quantite = $(fieldsName+'quantite').value;
	var prixF = $(fieldsName+'prixF').innerHTML;
	var taillepf = prixF.length;
	prixF = prixF.substring(7,taillepf-2); 
	var totalF = prixF*(1-remiseF/100)*quantite;
	var totalV = $(fieldsName+'totalV').innerHTML;
	var tailletv = totalV.length;
	totalV = totalV.substring(7,tailletv-2);
	var marge = totalV-totalF;
	totalF = Math.round((totalF*100)/100);
	totalF=totalF.toFixed(2);
	marge = Math.round((marge*100)/100);
	marge=marge.toFixed(2);
	$(fieldsName+'totalF').innerHTML = 'TT F : '+totalF+' &euro;';
	$(fieldsName+'marge').innerHTML = 'Marge : '+marge+' &euro;';
}
function remisevoncommand(fieldsName, remiseV)
{
	var totalV = remisevonfacture(fieldsName, remiseV);
	var idp = $(fieldsName+'InputId').value;
	var totalF = $(fieldsName+'totalF').innerHTML;
	var tailletf = totalF.length;
	totalF = totalF.substring(7,tailletf-2);
	var marge = totalV-totalF;
	marge = Math.round((marge*100)/100);
	marge=marge.toFixed(2);
	$(fieldsName+'marge').innerHTML = 'Marge : '+marge+' &euro;';
}
function remisevonfacture(fieldsName, remiseV, signe)
{
	if(typeof(signe) == 'undefined')
	{
		signe = '';
	}
	var quantite = $(fieldsName+'quantite').value;
	var prixV = $(fieldsName+'prix').value;
	var totalV = quantite*prixV*(1-remiseV/100);
	totalV = Math.round((totalV*100)/100);
	totalV=totalV.toFixed(2);
	$(fieldsName+'totalV').innerHTML = 'TT V : '+signe+totalV+' &euro;';
	return totalV;
}
function prixvoncommand(fieldsName, prixV)
{
	var totalV = prixvonfacture(fieldsName, prixV);
	var totalF = $(fieldsName+'totalF').innerHTML;
	var tailletf = totalF.length;
	totalF = totalF.substring(7,tailletf-2);
	var marge = totalV-totalF;
	marge = Math.round((marge*100)/100);
	marge=marge.toFixed(2);
	$(fieldsName+'marge').innerHTML = 'Marge : '+marge+' &euro;';
}
function prixvonfacture(fieldsName, prixV, signe)
{
	if(typeof(signe) == 'undefined')
	{
		signe = '';
	}
	var quantite = $(fieldsName+'quantite').value;
	var remiseV = $(fieldsName+'remiseV').value;
	var totalV = quantite*prixV*(1-remiseV/100);
	totalV = Math.round((totalV*100)/100);
	totalV=totalV.toFixed(2);
	$(fieldsName+'totalV').innerHTML = 'TT V : '+signe+totalV+' &euro;';
	return totalV;
}

function TextAreaCountLines(strtocount, cols) {
    var hard_lines = 1;
    var last = 0;
    while ( true ) {
        last = strtocount.indexOf("\n", last+1);
        hard_lines ++;
        if ( last == -1 ) break;
    }
    var soft_lines = Math.round(strtocount.length / (cols-1));
    var hard = eval("hard_lines  " + unescape("%3e") + "soft_lines;");
    if ( hard ) soft_lines = hard_lines;
    return soft_lines;
}
function TextAreaAutoResize(id) {
    var f = $(id);
    for ( var x in f ) {
        if ( ! f[x] ) continue;
        if( typeof f[x].rows != "number" ) continue;
        f[x].rows = TextAreaCountLines(f[x].value,f[x].cols) +1;
    }
    setTimeout("TextAreaAutoResize(\'"+id+"\');", 300);
}


// Fonction utilisée pour un print_r() en javascript
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

function commandChangePrice(idp, fourn)
{
var remise = fournisseur[idp][fourn];
var prix = fournisseur[idp][fourn+"P"];
var total = $("QuantiteCommande"+idp).value*(1-remise/100)*prix;
$("TotalCommande"+idp).innerHTML = "Total : "+total+" &euro;";
$("PrixCommande"+idp).innerHTML = "Prix : "+prix+" &euro;";
$("RemiseCommande"+idp).value = remise;
$("PrixCommande"+idp+"hidden").value = prix;
}
function commandChangeQtt(idp, qtt)
{
	var remise = $("RemiseCommande"+idp).value;
	var fourn = $("FournisseurCommande"+idp).value;
	var total = qtt*(1-remise/100)*fournisseur[idp][fourn+"P"];
	$("TotalCommande"+idp).innerHTML = "Total : "+total+" &euro;";
}
function commandChangeRemise(idp, remise)
{
if((isNaN(remise))||(remise < 0)||(remise > 100))
	{
	alert("Veuillez entrer une valeur comprise entre 0 et 100");
	remise = 0;
	$("RemiseCommande"+idp).value = remise;
	}
var fourn = $("FournisseurCommande"+idp).value;
var total = $("QuantiteCommande"+idp).value*(1-remise/100)*fournisseur[idp][fourn+"P"];
$("TotalCommande"+idp).innerHTML = "Total : "+total+" &euro;";
}
function factureChangeRemise(idp, remise)
{
if((isNaN(remise))||(remise < 0)||(remise > 100))
	{
	alert("Veuillez entrer une valeur comprise entre 0 et 100");
	remise = 0;
	$("RemiseFacture"+idp).value = remise;
	}
var total = totalBrut[idp]*(1-remise/100);
$("TotalFacture"+idp).innerHTML = "Total : "+total+" &euro;";
}

function simulateClick(id)
{
  var evt = document.createEvent("MouseEvents");
  evt.initMouseEvent("click", true, false, window,
    0, 0, 0, 0, 0, false, false, false, false, 0, null);
  var cb = document.getElementById(id); 
  cb.dispatchEvent(evt);
}
function confirmBeforeClickSimple(id, action)
{
	if(confirm('Confirmer '+action+' du produit ?'))
	{
		simulateClick(id);
	}
}
function confirmBeforeClick(id, produit, partie)
{
	if(confirm('Confirmer la suppression ?'))
	{
	$("id_produit_hidden_"+partie).value = produit;
	simulateClick(id+partie);
	}
	else{return false;}
}
function confirmBeforeClickBis(id, fournisseur)
{
	if(confirm('Confirmer la suppression ?'))
	{
	$(id+"hidden").value = fournisseur;
	simulateClick(id);
	}
}
function confirmBeforeClickFourn(id, nb)
{
	if(confirm('Ce fournisseur est lié à '+nb+' produits. Le désactiver ?'))
	{
	simulateClick(id);
	}
}
function affichernomfourn(value, N)
{
	$("nomfournisseurajout"+N).innerHTML = fourn[value];
	$("nomfournisseurajout"+N).style.display = 'block';
	if(N == 'New')
	{
		$("id_ent_hidden").value = value;
		simulateClick("addNewFournCont");
		$("fieldsetContactAddFourn").style.display = 'block';
	}
	else
	{
		$("remisefournisseurproduit").value = remise[value];
	}
}
function affichertotal(quoi, combien)
{
	var remise; var prix; var total;
	if(quoi == 'prix')
	{prix = combien;remise = $("remisefournisseurproduit").value;}
	if(quoi == 'remise')
	{remise = combien;prix = $("prixfournisseurproduit").value;}
	total = prix*(100-remise)/100;
	total = Math.round(total*100)/100;
	total = total.toFixed(2);
	$("totalfournisseurajout").innerHTML = 'Total unitaire : '+total+' €';
}
function modifFamille(valEnt)
{
	$("famille_hidden_produit_add").value = valEnt;
	var script = $("scriptFamille");
	try {
		$("waProduitAdd").removeChild(script);
		} catch(err) {script = null;}
	simulateClick("valid_familleProduitAdd");
}
function doModifFamille()
{
	$("id_famille").value = null;
	montrerCroix($("familleProduitAdd"), 'label');
	var val = $("familleProduitAdd").value;
	val = val.toUpperCase();
	var expression = new RegExp("^"+val);
	$("propositionFamilleJS").innerHTML = '';
	$("propositionFamille2JS").innerHTML = '';
	$("propositionFamille3JS").innerHTML = '';
	$("propositionFamille4JS").innerHTML = '';
	$("propositionFamille5JS").innerHTML = '';
	$("propositionFamilleJS").style.display = 'none';
	$("propositionFamille2JS").style.display = 'none';
	$("propositionFamille3JS").style.display = 'none';
	$("propositionFamille4JS").style.display = 'none';
	$("propositionFamille5JS").style.display = 'none';
	if(val != '')
		{
		var j; var k = 1;
       for (var i=1; i<famille.length; i++)                                                                            
       {                                                                                                                  
               if( k == 1)
               { j = '';}
               else { j = k;}
               if(j > 5)
               {break;}
               tempo = famille[i]["nom_prodfam"].toUpperCase();                                                            
               result = tempo.match(expression);                                                                          
               if (result != null)                                                                                        
               {$("propositionFamille"+j+"JS").innerHTML = famille[i]["nom_prodfam"];       
               $("propositionFamille"+j+"JS").style.display = 'block';                                                      
               $("propositionFamille"+j+"JS").style.backgroundColor = '#C0C0C0';
               k++;}                                           
               else{continue;}
       }
       }
}
function addFamilleAuto(id)
{
	for (var i=1; i<famille.length;i++)
	{
		if ($(id).innerHTML.toUpperCase() == famille[i]["nom_prodfam"].toUpperCase())
			{
				$("id_famille").value = famille[i]["id_prodfam"];
				k = 1;
				break;
			}
	}
	$("familleProduitAdd").value = $(id).innerHTML;
	$("propositionFamilleJS").innerHTML = '';
	$("propositionFamille2JS").innerHTML = '';
	$("propositionFamille3JS").innerHTML = '';
	$("propositionFamille4JS").innerHTML = '';
	$("propositionFamille5JS").innerHTML = '';
	$("propositionFamilleJS").style.display = 'none';
	$("propositionFamille2JS").style.display = 'none';
	$("propositionFamille3JS").style.display = 'none';
	$("propositionFamille4JS").style.display = 'none';
	$("propositionFamille5JS").style.display = 'none';
}
function modifEntrepriseDevisExpress(valEnt)
{
	$("entreprise_hidden_devis_express").value = valEnt;
	var script = $("scriptDevisExpress");
	try {
		$("waDevisAddExpress").removeChild(script);
		} catch(err) {script = null;}
	simulateClick("valid_entrepriseDevisExpress");
}

function modifEntrepriseFactureExpress(valEnt)
{
	$("entreprise_hidden_facture_express").value = valEnt;
	removeElementFromDom("scriptFactureExpress");
	simulateClick("valid_entrepriseFactureExpress");
}

function doModifEntrepriseDevisExpress(partie)
{
	var F;
	if(partie == 'Facture')
	{F = 'F';}
	else {F='';}
	var valEnt = $("entreprise"+partie+"Express").value;
	valEnt = valEnt.toUpperCase();
	var expression = new RegExp("^"+valEnt);
	var result;
	var tempo;
	$("id_ent"+partie+"Express").value = null;
	montrerCroix($("entreprise"+partie+"Express"), 'label');
	$("propositionEntreprise"+F+"JS").innerHTML = '';
	$("propositionEntreprise"+F+"2JS").innerHTML = '';
	$("propositionEntreprise"+F+"3JS").innerHTML = '';
	$("propositionEntreprise"+F+"4JS").innerHTML = '';
	$("propositionEntreprise"+F+"5JS").innerHTML = '';
	$("propositionEntreprise"+F+"JS").style.display = 'none';
	$("propositionEntreprise"+F+"2JS").style.display = 'none';
	$("propositionEntreprise"+F+"3JS").style.display = 'none';
	$("propositionEntreprise"+F+"4JS").style.display = 'none';
	$("propositionEntreprise"+F+"5JS").style.display = 'none';
	$("telEntreprise"+F+"JS").style.display='none';
	$("prenomContact"+F+"JS").style.display='none';
	$("civContact"+F+"JS").style.display='none';
	$("telContact"+F+"JS").style.display='none';
	if(valEnt != '')
		{
		var j; var k = 1;
       for (var i=1; i<entreprise.length; i++)                                                                            
       {                                                                                                                  
               if( k == 1)
               { j = '';}
               else { j = k;}
               if(j > 5)
               {break;}
               tempo = entreprise[i]["nom_ent"].toUpperCase();                                                            
               result = tempo.match(expression);                                                                          
               if (result != null)                                                                                        
               {$("propositionEntreprise"+F+j+"JS").innerHTML = entreprise[i]["nom_ent"]+' ('+entreprise[i]["cp_ent"]+')';       
               $("propositionEntreprise"+F+j+"JS").style.display = 'block';                                                      
               $("propositionEntreprise"+F+j+"JS").style.backgroundColor = '#C0C0C0';
               k++;}                                           
               else{continue;}
       }

		}
	if($("propositionEntreprise"+F+"JS").innerHTML == '')
	{
		$("telEntreprise"+F+"JS").style.display='block';
		$("prenomContact"+F+"JS").style.display='block';
		$("civContact"+F+"JS").style.display='block';
		$("telContact"+F+"JS").style.display='block';
	
	}
	
}
function doModifContactDevisExpress(partie)
{
	var F;
	if(partie == 'Facture')
	{F = 'F';}
	else {F='';}
	var valCont = $("contact"+partie+"Express").value;
	valCont = valCont.toUpperCase();
	var expression = new RegExp(valCont);
	var result;
	var tempo; var tempo2;
	$("id_cont"+partie+"Express").value = null;
	montrerCroix($("contact"+partie+"Express"), 'label');
	$("propositionContact"+F+"JS").innerHTML = '';
	$("propositionContact"+F+"2JS").innerHTML = '';
	$("propositionContact"+F+"3JS").innerHTML = '';
	$("propositionContact"+F+"4JS").innerHTML = '';
	$("propositionContact"+F+"5JS").innerHTML = '';
	$("propositionContact"+F+"JS").style.display = 'none';
	$("propositionContact"+F+"2JS").style.display = 'none';
	$("propositionContact"+F+"3JS").style.display = 'none';
	$("propositionContact"+F+"4JS").style.display = 'none';
	$("propositionContact"+F+"5JS").style.display = 'none';
	$("telContact"+F+"JS").style.display='none';
	$("prenomContact"+F+"JS").style.display='none';
	$("civContact"+F+"JS").style.display='none';
	$("telContact"+F+"JS").style.display='none';
	if(valCont != '')
		{
		var j; var k = 1;
       for (var i=1; i<contact.length; i++)                                                                            
       {                                                                                                                  
               if( k == 1)
               { j = '';}
               else { j = k;}
               if(j > 5)
               {break;}
               tempo = contact[i]["nom_cont"].toUpperCase();
               tempo2 = contact[i]["prenom_cont"].toUpperCase();                                                            
               result = tempo.match(expression);                                                                          
               if (result != null)                                                                                        
               {$("propositionContact"+F+j+"JS").innerHTML = contact[i]["prenom_cont"]+' '+contact[i]["nom_cont"];       
               $("propositionContact"+F+j+"JS").style.display = 'block';                                                      
               $("propositionContact"+F+j+"JS").style.backgroundColor = '#C0C0C0';
               $("idcontExpress"+F+j).innerHTML = contact[i]["id_cont"];
               k++;}                                           
               else
               {
               		result = tempo2.match(expression);                                                                          
              		if (result != null)                                                                                        
              		{$("propositionContact"+F+j+"JS").innerHTML = contact[i]["prenom_cont"]+' '+contact[i]["nom_cont"];       
               		$("propositionContact"+F+j+"JS").style.display = 'block';                                                      
               		$("propositionContact"+F+j+"JS").style.backgroundColor = '#C0C0C0';
               		$("idcontExpress"+F+j).innerHTML = contact[i]["id_cont"];
               		k++;}
               		else{continue;}
               }
       }

		}
	if($("propositionContact"+F+"JS").innerHTML == '')
	{
		$("telContact"+F+"JS").style.display='block';
		$("prenomContact"+F+"JS").style.display='block';
		$("civContact"+F+"JS").style.display='block';
		$("telContact"+F+"JS").style.display='block';
	
	}
	
}
function addEntrepriseAuto(id, partie)
{
	var k;
	var F;
	if(partie == 'Facture')
	{F = 'F';}
	else {F='';}
	$("telEntreprise"+F+"JS").style.display='none';
	$("prenomContact"+F+"JS").style.display='none';
	$("civContact"+F+"JS").style.display='none';
	$("telContact"+F+"JS").style.display='none';
	$("entreprise"+partie+"Express").value = $(id).innerHTML;
	$("propositionEntreprise"+F+"JS").style.display = 'none';
	$("propositionEntreprise"+F+"2JS").style.display = 'none';
	$("propositionEntreprise"+F+"3JS").style.display = 'none';
	$("propositionEntreprise"+F+"4JS").style.display = 'none';
	$("propositionEntreprise"+F+"5JS").style.display = 'none';
	$("add1"+partie+"Express").value = '';
	$("add2"+partie+"Express").value = '';
	$("cp"+partie+"Express").value = '';
	$("ville"+partie+"Express").value = '';
	$("pays"+partie+"Express").value = '1';
	$("contact"+partie+"Express").value = '';
	document.forms['formAdd'+partie+'Express'].listeContact.length = 0;
	for (var i=1; i<entreprise.length;i++)
	{
		if ($(id).innerHTML == entreprise[i]["nom_ent"]+' ('+entreprise[i]["cp_ent"]+')')
			{
				if(entreprise[i]["add1_ent"] != null)
				$("add1"+partie+"Express").value = entreprise[i]["add1_ent"];
				if(entreprise[i]["add2_ent"] != null)
				$("add2"+partie+"Express").value = entreprise[i]["add2_ent"];
				if(entreprise[i]["cp_ent"] != null)
				$("cp"+partie+"Express").value = entreprise[i]["cp_ent"];
				if(entreprise[i]["ville_ent"] != null)
				$("ville"+partie+"Express").value = entreprise[i]["ville_ent"];
				if(entreprise[i]["pays_ent"] != null)
				$("pays"+partie+"Express").value = entreprise[i]["pays_ent"];
				$("id_ent"+partie+"Express").value = entreprise[i]["id_ent"];
				k = 1;
				break;
			}
	}
}
function addContactAuto(contID, partie)
{
	var F;
	if(partie == 'Facture')
	{F = 'F';}
	else {F='';}
	contID = $("idcontExpress"+F+contID).innerHTML;
	$("propositionContact"+F+"JS").style.display = 'none';
	$("propositionContact"+F+"2JS").style.display = 'none';
	$("propositionContact"+F+"3JS").style.display = 'none';
	$("propositionContact"+F+"4JS").style.display = 'none';
	$("propositionContact"+F+"5JS").style.display = 'none';
	for (var i=1; i<contact.length; i++)
	{
		if(contact[i]["id_cont"] == contID)
		{
			$("contact"+partie+"Express").value = contact[i]["prenom_cont"]+' '+contact[i]["nom_cont"];
			if(contact[i]["mail_cont"] != null)
			$("mail"+partie+"Express").value = contact[i]["mail_cont"];
			$("prenomContact"+F+"JS").style.display='none';
			$("civContact"+F+"JS").style.display='none';
			$("telContact"+F+"JS").style.display='none';
			$("id_cont"+partie+"Express").value = contact[i]["id_cont"];
		}
	}
}
function cleanContactDevisExpress(nomCont, partie)
{
	var F;
	if(partie == 'Facture')
	{F = 'F';}
	else {F='';}
	montrerCroix($("contact"+partie+"Express"), 'label');
	$("prenomContact"+F+"JS").style.display='block';
	$("civContact"+F+"JS").style.display='block';
	$("telContact"+F+"JS").style.display='block';
}

function montrerCroix(objet, label)
{
	var li = objet.parentNode;
	var enfants = li.childNodes;
	var affichage;
	if( objet.value == null || objet.value == '')
	{affichage = 'none';}
	else{affichage = 'inline';}
	if( label == 'sans')
	{enfants[1].style.display=affichage;}
	if( label == 'label')
	{enfants[2].style.display=affichage;}
}
function croixEfface(objet, label)
{
	var li = objet.parentNode;
	var enfants = li.childNodes;
	if( label == 'sans')
	{enfants[0].value = '';
	objet.style.display='none';}
	if( label == 'label')
	{enfants[1].value = '';
	objet.style.display='none';}
}
function autosaveProfil(time)
{
	setTimeout("simulateClick('liendeautosaveProfil')",time);
}
function autosaveProfilBis(time, objet)
{
if(((objet.value != $(objet.name+"_pref_hidden").value) || ($(objet.name+"_pref_hidden").value == undefined)) && objet.value != '')
	{
		$(objet.name+"_pref_hidden").value = objet.value;
		autosaveProfil(time);
	}
if(objet.value == '')
{
	alert("Ce champ ne peut être vide !");
	objet.value = $(objet.name+"_pref_hidden").value;
}
}
function modifPrixProduit(prix, numero)
{
	 $("prixcalcule"+numero).innerHTML = 'Prix avec la remise : '+(Math.round(((100-$("remiseProdModif"+numero).value)/100 * prix)*100)/100).toFixed(2)+' €';
}
function modifRemiseProduit(remise, numero)
{
	 $("prixcalcule"+numero).innerHTML = 'Prix avec la remise : '+(Math.round(((100-remise)/100*$("prixProdModif"+numero).value)*100)/100).toFixed(2)+' €';
}




function ajoutepin(numero)
{
	pc = $("pinCode");
	pv = new String(pc.value);
	nm = new String(numero);
	c = pv.length;
	if(c < 4)
	{
		$("chiffre"+c).innerHTML = '<img src="Img/pin/x.png"/>';
		pc.value = pv+nm;
	}
}

function videpin()
{
	$("pinCode").value = '';
	for (x=0; x<4; x++)
		$("chiffre"+x).innerHTML = '&nbsp;';
}

function soumettrepin()
{
	pv = new String($("pinCode").value);
	if(pv.length == 4)
		simulateClick('lienactivationpin');
}

function autoVille(cp, id)
{
	$(id+"hidden").value = cp;
	simulateClick(id+"_valid");
}
function placerVille(value, id)
{
	$(id).value = value;
}


function orientation() 
{ 
    switch(window.orientation) { 
    case 0: 
        orient = "portrait"; 
        break; 
    case -90: 
        orient = "landscape"; 
        break; 
    case 90: 
        orient = "landscape"; 
        break; 
    case 180: 
        orient = "portrait"; 
        break; 
    } 
    return orient; 
}
function adaptTextNavigator(nombre)
{
var orient = orientation();alert(orient);
}
fournisseur = new Array();
totalBrut = new Array();
afffourn = new Array();
