/**
 * Functions for the image listing, used by images.php only
 * @author $Author: paul $
 * @version $Id: images.js 5087 2006-06-01 21:54:08Z paul $
 * @package ImageManager
 */

	function i18n(str) {
		if(I18N)
		  return (I18N[str] || str);
		else
			return str;
	};

	function changeDir(newDir)
	{
		showMessage('Loading');
		location.href = "images.php?dir="+newDir;
	}


	function newFolder(dir, newDir)
	{
		location.href = "images.php?dir="+dir+"&newDir="+newDir;
	}

	//update the dir list in the parent window.
	function updateDir(newDir)
	{
		var selection = window.top.document.getElementById('dirPath');
		if(selection)
		{
			for(var i = 0; i < selection.length; i++)
			{
				var thisDir = selection.options[i].text;
				if(thisDir == newDir)
				{
					selection.selectedIndex = i;
					showMessage('Loading');
					break;
				}
			}
		}
	}

	function selectImage(p_image_template_id, p_filename, p_alt)
	{
		var topDoc = window.top.document;

		var obj = topDoc.getElementById('f_image_template_id');
		obj.value = p_image_template_id;

		var obj = topDoc.getElementById('f_url');
		obj.value = p_filename;

		var obj = topDoc.getElementById('f_alt');
		obj.value = p_alt;

		var obj = topDoc.getElementById('f_caption');
		obj.value = p_alt;

		//alert('f_image_template_id: '+p_image_template_id+', f_url: '+p_filename+', f_alt: '+p_alt);
	}

	function showMessage(newMessage)
	{
		var topDoc = window.top.document;

		var message = topDoc.getElementById('message');
		var messages = topDoc.getElementById('messages');
		if(message && messages)
		{
			if(message.firstChild)
				message.removeChild(message.firstChild);

			message.appendChild(topDoc.createTextNode(i18n(newMessage)));

			messages.style.display = "block";
		}
	}

	function addEvent(obj, evType, fn)
	{
		if (obj.addEventListener) { obj.addEventListener(evType, fn, true); return true; }
		else if (obj.attachEvent) {  var r = obj.attachEvent("on"+evType, fn);  return r;  }
		else {  return false; }
	}

	function confirmDeleteFile(file)
	{
		if(confirm(i18n("Delete file?")))
			return true;

		return false;
	}

	function confirmDeleteDir(dir, count)
	{
		if(count > 0)
		{
			alert(i18n("Please delete all files/folders inside the folder you wish to delete first."));
			return;
		}

		if(confirm(i18n("Delete folder?")))
			return true;

		return false;
	}

	addEvent(window, 'load', init);