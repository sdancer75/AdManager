/* Functions for the campsiteinternallink plugin popup */

tinyMCEPopup.requireLangPack();

var templates = {
    "window.open" : "window.open('${url}','${target}','${options}')"
};

function preinit() {
    var url;

    if (url = tinyMCEPopup.getParam("external_link_list_url"))
	document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
}

function changeClass() {
    var f = document.forms[0];

    f.classes.value = getSelectValue(f, 'classlist');
}

function init() {
    tinyMCEPopup.resizeToInnerSize();

    var formObj = document.forms[0];
    var inst = tinyMCEPopup.editor;
    var elm = inst.selection.getNode();
    var action = "insert";
    var html;

    document.getElementById('targetlistcontainer').innerHTML = getTargetListHTML('targetlist','target');

    elm = inst.dom.getParent(elm, "A");
    if (elm != null && elm.nodeName == "A")
	action = "update";

    formObj.insert.value = tinyMCEPopup.getLang(action, 'Insert', true);

    if (action == "update") {
	var href = inst.dom.getAttrib(elm, 'href');

	selectByValue(formObj, 'targetlist', inst.dom.getAttrib(elm, 'target'), true);
    }

    addClassesToList('classlist', 'campsiteinternallink_styles');
}

function checkPrefix(n) {
    if (n.value && Validator.isEmail(n) && !/^\s*mailto:/i.test(n.value) && confirm(tinyMCEPopup.getLang('campsiteinternallink_dlg.is_email')))
	n.value = 'mailto:' + n.value;

    if (/^\s*www./i.test(n.value) && confirm(tinyMCEPopup.getLang('campsiteinternallink_dlg.is_external')))
	n.value = 'http://' + n.value;
}

function setFormValue(name, value) {
    document.forms[0].elements[name].value = value;
}

function parseWindowOpen(onclick) {
    var formObj = document.forms[0];

    // Preprocess center code
    if (onclick.indexOf('return false;') != -1) {
	formObj.popupreturn.checked = true;
	onclick = onclick.replace('return false;', '');
    } else
	formObj.popupreturn.checked = false;

    var onClickData = parseLink(onclick);

    if (onClickData != null) {
	formObj.ispopup.checked = true;
	setPopupControlsDisabled(false);

	var onClickWindowOptions = parseOptions(onClickData['options']);
	var url = onClickData['url'];

	formObj.popupname.value = onClickData['target'];
	formObj.popupurl.value = url;
	formObj.popupwidth.value = getOption(onClickWindowOptions, 'width');
	formObj.popupheight.value = getOption(onClickWindowOptions, 'height');

	formObj.popupleft.value = getOption(onClickWindowOptions, 'left');
	formObj.popuptop.value = getOption(onClickWindowOptions, 'top');

	if (formObj.popupleft.value.indexOf('screen') != -1)
	    formObj.popupleft.value = "c";

	if (formObj.popuptop.value.indexOf('screen') != -1)
	    formObj.popuptop.value = "c";

	formObj.popuplocation.checked = getOption(onClickWindowOptions, 'location') == "yes";
	formObj.popupscrollbars.checked = getOption(onClickWindowOptions, 'scrollbars') == "yes";
	formObj.popupmenubar.checked = getOption(onClickWindowOptions, 'menubar') == "yes";
	formObj.popupresizable.checked = getOption(onClickWindowOptions, 'resizable') == "yes";
	formObj.popuptoolbar.checked = getOption(onClickWindowOptions, 'toolbar') == "yes";
	formObj.popupstatus.checked = getOption(onClickWindowOptions, 'status') == "yes";
	formObj.popupdependent.checked = getOption(onClickWindowOptions, 'dependent') == "yes";

	buildOnClick();
    }
}

function parseFunction(onclick) {
    var formObj = document.forms[0];
    var onClickData = parseLink(onclick);

    // TODO: Add stuff here
}

function getOption(opts, name) {
    return typeof(opts[name]) == "undefined" ? "" : opts[name];
}

