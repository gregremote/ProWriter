jQuery(document).ready(function(){
	jQuery( "body" ).dblclick(function() {	
		checkSelectionChanged('dclick');
		console.log('double click');
	});
});

jQuery(window).load(function(){
	
	jQuery('div[contenteditable="true"]').keypress(function(event) {
    
	if (event.which != 13)
        return true;
    var docFragment = document.createDocumentFragment();

    //add a new line
    var newEle = document.createTextNode('\n');
    docFragment.appendChild(newEle);

    //add the br, or p, or something else
    newEle = document.createElement('br');
    docFragment.appendChild(newEle);

    //make the br replace selection
    var range = window.getSelection().getRangeAt(0);
    range.deleteContents();
    range.insertNode(docFragment);

    //create a new range
    range = document.createRange();
    range.setStartAfter(newEle);
    range.collapse(true);

    //make the cursor there
    var sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);

    return false;
});
	  
	window['saveSelection']=0;
	window['restoreSelection']=0;
	window['disable_select']=0;
	
	
	if (window.getSelection && document.createRange) { // save selection
		saveSelection = function(containerEl) {
			var range = window.getSelection().getRangeAt(0);
			var preSelectionRange = range.cloneRange();
			preSelectionRange.selectNodeContents(containerEl);
			preSelectionRange.setEnd(range.startContainer, range.startOffset);
			var start = preSelectionRange.toString().length;
	
			return {
				start: start,
				end: start + range.toString().length
			}
		};
	
		restoreSelection = function(containerEl, savedSel) { // get selection
			var charIndex = 0, range = document.createRange();
			range.setStart(containerEl, 0);
			range.collapse(true);
			var nodeStack = [containerEl], node, foundStart = false, stop = false;
	
			while (!stop && (node = nodeStack.pop())) {
				if (node.nodeType == 3) {
					var nextCharIndex = charIndex + node.length;
					if (!foundStart && savedSel.start >= charIndex && savedSel.start <= nextCharIndex) {
						range.setStart(node, savedSel.start - charIndex);
						foundStart = true;
					}
					if (foundStart && savedSel.end >= charIndex && savedSel.end <= nextCharIndex) {
						range.setEnd(node, savedSel.end - charIndex);
						stop = true;
					}
					charIndex = nextCharIndex;
				} else {
					var i = node.childNodes.length;
					while (i--) {
						nodeStack.push(node.childNodes[i]);
					}
				}
			}
	
			var sel = window.getSelection();
			sel.removeAllRanges();
			sel.addRange(range);
		}
	} else if (document.selection && document.body.createTextRange) {
		saveSelection = function(containerEl) {
			var selectedTextRange = document.selection.createRange();
			var preSelectionTextRange = document.body.createTextRange();
			preSelectionTextRange.moveToElementText(containerEl);
			preSelectionTextRange.setEndPoint("EndToStart", selectedTextRange);
			var start = preSelectionTextRange.text.length;
	
			return {
				start: start,
				end: start + selectedTextRange.text.length
			}
		};
	
		restoreSelection = function(containerEl, savedSel) { // restore selection
			var textRange = document.body.createTextRange();
			textRange.moveToElementText(containerEl);
			textRange.collapse(true);
			textRange.moveEnd("character", savedSel.end);
			textRange.moveStart("character", savedSel.start);
			textRange.select();
		};
	}
	
	window['savedSelection']=0;
	
	window['doSave'] = doSave;
	
	window['doRestore'] = doRestore;
	
	function doSave() { //save
		savedSelection = saveSelection( document.getElementById("wysi-content") );
	}
	
	function doRestore() { // restore
		if (savedSelection) {
			restoreSelection(document.getElementById("wysi-content"), savedSelection);
		}
	}
	
	
	window['pasteHtmlAtCaret'] = pasteHtmlAtCaret;
	
	
	function pasteHtmlAtCaret(html) { // paste html at caret posision
		var sel, range;
		if (window.getSelection) {
			sel = window.getSelection();
			if (sel.getRangeAt && sel.rangeCount) {
				range = sel.getRangeAt(0);
				range.deleteContents();
	
				var el = document.createElement("div");
				el.innerHTML = html;
				var frag = document.createDocumentFragment(), node, lastNode;
				while ( (node = el.firstChild) ) {
					lastNode = frag.appendChild(node);
				}
				range.insertNode(frag);
	
				if (lastNode) {
					range = range.cloneRange();
					range.setStartAfter(lastNode);
					range.collapse(true);
					sel.removeAllRanges();
					sel.addRange(range);
				}
			}
		} else if (document.selection && document.selection.type != "Control") {
			// IE < 9
			document.selection.createRange().pasteHTML(html);
		}
	}
	
	window['addImageMV'] = addImageMV;
	
	function addImageMV(iurl) {
		disable_select=0;
		doRestore();
		pasteHtmlAtCaret('<p><img src="'+iurl+'" /></p>');
	}
	
		var custom_uploader;
	
		jQuery('#upload_image_button').click(function(e) {
	
			jQuery('#quote').css('display','none');
	
			disable_select=1;
	
			e.preventDefault();
	
			//If the uploader object has already been created, reopen the dialog
			if (custom_uploader) {
				custom_uploader.open();
				return;
			}
	
			//Extend the wp.media object
			custom_uploader = wp.media.frames.file_frame = wp.media({
				title: 'Choose Image',
				button: {
					text: 'Choose Image'
				},
				multiple: false
			});
	
			//When a file is selected, grab the URL and set it as the text field's value
			custom_uploader.on('select', function() {
				attachment = custom_uploader.state().get('selection').first().toJSON();
				jQuery('#upload_image').val(attachment.url);
	
				addImageMV(attachment.url);
			});
	
			//Open the uploader dialog
			custom_uploader.open();
	
		});
	
	
	
	
	window['rangev']='';
	
	
	
	window['ose_edit_html'] = ose_edit_html;
	
		function ose_edit_html() {
			
			jQuery('.ose-edit-button').css('display','none');
	jQuery('.ose-save-button').css('display','block');
			
			var menuInterval=setInterval(checkSelectionChanged, 100);
			
			
			jQuery('#ose-edit-button').css('display','none');
			jQuery('#ose-save-button').css('display','block');
	
	
	var WYSIID=document.getElementById('wysi-content');
	
	WYSIID.addEventListener("mouseup", checkSelectionChanged);
	
	function unwrap(el, target) {
		if ( !target ) {
			target = el.parentNode;
		}
		while (el.firstChild) {
			target.appendChild(el.firstChild);
		}
		el.parentNode.removeChild(el);
	}


	document.getElementById('wysi-content').addEventListener('DOMNodeInserted', function(ev) {
		if ( ev.target.tagName=='SPAN' ) {
			unwrap(ev.target);
		}
	});
		
	
	jQuery('#wysi-content').attr('contenteditable','true');
	
	
	
	
	jQuery('#wysi-content').css('outline','none');
	jQuery('.noedit').attr('contenteditable','false');
	jQuery('.noedit').css('background-color','#fff');
	
	
	//remove all shortcodes from view
	console.log("A:"+jQuery('#wysi-content').html());
	
	var text = jQuery('#wysi-content').html().match(/\[.*?\]/g);
	
	function onlyUnique(value, index, self) { 
		return self.indexOf(value) === index;
		}//array_unique in JS
		
	
	//text = text.filter( onlyUnique ); // returns unique array values
	
	
	var str=jQuery('#wysi-content').html();
	
	//text.forEach(stripShortcodes);
	
	
	
	/*function stripShortcodes(element, index, array) {
		
		var re = new RegExp(element+"(?=>')","g");
		
		console.log (re);
		
		str=str.replace(element, '<div class="ose_hidden">'+element+'</div>');
		
	
	}*/
	
	text.forEach(stripShortcodes);
	
	
	
	function stripShortcodes(element, index, array) {
				
		var relement = 'This is a string';
			var selement=element;
			var relement = selement.replace(/\[/g, "^");
			str=str.replace(element, '<div class="ose_hidden" contenteditable="false" >'+relement+'</div>');
		}
			
			 str=str.replace(/\^/g, "[");
	
			jQuery('#wysi-content').html(str);
	
	}
	
	
	
	window['SetToH1'] = SetToH1;
	window['SetToH2'] = SetToH2;
	window['ClearBlock'] = ClearBlock;
	window['addLink'] = addLink;
	window['makeItal'] = makeItal;
	window['makeBold'] = makeBold;
	window['addEmbed'] = addEmbed;
	window['makeQuote'] = makeQuote;
	
	
	 function SetToH1 () {
				document.execCommand ("formatBlock", false, "<h1>");
				jQuery('#quote').css('display','none');
			}
			 function SetToH2 () {
				document.execCommand ("formatBlock", false, "<h2>");
				jQuery('#quote').css('display','none');
			}
			function ClearBlock () {
				document.execCommand ("formatBlock", false, "<p>");
				jQuery('#quote').css('display','none');
			}
	
			function addEmbed () {
				
				var theEmbed = prompt("Enter the Embed Code", "");
				document.execCommand ("insertHTML", false, theEmbed);
				jQuery('#quote').css('display','none');
	
			}
			
	
			function addLink () {
				var linkEmbed = prompt("Enter the Url", "");
				var selected = document.getSelection();
				pasteHtmlAtCaret('<p><a href="'+linkEmbed+'" />'+selected+'</a></p>');
				jQuery('#quote').css('display','none');
			}
			function makeItal () {
				document.execCommand ("italic", false);
				jQuery('#quote').css('display','none');
			}
			function makeBold () {
				document.execCommand ("bold", false);
				jQuery('#quote').css('display','none');
			}
			 function makeQuote () {
				document.execCommand ("formatBlock", false, "<blockquote>");
				jQuery('#quote').css('display','none');
			}
	
	
	window['mousePos']=0;
	
	
	jQuery(document).mousemove(function (e) {
	
	
	
		mousePos = {left: e.pageX - 220, top: e.pageY - 110};
	});
	
	
	window['selectedText']='';
	
	window['getSelectedText'] = getSelectedText;
	
	
	function getSelectedText(){
	
	
		if(window.getSelection){
		
		var selObj = window.getSelection();
	
	
		var range  = selObj.getRangeAt(0);
		if(range.collapsed)
			return null;
	
			if(window.getSelection().toString()!="")
				return window.getSelection().toString();
			else
				return window.getSelection().anchorNode;
		}
		else if(document.getSelection){
			return document.getSelection();
		}
		else if(document.selection){
			return document.selection.createRange().text;
		}
	}
	
	window['checkSelectionChanged'] = checkSelectionChanged;
	
	
	
	function checkSelectionChanged(dclick) {
	
		var rangev;
	
		var current = getSelectedText();
		var noffset = jQuery( '#quote' ).offset();
		jQuery('#inputt').val(noffset.left);
		jQuery('#inputu').val(noffset.top);
	
		jQuery('#inputv').val(mousePos.left);
		jQuery('#inputw').val(mousePos.top);
	
		var el = document.getElementById( 'wysi-content' );
		var str= el.innerHTML ;
	
		str=str.replace(/\s+/g, " ");
	
	
		if(current != selectedText) {
			selectedText = current;
			if(selectedText!=null||dclick=="dclick") {
			   jQuery('#quote #text').text(selectedText);
			   var qleft=mousePos.left;
			   if (qleft<0)
				qleft=0;
				if(qleft+440>parseFloat(jQuery( window ).width()))
					qleft=parseFloat(jQuery( window ).width())-440;
				jQuery('#quote').css('left',qleft);
				jQuery('#quote').css('top',mousePos.top);
				jQuery('#quote').css('display','block');
	
				doSave();
	
			 } else {
				jQuery('#quote').css('display','none');
			}
		}
	}
	
	
	window['swapHTML'] = swapHTML;
	
	
	function swapHTML() {
		//clearInterval(menuInterval); not needed interval clears on page refresh
		jQuery('.edit-link').remove();
		jQuery('.ose_hidden').contents().unwrap();
		var str=jQuery('#wysi-content').html();
	  str = str.replace(/[\u201C\u201D\u2033]/g, '"');
	
		jQuery('#content-replace').val(str);
		
		
	}
	
	


 
});


