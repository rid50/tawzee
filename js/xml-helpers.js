(function(xmlHelper, $, undefined ) {

	xmlHelper.createElement = function(name, value) {
	//function createElement(name, value) {
		var el = document.createElementNS("", name);
		var textNode = document.createTextNode(value);
		el.appendChild(textNode);
		return el;
	}


	xmlHelper.createElementWithAttribute = function(name, attrName, attrValue) {
	//function createElementWithAttribute(name, attrName, attrValue) {
		var el = document.createElementNS("", name);
		el.setAttribute(attrName, attrValue);
		return el;
	}

	xmlHelper.appendAttributeToElement = function(el, attrName, attrValue) {
	//function appendAttributeToElement(el, attrName, attrValue) {
		el.setAttribute(attrName, attrValue);
		return el;
	}

	xmlHelper.appendNewLineElement = function(el, whitespace) {
	//function appendNewLineElement(el, whitespace) {
		var text = '\n';
		if (el == undefined)
			whitespace = 0;
		while (whitespace) {
			text += ' ';
			whitespace--;
		}
		var textNode = document.createTextNode(text);
		if (el == undefined)
			return textNode;
		el.appendChild(textNode);
		return el;
	}

	xmlHelper.appendSpaceElement = function(el, whitespace) {
	//function appendSpaceElement(el, whitespace) {
		var text = '';
		if (el == undefined)
			whitespace = 0;
		while (whitespace) {
			text += ' ';
			whitespace--;
		}
		var textNode = document.createTextNode(text);
		if (el == undefined)
			return textNode;
		el.appendChild(textNode);
		return el;
	}

	xmlHelper.createNewLineElement = function(whitespace) {
	//function createNewLineElement(whitespace) {
		var text = '\n';
		while (whitespace) {
			text += ' ';
			whitespace--;
		}
		var textNode = document.createTextNode(text);
		return textNode;
	}

	xmlHelper.createSpaceElement = function(whitespace) {
	//function createSpaceElement(whitespace) {
		var text = '';
		while (whitespace) {
			text += ' ';
			whitespace--;
		}
		var textNode = document.createTextNode(text);
		return textNode;
	}

}( window.xmlHelper = window.xmlHelper || {}, jQuery ));