function setPopupControlsDisabled(state) {
    var formObj = document.forms[0];

    formObj.popupname.disabled = state;
    formObj.popupurl.disabled = state;
    formObj.popupwidth.disabled = state;
    formObj.popupheight.disabled = state;
    formObj.popupleft.disabled = state;
    formObj.popuptop.disabled = state;
    formObj.popuplocation.disabled = state;
    formObj.popupscrollbars.disabled = state;
    formObj.popupmenubar.disabled = state;
    formObj.popupresizable.disabled = state;
    formObj.popuptoolbar.disabled = state;
    formObj.popupstatus.disabled = state;
    formObj.popupreturn.disabled = state;
    formObj.popupdependent.disabled = state;

    setBrowserDisabled('popupurlbrowser', state);
}


function getURLVar(urlVarName, href) {
    //divide the URL in half at the '?'
    var urlHalves = String(href).split('?');
    var urlVarValue = '';

    if(urlHalves[1]){
	//load all the name/value pairs into an array
	var urlVars = urlHalves[1].split('&');
	//loop over the list, and find the specified url variable
	for(i=0; i<=(urlVars.length); i++){
	    if(urlVars[i]){
		//load the name/value pair into an array
		var urlVarPair = urlVars[i].split('=');
		if (urlVarPair[0] && urlVarPair[0] == urlVarName) {
		    //I found a variable that matches, load it's value into the return variable
		    urlVarValue = urlVarPair[1];
		}
	    }
	}
    }
    return urlVarValue;
}


function parseLink(link) {
    link = link.replace(new RegExp('&#39;', 'g'), "'");

    var fnName = link.replace(new RegExp("\\s*([A-Za-z0-9\.]*)\\s*\\(.*", "gi"), "$1");

    // Is function name a template function
    var template = templates[fnName];
    if (template) {
	// Build regexp
	var variableNames = template.match(new RegExp("'?\\$\\{[A-Za-z0-9\.]*\\}'?", "gi"));
	var regExp = "\\s*[A-Za-z0-9\.]*\\s*\\(";
	var replaceStr = "";
	for (var i=0; i<variableNames.length; i++) {
	    // Is string value
	    if (variableNames[i].indexOf("'${") != -1)
		regExp += "'(.*)'";
	    else // Number value
		regExp += "([0-9]*)";

	    replaceStr += "$" + (i+1);

	    // Cleanup variable name
	    variableNames[i] = variableNames[i].replace(new RegExp("[^A-Za-z0-9]", "gi"), "");

	    if (i != variableNames.length-1) {
		regExp += "\\s*,\\s*";
		replaceStr += "<delim>";
	    } else
		regExp += ".*";
	}

	regExp += "\\);?";

	// Build variable array
	var variables = [];
	variables["_function"] = fnName;
	var variableValues = link.replace(new RegExp(regExp, "gi"), replaceStr).split('<delim>');
	for (var i=0; i<variableNames.length; i++)
	    variables[variableNames[i]] = variableValues[i];

	return variables;
    }

    return null;
}

function parseOptions(opts) {
    if (opts == null || opts == "")
	return [];

    // Cleanup the options
    opts = opts.toLowerCase();
    opts = opts.replace(/;/g, ",");
    opts = opts.replace(/[^0-9a-z=,]/g, "");

    var optionChunks = opts.split(',');
    var options = [];

    for (var i=0; i<optionChunks.length; i++) {
	var parts = optionChunks[i].split('=');

	if (parts.length == 2)
	    options[parts[0]] = parts[1];
    }

    return options;
}

function buildOnClick() {
    var formObj = document.forms[0];

    if (!formObj.ispopup.checked) {
	formObj.onclick.value = "";
	return;
    }

    var onclick = "window.open('";
    var url = formObj.popupurl.value;

    onclick += url + "','";
    onclick += formObj.popupname.value + "','";

    if (formObj.popuplocation.checked)
	onclick += "location=yes,";

    if (formObj.popupscrollbars.checked)
	onclick += "scrollbars=yes,";

    if (formObj.popupmenubar.checked)
	onclick += "menubar=yes,";

    if (formObj.popupresizable.checked)
	onclick += "resizable=yes,";

    if (formObj.popuptoolbar.checked)
	onclick += "toolbar=yes,";

    if (formObj.popupstatus.checked)
	onclick += "status=yes,";

    if (formObj.popupdependent.checked)
	onclick += "dependent=yes,";

    if (formObj.popupwidth.value != "")
	onclick += "width=" + formObj.popupwidth.value + ",";

    if (formObj.popupheight.value != "")
	onclick += "height=" + formObj.popupheight.value + ",";

    if (formObj.popupleft.value != "") {
	if (formObj.popupleft.value != "c")
	    onclick += "left=" + formObj.popupleft.value + ",";
	else
	    onclick += "left='+(screen.availWidth/2-" + (formObj.popupwidth.value/2) + ")+',";
    }

    if (formObj.popuptop.value != "") {
	if (formObj.popuptop.value != "c")
	    onclick += "top=" + formObj.popuptop.value + ",";
	else
	    onclick += "top='+(screen.availHeight/2-" + (formObj.popupheight.value/2) + ")+',";
    }

    if (onclick.charAt(onclick.length-1) == ',')
	onclick = onclick.substring(0, onclick.length-1);

    onclick += "');";

    if (formObj.popupreturn.checked)
	onclick += "return false;";

    // tinyMCE.debug(onclick);

    formObj.onclick.value = onclick;

    if (formObj.href.value == "")
	formObj.href.value = url;
}

