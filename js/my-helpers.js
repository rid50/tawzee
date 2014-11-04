(function(myHelper, $, undefined ) {
	myHelper.isRequired = function(o, n) {
		if (o.val().length == 0) {
			o.addClass( "ui-state-error" );
			updateTips($.i18n.prop("Field") + " " + n + " " + $.i18n.prop("CannotBeEmpty"));
			o.focus();
			return false;
		} else {
			return true;
		}
	}
	myHelper.checkLength = function(o, n, min, max) {
		if (o.val().length > max || o.val().length < min) {
			o.addClass( "ui-state-error" );
			updateTips($.i18n.prop("LengthOf") + " " + n + " " + $.i18n.prop("MustBeBetween") + " " + min + " & " + max);
			o.focus();
			return false;
		} else {
			return true;
		}
	}

	myHelper.checkRegexp = function(o, regexp, n) {
		if (o.val().length == 0)
			return true;
		regexp = new RegExp(regexp);
		if (!(regexp.test(o.val()))) {
			o.addClass("ui-state-error");
			updateTips(n);
			return false;
		} else {
			return true;
		}
	}
	
	myHelper.hyphensToCamel = function(str) {
/*	
		return str.replace(/-([a-z])/g, function (h, c) {
			return c.toUpperCase();
		}).replace(/^[a-z]/, function (c2) {
			return c2.toUpperCase();
		});
*/		
		return str.replace(/^[a-z]/, function (c) {
			return c.toUpperCase();
		}).replace(/-([a-z])/g, function (h, c) {
			return c.toUpperCase();
		});
	}

	myHelper.camelToHyphens = function(str) {
/*	
		return str.replace(/-([a-z])/g, function (h, c) {
			return c.toUpperCase();
		}).replace(/^[a-z]/, function (c2) {
			return c2.toUpperCase();
		});
*/		
		return str.replace(/^[A-Z]/, function (c) {
			return c.toLowerCase();
		}).replace(/[A-Z]/g, function (c) {
			return '-' + c.toLowerCase();
		});
	}
	
	var validationTip;

	myHelper.setValidationTip = function(tip) {
		validationTip = tip;
	}

	function updateTips(t) {
		validationTip.val(t);
		//validationTip.val(t).addClass( "ui-state-highlight" );
		//setTimeout(function() {
		//	validationTip.removeClass("ui-state-highlight", 1500);
		//}, 500 );
	}
	
}( window.myHelper = window.myHelper || {}, jQuery ));