function setAttrib(elm, attrib, value) {
    var formObj = document.forms[0];
    var valueElm = formObj.elements[attrib.toLowerCase()];
    var dom = tinyMCEPopup.editor.dom;

    if (typeof(value) == "undefined" || value == null) {
	value = "";

	if (valueElm)
	    value = valueElm.value;
    }

    // Clean up the style
    if (attrib == 'style')
	value = dom.serializeStyle(dom.parseStyle(value));

    dom.setAttrib(elm, attrib, value);
}

function getAnchorListHTML(id, target) {
    var inst = tinyMCEPopup.editor;
    var nodes = inst.dom.select('a.mceItemAnchor,img.mceItemAnchor'), name, i;
    var html = "";

    html += '<select id="' + id + '" name="' + id + '" class="mceAnchorList" o2nfocus="tinyMCE.addSelectAccessibility(event, this, window);" onchange="this.form.' + target + '.value=';
    html += 'this.options[this.selectedIndex].value;">';
    html += '<option value="">---</option>';

    for (i=0; i<nodes.length; i++) {
	if ((name = inst.dom.getAttrib(nodes[i], "name")) != "")
	    html += '<option value="#' + name + '">' + name + '</option>';
    }

    html += '</select>';

    return html;
}

function insertAction() {
    var inst = tinyMCEPopup.editor;
    var elm, elementArray, i;

    languageId = document.getElementById("IdLanguage").value;
    publicationElement = document.getElementById("IdPublication");
    publicationId = publicationElement ? publicationElement.value : 0;
    issueElement = document.getElementById("NrIssue");
    issueId = issueElement ? issueElement.value : 0;
    sectionElement = document.getElementById("NrSection");
    sectionId = sectionElement ? sectionElement.value : 0;
    articleElement = document.getElementById("NrArticle");
    articleId = articleElement? articleElement.value : 0;
    targetElement = document.getElementById("targetlist");
    target = targetElement ? targetElement.value : '';

    // User must at least specify language and publication.
    if ((languageId <= 0) || (publicationId <= 0)) {
	alert("You must specify the language and the publication.");
	return false;
    }

    // Pass data back to the calling window.
    var param = new Object();
    param["f_href"] = "/campsite/campsite_internal_link?IdPublication="+publicationId
	+"&IdLanguage="+languageId;
    if (issueId > 0) {
	param["f_href"] += "&NrIssue=" + issueId;
    }
    if (sectionId > 0) {
	param["f_href"] += "&NrSection=" + sectionId;
    }
    if (articleId > 0) {
	param["f_href"] += "&NrArticle=" + articleId;
    }
    if (target != '') {
	if (target == "_other") {
	    param["f_target"] = document.getElementById("f_other_target").value;
	}
	else {
	    param["f_target"] = target;
	}
    }
    else {
	param["f_target"] = "";
    }
    param["f_title"] = "";

    elm = inst.dom.getParent(inst.selection.getNode(), "A");

    tinyMCEPopup.execCommand("mceBeginUndoLevel");

    // Create new anchor elements
    if (elm == null) {
	tinyMCEPopup.execCommand("CreateLink", false, "#mce_temp_url#", {skip_undo : 1});
	elementArray = tinymce.grep(inst.dom.select("a"), function(n) {return inst.dom.getAttrib(n, 'href') == '#mce_temp_url#';});
	for (i=0; i<elementArray.length; i++)
	    setAllAttribs(elm = elementArray[i]);
    }
    setAllAttribs(elm, param["f_href"]);

    tinyMCEPopup.execCommand("mceEndUndoLevel");
    tinyMCEPopup.close();
}

function setAllAttribs(elm, href) {
    var formObj = document.forms[0];
    var target = getSelectValue(formObj, 'targetlist');

    setAttrib(elm, 'href', href);
    setAttrib(elm, 'mce_href', href);
    setAttrib(elm, 'title');
    setAttrib(elm, 'target', target == '_self' ? '' : target);
    setAttrib(elm, 'id');
    setAttrib(elm, 'style');
    setAttrib(elm, 'class', getSelectValue(formObj, 'classlist'));
    setAttrib(elm, 'rel');
    setAttrib(elm, 'rev');
    setAttrib(elm, 'charset');
    setAttrib(elm, 'hreflang');
    setAttrib(elm, 'dir');
    setAttrib(elm, 'lang');
    setAttrib(elm, 'tabindex');
    setAttrib(elm, 'accesskey');
    setAttrib(elm, 'type');
    setAttrib(elm, 'onfocus');
    setAttrib(elm, 'onblur');
    setAttrib(elm, 'onclick');
    setAttrib(elm, 'ondblclick');
    setAttrib(elm, 'onmousedown');
    setAttrib(elm, 'onmouseup');
    setAttrib(elm, 'onmouseover');
    setAttrib(elm, 'onmousemove');
    setAttrib(elm, 'onmouseout');
    setAttrib(elm, 'onkeypress');
    setAttrib(elm, 'onkeydown');
    setAttrib(elm, 'onkeyup');

    // Refresh in old MSIE
    if (tinyMCE.isMSIE5)
	elm.outerHTML = elm.outerHTML;
}

function getSelectValue(form_obj, field_name) {
    var elm = form_obj.elements[field_name];

    if (elm == null || elm.options == null)
	return "";

    return elm.options[elm.selectedIndex].value;
}

function getLinkListHTML(elm_id, target_form_element, onchange_func) {
    if (typeof(tinyMCELinkList) == "undefined" || tinyMCELinkList.length == 0)
	return "";

    var html = "";

    html += '<select id="' + elm_id + '" name="' + elm_id + '"';
    html += ' class="mceLinkList" onfoc2us="tinyMCE.addSelectAccessibility(event, this, window);" onchange="this.form.' + target_form_element + '.value=';
    html += 'this.options[this.selectedIndex].value;';

    if (typeof(onchange_func) != "undefined")
	html += onchange_func + '(\'' + target_form_element + '\',this.options[this.selectedIndex].text,this.options[this.selectedIndex].value);';

    html += '"><option value="">---</option>';

    for (var i=0; i<tinyMCELinkList.length; i++)
	html += '<option value="' + tinyMCELinkList[i][1] + '">' + tinyMCELinkList[i][0] + '</option>';

    html += '</select>';

    return html;

    // tinyMCE.debug('-- image list start --', html, '-- image list end --');
}

function getTargetListHTML(elm_id, target_form_element) {
    var targets = tinyMCEPopup.getParam('theme_advanced_link_targets', '').split(';');
    var html = '';

    html += '<select id="' + elm_id + '" name="' + elm_id + '" onf2ocus="tinyMCE.addSelectAccessibility(event, this, window);" onchange="this.form.' + target_form_element + '.value=';
    html += 'this.options[this.selectedIndex].value;">';
    html += '<option value="_self">' + tinyMCEPopup.getLang('campsiteinternallink_dlg.target_same') + '</option>';
    html += '<option value="_blank">' + tinyMCEPopup.getLang('campsiteinternallink_dlg.target_blank') + ' (_blank)</option>';
    html += '<option value="_parent">' + tinyMCEPopup.getLang('campsiteinternallink_dlg.target_parent') + ' (_parent)</option>';
    html += '<option value="_top">' + tinyMCEPopup.getLang('campsiteinternallink_dlg.target_top') + ' (_top)</option>';

    for (var i=0; i<targets.length; i++) {
	var key, value;

	if (targets[i] == "")
	    continue;

	key = targets[i].split('=')[0];
	value = targets[i].split('=')[1];

	html += '<option value="' + key + '">' + value + ' (' + key + ')</option>';
    }

    html += '</select>';

    return html;
}

// While loading
preinit();
tinyMCEPopup.onInit.add(init);
