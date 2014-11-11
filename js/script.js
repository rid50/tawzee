var idp, idpSource;
var documentSource;
var lang;
var searchInterval;

var _admin;

var _directors = [];
var _userLoginName = "";

var userInfo;
var _acl;
var _rootActors;
var actor;
var sectionId;				// id of  section of the department, like: follow_up, ac...
var reportTo = null;
var areaNames;

var _currentForm;
var _formButtonSet;
var _applicationNumber = "";
var	_jasperReportsServerConnection = false;
var _jasperReportsURL = "http://" + location.hostname +  ":8084/TawzeeJasperReports/JasperServlet";
var _runJettyEmbedded = "http://tawzee/jetty/webapps/TawzeeJasperReports/WEB-INF/cgi-bin/RunJasperReportsCGI.bat";

var _myCustomEvent;
var _slider; 

var actor_enum = {
	manager: 0x1,
	employee: 0x2
};

var sectionId_enum = {
	followup: "0",
	ac: "1",
	electricity: "2",
	checkup: "3",
	edafat: "4",
	archive: "39",
};

//first, checks if it isn't implemented yet
if (!String.prototype.format) {
  String.prototype.format = function() {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function(match, number) { 
      return typeof args[number] != undefined
        ? args[number]
        : match
      ;
    });
  };
}

$.xml = function(el) {
	if (window.XMLSerializer)
		 return (new XMLSerializer()).serializeToString(el);
	else if (window.ActiveXObject)
		 return this.xml;
    else alert('Xmlserializer not supported');
}

function xml2Str(xmlNode) {
	try {
		// Gecko- and Webkit-based browsers (Firefox, Chrome), Opera.
		return (new XMLSerializer()).serializeToString(xmlNode);
	}
	catch (e) {
		try {
			// Internet Explorer.
			return xmlNode.xml;
		}
		catch (e) {  
			//Other browsers without XML Serializer
			alert('Xmlserializer not supported');
		}
	}
	return false;
}

//function moveScroller() {
function setScroller() {
    //var move = function() {
		var s = $("#terms");
		var s2 = $("#formButtonSet");

		var left, left2;
		if ($("body[dir='ltr']").length) {
			left = $("#left-section").offset().left + 5;
			left2 = $("#left-section").offset().left + $("#left-section").width() - s2.width() - 20;
			//right = "auto";
		} else {
			//left = "184.5px";
			//left = "";
			left = $("#left-section").offset().left + $("#left-section").width() - s.width() - 5;
			left2 = $("#left-section").offset().left + 20;
		}
		
		//console.log(left2);

		s.css({
			position: "fixed",
			top: ($("#left-section").offset().top + 14) + "px",
			left: left + "px",
			//right: right
		});

		s2.css({
			position: "fixed",
			top: ($("#left-section").offset().top + 20) + "px",
			left: left2 + "px",
			//right: right
		});
		//console.log("resize");
/*
        var st = $("#left-section").scrollTop();
        var ot = $("#scroller-anchor").position().top;
		console.log("st: " + st + " | ot: " + ot);

        var s = $("#scroller");
        if(st > ot) {
            s.css({
                position: "fixed",
                top: ($("#left-section").offset().top + 51) + "px",
				left: ($("#left-section").offset().left + 1) + "px",
            });
        } else {
            if(st <= ot) {
                s.css({
                    position: "relative",
                    top: "50px",
					left: "-20px"
                });
            }
        }
*/		
    //};
    //$("#left-section").scroll(move);
    //$(window).resize(move);
    //move();
}

$(window).resize(setScroller);

$(document).ready(function () {
	$.ajaxSetup({ cache: false, async: false });

	$.blockUI.defaults.message = '<img src="images/ajax-loader.gif"/>';
	$.blockUI.defaults.css.top = '3px';
	$.blockUI.defaults.css.left = '3px';
	$.blockUI.defaults.css.textAlign = 'left';
	$.blockUI.defaults.css.border = 'none';
	$.blockUI.defaults.css.color = 'transparent';
	$.blockUI.defaults.css.backgroundColor = 'transparent';
	$.blockUI.defaults.css.cursor = 'default';
	
	$.blockUI.defaults.overlayCSS.backgroundColor = '#383838';
	$.blockUI.defaults.overlayCSS.opacity = 0.2;
	
	if (navigator.userAgent.match(/msie/i))
		$.blockUI.defaults.overlayCSS.cursor = 'default';

	$.blockUI.defaults.centerX = true;
	$.blockUI.defaults.centerY = true;

	// Ajax activity indicator bound to ajax start/stop document events
	$(document)
		.ajaxStart(function(){
			$.blockUI();
		})
		.ajaxStop(function(){
			$.unblockUI();
		});
	
	$.get("get_ini.php")
		.done(function(data) {
			_admin = data.admin;
			idp = data.IdP;
			idpSource = data.IdPSource;
			documentSource = data.documentSource;
			lang = data.lang;
			searchInterval = data.searchInterval;
			
			switch (data.jquery_theme) {
				case "uilightness":
					$("#stylesheet").attr({href : "http://code.jquery.com/ui/1.11.1/themes/ui-lightness/jquery-ui.min.css"});
					break;
				case "redmond":
					$("#stylesheet").attr({href : "http://code.jquery.com/ui/1.11.1/themes/redmond/jquery-ui.min.css"});
					break;
				//default:
					//$("#stylesheet").attr({href : "http://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css"});
			}
		});
	
	$("#flagKuwait").off("click").on("click", function(event){
		if ($("body[dir='ltr']").length) {
			lang = 'ar';
			//$(videoControl).hide();
			toggleLanguage('ar', 'rtl');
			//moveScroller();			
			

			//$(videoControl).show();
		}
	});

	$("#flagUK").off("click").on("click", function(event){
		if ($("body[dir='rtl']").length) {
			lang = 'en';
			toggleLanguage('en', 'ltr');
			//moveScroller();			
			
			
		}
	});

	$.datepicker.setDefaults( $.datepicker.regional[ "" ] );
	$.datepicker.setDefaults({
		//regional: "",
		showOn: "both",
		buttonImageOnly: true,
		buttonImage: "images/calendar.gif",
		buttonText: "Calendar",
		//changeMonth: 'true',
		//changeYear: 'true'
	});
	
	$(".rid50-datepicker").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "dd/mm/yy",
	});
	
	//$("#application-date").datepicker( "setDate", "now" );
	$('input[id="application-date"]').change(function() {
		var regExpPattern = /^(0[1-9]|[12][0-9]|3[01])[/](0[1-9]|1[012])[/](19|20)\d\d$/;
		if (!$(this).val().match(regExpPattern)) {
			$(this).addClass( "ui-state-error" );
			$("#searchButton").attr('disabled', 'disabled');
		} else {
			$(this).removeClass( "ui-state-error" );
			$("#searchButton").removeAttr("disabled");
		}
	});

// ********************************* on show/hide *************************************

	(function ($) {
		$.each(['show', 'hide'], function (i, ev) {
			var el = $.fn[ev];
			$.fn[ev] = function () {
				//console.log(this);
				this.trigger(ev);
				return el.apply(this, arguments);
			};
		});
	})(jQuery);
	
	$('#main-form').on('show', function() {
		fillControlCenterDDBox();
		//if (_applicationNumber == "") {
			//$('#application-number').removeAttr('readonly');
			setTimeout(function() {
				setScroller();
				setFocus();
				//$('#application-number').focus();
			}, 100 );
		//}

		//$("#left-section").append($('#terms-and-conditions'));
		//$('#terms-and-conditions').show();
		
//		this.append('<input type="text" id="error-box" />');	
//		this.append('#formButtonSet');
	});

	$('#main-form').on('hide', function() {
		$(this).find("input[type='text']").removeClass("ui-state-error");
		$('#controlCenterId').removeClass("ui-state-error");
		//$('#application-number').attr('readonly','readonly');
		//$('#terms-and-conditions').hide();
		//moveScroller();
		error('');
	});
	
	$('#load-form').on('show', function() {
		//if ($('#file-number').attr('readonly') === undefined) {
			setTimeout(function() {
				setScroller();
				setFocus();
				//$('#file-number').focus();
			}, 100 );
		//}
	});
	 
	$('#load-form').on('hide', function() {
		$(this).find("input[type='text']").removeClass("ui-state-error");
		//$('#file-number').attr('readonly','readonly');
		error('');
	});
	 
	$('#report-container').on('show', function() {
		loadStampedSignatures();
	});

	$('#report-container').on('hide', function() {
		$('#report-container div').remove();
		$('#report-container img').remove();
	});

	$('#flexslider-container').on('hide', function() {
		$(".slider").remove();
	});

	$('#jstree_userlist').on('show', function(e) {
		if (e.target === this) {	// call is from accordion tabs, not from jTree framework
			//console.log(this.id);
			initUserTree();
			$('#userLegend').show();
		}
	});

	$('#jstree_userlist').on('hide', function(e) {
		if (e.target === this) {
			$('#userLegend').hide();
			if ($(this).hasClass("jstree")) {
				$(this).jstree('destroy').empty();
			}
		}
	});

	$('#jstree_resourcelist').on('show', function(e) {
		if (e.target === this)
			initResourceTree();
			$('#aclLegend').show();
	});

	$('#jstree_resourcelist').on('hide', function(e) {
		if (e.target === this) {
			$('#aclLegend').hide();
			if ($(this).hasClass("jstree")) {
				$(this).jstree('destroy').empty();
			}
		}
	});
	
// ********************************* on show/hide *************************************

	_currentForm = "main-form";

	$('#application-form-link, #load-form-link').on("click", function(e){
		var clickedForm = this.getAttribute('data-form');

		if (this.attributes["disabled"] && this.attributes["disabled"].value == "disabled") {
			e.preventDefault();
			return;
		}
		
		//if ($('#application-number').attr('readonly') == undefined && clickedForm == "load-form")
		if (!$('#application-number').val().match("[/\\\\]") && clickedForm == "load-form")
			return;

		_currentForm = clickedForm;
			
		if (_applicationNumber != "") {
			var data;
			if ("main-form" == _currentForm)
				data = {"func":"getApp"};
			else
				data = {"func":"getLoad"};

			data.param = {"applicationNumber": _applicationNumber};
			
			//data = {"func":"getLoad", "param":{applicationNumber:_applicationNumber}};
				
			$.get("json_db_pdo.php", data)
				.done(function( data ) {
					if (isAjaxError(data))
						return;
				
					// if (data && data.constructor == Array) {
						// if (data[0] && data[0].error !== undefined) {
							// alert (data[0].error);
							// return;
						// }
					// }
					
					if (data.d == undefined)
						result = data;
					else
						result = data.d.Data;
					/*
					$('#file-number').val(result.FileNumber);
					var dt = $.datepicker.parseDate('yy-mm-dd', result.LoadDate);
						
					$('#load-date').val($.datepicker.formatDate('dd/mm/yy', dt));
					
					delete result.FileNumber;
					delete result.LoadDate;
					*/
					//if (result.length == 0)
					//$('.tr-load-detail').not(':first').empty();

					var currForm = $('#' + _currentForm);
					if ("main-form" == _currentForm) {
						currForm.find("input[type='text']").not("#application-number, #application-date, #owner-name, #project-name, #area, #block, #plot, #construction-exp-date, #feed-points").val("");
						currForm.find(':radio').not("input[name='project-type']").prop('checked', false);
						currForm.find(':checkbox').prop('checked', false);

						if (result.length == 0) {
							//$('#main-form').children().val("");
						} else {
							//$('#residence-total-area, #construction-area, #conditioning-area').val("");
							$('.tr-application-detail').each(function(index, tr) {
								if (index > 0)
									$(tr).remove();
							})

							var diff = result.length - 1 - $('.tr-application-detail').length;
							if (diff > 0) {
								var lastRow, i;
								for (i = 0; i < diff; i++) {
									lastRow = $('.tr-application-detail:first').clone(true, true);
									$('.tr-application-detail:first').after(lastRow);
								}
								
								//setEventToDeleteRowButtons();
							}
							
//							fillControlCenterDDBox();
							
							var tableRow;
							result.forEach(function(r, index) {
								if (index == 0) {
									$('#residence-total-area').val(r.ResidenceTotalArea);
									$('#construction-area').val(r.ConstructionArea);
									$('#ac-area').val(r.ACArea);
									$('#current-load').val(r.CurrentLoad);
									$('#extra-load').val(r.ExtraLoad);
									$('#load-after-delivery').val(r.LoadAfterDelivery);
									$('#conductive-total-load').val(r.ConductiveTotalLoad);
									//$('#feed-points').val(r.FeedPoints);
									setRadioButton('site-feed-point', r.SiteFeedPoint);
									setRadioButton('requirements', r.Requirements);
									setRadioButton('cable-size', r.CableSize);
									setRadioButton('fuze', r.Fuze);
									setRadioButton('meter', r.Meter);
									setCheckBox('possibility-yes', r.PossibilityYes, 4);	//4(100) - bitmask
									setCheckBox('possibility-no', r.PossibilityNo, 4);
									
									// $('input:radio[name=site-feeding-point]')[r.SiteFeedPoint].checked = true;
									// $('input:radio[name=requirements]')[r.Requirements].checked = true;
									// $('input:radio[name=cable-size]')[r.CableSize].checked = true;
									// $('input:radio[name=fuze]')[r.Fuze].checked = true;
									// $('input:radio[name=meter]')[r.Meter].checked = true;
									// $('input:checkbox[name=possibilityyes]')[r.PossibilityYes].checked = true;
									// $('input:checkbox[name=possibilityno]')[r.PossibilityNo].checked = true;
									$('#station-number').val(r.StationNumber);
								} else {
									//tableRow = $('#load-form table tr:nth-child(3)');
									tableRow = $('.tr-application-detail').eq(index - 1);
									tableRow.find('td:first>input').val(r.Switch);
									tableRow.find('td:nth-child(2)>input').val(r.K1000KWT);
									tableRow.find('td:nth-child(3)>input').val(r.K1000AMP);
									tableRow.find('td:nth-child(4)>input').val(r.K1250KWT);
									tableRow.find('td:nth-child(5)>input').val(r.K1250AMP);
									tableRow.find('td:nth-child(6)>input').val(r.K1600KWT);
									tableRow.find('td:nth-child(7)>input').val(r.K1600AMP);
								}
							});
						}
					} else if ("load-form" == _currentForm) {
						currForm.find("input[type='text']").not("#owner-name2, #project-name2, #area2, #block2, #plot2, #construction-exp-date2, #feed-points2").val("");
						if (result.length == 0) {
							$('#file-number')[0].defaultValue = "";
							//$('#file-number').removeAttr('readonly');
						} else {
							//$('#file-number').attr('readonly','readonly');
									
							$('.tr-load-detail').each(function(index, tr) {
								if (index > 0)
									$(tr).remove();
							})

							var diff = result.length - 1 - $('.tr-load-detail').length;
							if (diff > 0) {
								var lastRow, i;
								for (i = 0; i < diff; i++) {
									lastRow = $('.tr-load-detail:first').clone(true, true);
									$('.tr-load-detail:first').after(lastRow);
								}
							}

							var ConnectorLoad = 0, SummerLoad = 0, WinterLoad = 0;
							
							var tableRow;
							result.forEach(function(r, index) {
								if (index == 0) {
									$('#file-number').val(r.FileNumber);
									$('#file-number')[0].defaultValue = $('#file-number')[0].value;

									var dt = $.datepicker.parseDate('yy-mm-dd', r.LoadDate);
									$('#load-date').val($.datepicker.formatDate('dd/mm/yy', dt));
									$('#power-factor-summer').val(format( "#,##0.#0", r.PowerFactorSummer));
									$('#power-factor-winter').val(format( "#,##0.#0", r.PowerFactorWinter));
									$('#maximum-loads-summer').val(format( "#,##0.#0", r.MaximumLoadsSummer));
									$('#maximum-loads-winter').val(format( "#,##0.#0", r.MaximumLoadsWinter));
								} else {
									//tableRow = $('#load-form table tr:nth-child(3)');
									tableRow = $('.tr-load-detail').eq(index - 1);
									tableRow.find('td:first>input').val(r.Description);
									tableRow.find('td:nth-child(2)>input').val(r.ConnectorLoad);
									tableRow.find('td:nth-child(3)>input').val(r.SummerLoad);
									tableRow.find('td:nth-child(4)>input').val(r.WinterLoad);
									tableRow.find('td:nth-child(5)>input').val(r.Remarks);
									ConnectorLoad += parseFloat(r.ConnectorLoad);
									SummerLoad += parseFloat(r.SummerLoad);
									WinterLoad += parseFloat(r.WinterLoad);
								}
							});

							if (!(ConnectorLoad == 0 && SummerLoad == 0 && WinterLoad == 0)) {
								tableRow = $('.tr-load-detail:last').next();
								//tableRow.find('td:eq(2)>input').val(ConnectorLoad);
								//tableRow.find('td:eq(3)>input').val(SummerLoad);
								//tableRow.find('td:eq(4)>input').val(WinterLoad);
								tableRow.find('td:eq(1)>input').val(format( "#,##0.##0", ConnectorLoad));
								tableRow.find('td:eq(2)>input').val(format( "#,##0.##0", SummerLoad));
								tableRow.find('td:eq(3)>input').val(format( "#,##0.##0", WinterLoad));
							}
						}
					}
				})
				.fail(function(jqXHR, textStatus, errorThrown) {
					alert("getLoad - error: " + errorThrown);
				});
		}

		//var errBox = '<input type="text" id="error-box" tabindex="-1" />';
		$('.forms').each(function() {
			if (this.id == _currentForm) {
				if (_formButtonSet === undefined) {
					//$(this).append(errBox);
					$(this).append($('#formButtonSet'));
					_formButtonSet = null;
					
					if ($('#formButtonSet').css("display") == "none")
						$('#formButtonSet').show();
				}  else if (_formButtonSet == null) {
					//$('#error-box').remove();
					//$(this).append(errBox);
					
					_formButtonSet = $('#formButtonSet').detach();
					_formButtonSet.appendTo($(this));
					_formButtonSet = null;
				}

				$(this).show();
				//console.log('show: ' + this.id);
			} else {
				//console.log('hide: ' + this.id);
				$(this).hide();
			}
		});
		
		//applyAcl();
	});
	
	if (lang == "ar") {
		toggleLanguage('ar', 'rtl');
	} else {
		jQuery.i18n.properties({
			name:'Messages', 
			path:'bundle/', 
			mode:'both',
			language: 'en'
		});	
	}

	$(".possibility").on("click", function(event){
		if (this.id == "possibility-yes") {
			//$(this).parent().siblings().children('input')).prop('checked', this.checked);
			$('#possibility-no').find(':checkbox').prop('checked', false);
		} else {
			$('#possibility-yes').find(':checkbox').prop('checked', false);		
			$('#station-number').val("");
		}
	});
	
	$("button")
		.button()
		.click(function( event ) {
			event.preventDefault();
		}
	);
/*
	$("#startService").button({
		icons: {primary: null},
		text: false
	})
	.on("click", function(event){
		$.get("startJasperReportsService.php")
			.done(function( data ) {
				if (data && data.constructor == Array) {
					if (data[0] && data[0].error != undefined) {
						alert (data[0].error);
						return;
					}
				}
			})
	});
*/	
	$("#add, #newForm, #editForm, #save, #print, #printForm, #delete").button({
		icons: {primary: null},
		text: false
	}) //.addClass('printButton')
	//.attr({target: '_blank', href: 'http://localhost:8084/TawsilatJasperReports/JasperServlet?ApplicationNumber=45678'});
	
	.on("click", function(event){
		if (this.id == "add") {
			clearForm();
			if ($('#' + _currentForm).is(":visible")) {
				fillControlCenterDDBox();
			}
		} else if (this.id == "newForm") {
			_currentForm = "main-form";
			clearForm();
			$("#accordion").accordion( "option", "active", 1 );				
			//clearForm();
		} else if (this.id == "save") {
			$.blockUI();
			saveForm(this);
			//if ($('#application-number').val() == "")
			//	insertForm(this);
			//else
			//	updateForm(this);
/*
			if ("main-form" == _currentForm && $('#application-number').attr('readonly') == undefined ||
				"load-form" == _currentForm && $('#file-number').attr('readonly') == undefined)
				insertForm(this);
			else
				updateForm(this);
*/				
			$.unblockUI();
		} else {
			//var apn = $(this).parent().siblings('#application-number').val();
			var keyFieldValue = $("#" + $('#' + _currentForm).attr('data-key-field')).val();
			if (keyFieldValue != "") {
			//if (_applicationNumber != "") {
				if (this.id == "print" || this.id == "printForm") {
					printReport(function(reportName) {
						window.open(_jasperReportsURL + '?reportName=' + reportName + '&applicationNumber=' + _applicationNumber + '&keyFieldValue=' + keyFieldValue + '&renderAs=pdf', '_blank');
					});
				
				/*
					$.blockUI();
					_jasperReportsServerConnection = false;
					
					function onTimeOutHandler() {
						if ($('#error-box2').length > 0)
							$('#error-box2').remove();
					
						$.unblockUI();
						if (_jasperReportsServerConnection) {
							var ReportName;
							if ("main-form" == _currentForm)
								ReportName = "TawsilatApplicationForm";
							else
								ReportName = "TawsilatLoadForm";
								
							window.open('http://172.16.16.226:8084/TawsilatJasperReports/JasperServlet?ReportName=' + ReportName + '&ApplicationNumber=' + _applicationNumber + '&renderAs=pdf', '_blank');
						}
					}
					
					window.setTimeout(onTimeOutHandler, 5000);
					CheckConnection();
				*/
				} else if (this.id == "delete") {
					confirm("AreYouSure", null, function() {
						$.blockUI();
						deleteForm();
						$.unblockUI();
					});
				} else if (this.id == "editForm") {
					$("#accordion").accordion( "option", "active", 1 );				
				}
			}
		}
	});
/*	
	var lastRow, i;
	for (i = 0; i < 2; i++) {
		lastRow = $('.tr-application-detail:last').clone();
		$('.tr-application-detail:last').after(lastRow);
		lastRow = $('.tr-load-detail:first').clone();
		$('.tr-load-detail:first').after(lastRow);
	}
*/
	$("#sync").button({
		icons: {primary: null},
		text: false
	}).on("click", function(event){
		if (_rowId && _page)
			$("#grid").setGridParam({page:_page, current:true}).trigger('reloadGrid');
	});
	
	$(".addRow").button({
		icons: {primary: null},
		text: false
	}).on("click", function(event){
		var lastRow;
		if ("main-form" == _currentForm) {
			lastRow = $('.tr-application-detail:last').clone(true, true);
			$('.tr-application-detail:last').after(lastRow);
			lastRow.find("input[type='text']").val("");
		} else if ("load-form" == _currentForm) {
			lastRow = $('.tr-load-detail:last').clone(true, true);
			$('.tr-load-detail:last').after(lastRow);
			lastRow.find("input[type='text']").val("");
		}
		//setEventToDeleteRowButtons();
	});

	$(".deleteRow").button({
		icons: {primary: null},
		text: false
	});
	
	$("#add, .addRow, #newForm, #editForm, #save, #print, #printForm, #delete, #terms-button").on("mousedown", function(){
		$(this).animate({'top': '+=1px', 'left': '+=1px'}, 100);
	});

	$("#add, .addRow, #newForm, #editForm, #save, #print, #printForm, #delete, #terms-button").on("mouseup", function(){
		$(this).animate({'top': '-=1px', 'left': '-=1px'}, 100);
	});

	$("#terms-button").button({
		icons: {primary: null},
		text: false
	}).on("click", function(event){
		$('#openModalLink')[0].click();
	});
	
	setEventToDeleteRowButtons();

	//moveScroller();
			
	//start(_userLoginName, 'db', function(){ applyAcl() });	// 'db' - get Actors from database
	start(_userLoginName, 'db', null);	// 'db' - get Actors from database
	
	if (_admin != userInfo[0].loginName) {
		$('#userLoginSelectDiv').hide();
	}			
		
	if (!$("#main-div").hasClass("accessRejected")) {
		initAccordion();
		getAreas();

		$("#left-section").append($("#divGrid"));
		$("#left-section").append($("#resourceManagement"));
		$("#left-section").append($("#main-form"));
		$("#left-section").append($("#load-form"));
		$("#left-section").append($("#report-container"));
		
		_slider = $(".slider").html(); 
		$("#left-section").append($("#flexslider-container"));
		$(".slider").remove();
		
		if ($("#divGrid").css("display") == "none") {
			toggleGrid(lang);
			$("#divGrid").show();
		}

		if (_admin != userInfo[0].loginName) {
			$('#userImportButton').attr('disabled', 'disabled').fadeTo("fast", .5);		// user assignment tab
			$('#userExportButton').attr('disabled', 'disabled').fadeTo("fast", .5);		// user assignment tab
		}
		
		$('#accordion').show();
	} else if (_admin == userInfo[0].loginName) {
		$("#main-div").removeClass("accessRejected");
		$("#left-section").append($("#resourceManagement"));
		initAccordion();
		var accTabs = $("#accordion > span");
		accTabs.addClass( "ui-state-disabled" );
		
		$("#accordion").accordion( "option", "active", 4 );				
		$('#accordion').show();
	}

	//_myCustomEvent = new Event('MyCustomEvent');
	_myCustomEvent = document.createEvent('Event');
	_myCustomEvent.initEvent('MyCustomEvent', true, true);
	document.addEventListener('MyCustomEvent', function (e) {
		setTimeout(function() {
			getAcl();
			//moveScroller();
		}, 100 );
	}, false);
	//applyAcl();
});

function setFocus() {
	var currForm = $('#' + _currentForm);
	var fields;

	if ("main-form" == _currentForm) {
		fields = currForm.find("input[type='text']").not("#error-box").filter(function() {
			return $(this).parent()[0].tagName != "TD";
		});
	} else if ("load-form" == _currentForm) {
		fields = currForm.find("input[type='text']").not("#error-box, #owner-name2, #project-name2, #area2, #block2, #plot2, #construction-exp-date2, #feed-points2").filter(function() {
			return $(this).parent()[0].tagName != "TD";
		});
	}

	fields.each(function() {
		if ($(this).attr('disabled') != 'disabled' && $(this).attr('readonly') != 'readonly' && $(this).css('visibility') != 'hidden') {
			$(this).focus();
			return false;
		}
	});
}

function fillControlCenterDDBox() {
	var selectTag = $('#controlCenterId'), html;
	selectTag.empty();
	if (selectTag.attr('data-controlcenterid') == "0" || selectTag.attr('data-controlcenterid') == undefined)
		selectTag.append('<option selected="selected" value="0"> --- ' + jQuery.i18n.prop('Select') + ' --- </option>');
	
	var nodes = $('#jstree').jstree(true).get_json("#", {flat:false});
	$(nodes).each(function() {
		if (this.data["data-memberof"] == "CONTROLOFFICE") {
			if (this.id == selectTag.attr('data-controlcenterid'))
				html = '<option selected="selected" value="' + this.id + '">' + this.text + '</option>';
			else
				html = '<option value="' + this.id + '">' + this.text + '</option>';

			selectTag.append(html);
		}
	})
}

function getAcl() {
	_acl = {};
	//console.log('ACCC');
	$.get('acl.json')
		.done(function( data ) {
			if (isAjaxError(data))
				return;
			
			//console.log("Ok");

			var jstree = $('#jstree').jstree(true);
			var node = jstree.get_node(sectionId);
			var manager_loginName, employee_loginName;
			var office_groupName = node.data["data-memberof"];
			node.children.some(function(id) {
				var node = jstree.get_node(id);
				manager_loginName = node.data["data-loginname"];
				if (node.data["data-loginname"] == userInfo[0].loginName) {
					return true;
				}
				
				var found = false;
				$(node.children).each(function(i, id) {
					var node = jstree.get_node(id);
					if (node.data["data-loginname"] == userInfo[0].loginName) {
						employee_loginName = userInfo[0].loginName;
						found = true;
						return false;
					}
				})
				
				if (found)
					return true;
			})
			
			//var idx = _directors.indexOf(userInfo[0].loginName);
			if (_directors.indexOf(userInfo[0].loginName) != -1) {
				_acl = data;
			} else {
/*			
				var jstree = $('#jstree').jstree(true);
				var node = jstree.get_node(sectionId);
				var manager_id, employee_id;
				var office_id = node.data["data-memberof"];
				node.children.some(function(id) {
					var node = jstree.get_node(id);
					manager_id = node.data["data-loginname"];
					if (node.data["data-loginname"] == userInfo[0].loginName) {
						//manager_id = userInfo[0].loginName;
						return true;
					}
					
					$(node.children).each(function(i, id) {
						var node = jstree.get_node(id);
						if (node.data["data-loginname"] == userInfo[0].loginName) {
							employee_id = userInfo[0].loginName;
							return;
						}
					})
				})
*/
				for (var prop in data) {
					if (employee_loginName in data[prop]) {
						_acl[prop] = _acl[prop] || {};
						_acl[prop][employee_loginName] = data[prop][employee_loginName];
					}
					if (manager_loginName in data[prop]) {
						_acl[prop] = _acl[prop] || {};
						_acl[prop][manager_loginName] = data[prop][manager_loginName];
					}
					if (office_groupName in data[prop]) {
						_acl[prop] = _acl[prop] || {};
						_acl[prop][office_groupName] = data[prop][office_groupName];
					}
				}
				//applyAcl(office_id, manager_id, employee_id);

			}

			applyAcl(office_groupName, manager_loginName, employee_loginName);
			
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			errorFound = true;
			alert("Get Access Control List - error: " + errorThrown);
		});
}

function resetAcl() {
	var form = "main-form";
	form = $('#' + form);
	var fields = form.find("input[type='text']").not("#error-box");
	fields.each(function() {
		$(this).css('visibility', 'initial');
		$('label[for="' + this.id + '"]').css('visibility', 'initial');
		if ($(this).hasClass( "rid50-datepicker" ))
			$(this).datepicker( "option", "disabled", false );

		$(this).removeAttr('disabled');
	});
	
	fields = form.find("input[type='radio'], input[type='checkbox']");
	fields.each(function() {
		$(this).css('visibility', 'initial');
		$('label[for="' + this.id + '"]').css('visibility', 'initial');
		$(this).each(function() {
			$('label[for="' + this.id + '"]').css('visibility', 'initial');
		})

		$(this).removeAttr('disabled');
	});

	form = "load-form";
	form = $('#' + form);	
	fields = form.find("input[type='text']").not("#error-box, #owner-name2, #project-name2, #area2, #block2, #plot2, #construction-exp-date2, #feed-points2");
	fields.each(function() {
		$(this).css('visibility', 'initial');
		$('label[for="' + this.id + '"]').css('visibility', 'initial');
		if ($(this).hasClass( "rid50-datepicker" ))
			$(this).datepicker( "option", "disabled", false );

		$(this).removeAttr('disabled');
	});
	
	fields = form.find("input[type='radio'], input[type='checkbox']");
	fields.each(function() {
		$(this).css('visibility', 'initial');
		$('label[for="' + this.id + '"]').css('visibility', 'initial');
		$(this).each(function() {
			$('label[for="' + this.id + '"]').css('visibility', 'initial');
		})

		$(this).removeAttr('disabled');
	});
}

function applyAcl(office_groupName, manager_loginName, employee_loginName) {
	//console.log("appl");
	function setAcl(prop) {
		for (var id in _acl) {
			if (prop in _acl[id]) {

				var el = $('#' + id);
			//if (userInfo[0].loginName in _acl[id]) {
			
				if (el.length == 0) {
					el = $('input[name="' + id + '"]');		// group of radio/checkbox tags
					if (el.length == 0)
						continue;							// happens when accordion is not visible yet
				}
				
				//var nodeName = el[0].nodeName.toLowerCase();
				//var nodeType = (el[0].type) ? el[0].type.toLowerCase() : el[0].nodeName.toLowerCase();
				var nodeType = (el[0].nodeName.toLowerCase() == "input") ? el[0].type.toLowerCase() : el[0].nodeName.toLowerCase();

				//if (accordion && nodeType != "span")
				//	continue;

				if (nodeType == "span") {
					if (!el.hasClass('ui-accordion-header'))
						continue;
					
					if ("write" in _acl[id][prop]) {
						if (_acl[id][prop].write) {
							el.css('visibility', 'initial');
							if (el.hasClass( "my-ui-state-disabled" )) {
								el.removeClass( "my-ui-state-disabled" );
							}
						} else {
							if (!el.hasClass( "my-ui-state-disabled" )) {
								el.addClass( "my-ui-state-disabled" );
							}
						}
					}
					if ("read" in _acl[id][prop]) {
						if (_acl[id][prop].read) {
							if (el.hasClass( "my-ui-state-disabled" )) {
								el.removeClass( "my-ui-state-disabled" );
							}
						} else {
							el.css('visibility', 'hidden');
							if (!el.hasClass( "my-ui-state-disabled" )) {
								el.addClass( "my-ui-state-disabled" );
							}
						}
					}
				} else if (nodeType == "table") {
					if ("write" in _acl[id][prop]) {
						if (_acl[id][prop].write) {
							el.css('visibility', 'initial');
							el.find('button').css('visibility', 'initial');
							el.find('input').removeAttr('disabled');
						} else {
							el.find('input').attr('disabled', 'disabled');
							el.find('button').css('visibility', 'hidden');
						}
					}
					if ("read" in _acl[id][prop]) {
						if (_acl[id][prop].read) {
							el.find('button').css('visibility', 'initial');
							el.find('input').removeAttr('disabled');
						} else {
							el.css('visibility', 'hidden');
							el.find('button').css('visibility', 'hidden');
						}
					}
				} else if (nodeType == "div") {
					if ("write" in _acl[id][prop]) {
						if (_acl[id][prop].write) {
							el.css('visibility', 'initial');
							$('#' + el.attr('data-link')).removeAttr('disabled');
							$('#' + el.attr('data-link')).fadeTo("fast", 1.0).attr("href", "#");
							$('#formButtonSet button').not('#print').fadeTo("fast", 1.0).removeAttr('disabled');
						} else {
							$('#' + el.attr('data-link')).attr('disabled', 'disabled');
							$('#' + el.attr('data-link')).fadeTo("fast", .5).removeAttr("href"); 
							$('#formButtonSet button').not('#print').fadeTo("fast", .5).attr('disabled', 'disabled');
						}
					}
					if ("read" in _acl[id][prop]) {
						if (_acl[id][prop].read) {
							el.css('visibility', 'initial');
							$('#' + el.attr('data-link')).removeAttr('disabled');
							$('#' + el.attr('data-link')).fadeTo("fast", 1.0).attr("href", "#");
							$('#formButtonSet button').not('#print').fadeTo("fast", 1.0).removeAttr('disabled');
						} else {
							el.css('visibility', 'hidden');
							$('#' + el.attr('data-link')).attr('disabled', 'disabled');
							$('#' + el.attr('data-link')).fadeTo("fast", .5).removeAttr("href"); 
							$('#formButtonSet button').not('#print').fadeTo("fast", .5).attr('disabled', 'disabled');
						}
					}
				} else if (nodeType == "text") {
					if ("write" in _acl[id][prop] && !el.attr('readonly')) {
						if (_acl[id][prop].write) {
							el.css('visibility', 'initial');
							$('label[for="' + id + '"]').css('visibility', 'initial');
							if (el.hasClass( "rid50-datepicker" ))
								el.datepicker( "option", "disabled", false );

							el.removeAttr('disabled');
						} else {
							if (el.hasClass( "rid50-datepicker" ))
								el.datepicker( "option", "disabled", true );

							el.attr('disabled', 'disabled');
						}
					}
					if ("read" in _acl[id][prop] && !el.attr('readonly')) {
						if (_acl[id][prop].read) {
							$('label[for="' + id + '"]').css('visibility', 'initial');
							if (el.hasClass( "rid50-datepicker" ))
								el.datepicker( "option", "disabled", false );

							el.removeAttr('disabled');
						} else {
							el.css('visibility', 'hidden');
							$('label[for="' + id + '"]').css('visibility', 'hidden');
							if (el.hasClass( "rid50-datepicker" ))
								el.datepicker( "option", "disabled", true );

							el.attr('disabled', 'disabled');
						}
					}
				} else if (nodeType == "radio" || nodeType == "checkbox") {
					if ("write" in _acl[id][prop]) {
						if (_acl[id][prop].write) {
							el.css('visibility', 'initial');
							$('label[for="' + id + '"]').css('visibility', 'initial');
							el.each(function() {
								$('label[for="' + this.id + '"]').css('visibility', 'initial');
							})
							el.removeAttr('disabled');
						} else {
							el.attr('disabled', 'disabled');
						}
					}
					if ("read" in _acl[id][prop]) {
						if (_acl[id][prop].read) {
							$('label[for="' + id + '"]').css('visibility', 'initial');
							el.each(function() {
								$('label[for="' + this.id + '"]').css('visibility', 'initial');
							})
							el.removeAttr('disabled');
						} else {
							el.css('visibility', 'hidden');
							$('label[for="' + id + '"]').css('visibility', 'hidden');
							el.each(function() {
								$('label[for="' + this.id + '"]').css('visibility', 'hidden');
							})
						}
					}
				} else if (nodeType == "fieldset") {
					if ("write" in _acl[id][prop]) {
						if (_acl[id][prop].write) {
							el.css('visibility', 'initial');
							el.removeAttr('disabled');
						} else {
							el.attr('disabled', 'disabled');
						}
					}
					if ("read" in _acl[id][prop]) {
						if (_acl[id][prop].read) {
							el.removeAttr('disabled');
						} else {
							el.css('visibility', 'hidden');
						}
					}
				} else if (nodeType == "button") {
					if ("write" in _acl[id][prop]) {
						if (_acl[id][prop].write) {
							$('#formButtonSet #print').fadeTo("fast", 1.0).removeAttr('disabled');
							el.fadeTo("fast", 1.0).removeAttr('disabled');
						} else {
							$('#formButtonSet #print').fadeTo("fast", .5).attr('disabled', 'disabled');
							el.fadeTo("fast", .5).attr('disabled', 'disabled');
						}
					}
					if ("read" in _acl[id][prop]) {
						if (_acl[id][prop].read) {
							$('#formButtonSet #print').fadeTo("fast", 1.0).removeAttr('disabled');
							el.fadeTo("fast", 1.0).removeAttr('disabled');
						} else {
							$('#formButtonSet #print').fadeTo("fast", .5).attr('disabled', 'disabled');
							el.fadeTo("fast", .5).attr('disabled', 'disabled');
						}
					}
				} 
			}
		}
	}
	
	setAcl(office_groupName);
	setAcl(manager_loginName);
	setAcl(employee_loginName);
	
	setAccordionState();
}

function initAccordion() {
	var divGrid = $('#divGrid'), main_form = $('#main-form'), report_container = $('#report-container'), flexslider_container = $('#flexslider-container'), resourceManagement = $("#resourceManagement");
		
	$("#accordion").accordion({
		//active: false,
		collapsible: false,
		heightStyle: 'content',
		create: function( event, ui ) {
			//applyAcl();
			//setAccordionState();
			//applyAcl(true); 	// true - check for accordion access control list
		},
		beforeActivate: function( event, ui ) {
			switch (ui.newHeader.index()) {
				case 0:
					divGrid.show();
					$('#' + _currentForm).hide();
					report_container.hide();
					flexslider_container.hide();
					resourceManagement.hide();
					break;
				case 2:
					if (ui.newHeader.hasClass( "my-ui-state-disabled" )) {
						return false;
					}
					
					var anchor = $('#' + $('#load-form').attr('data-link'));
					if ($('#application-number').val().match("[/\\\\]")) {
						//anchor.css('visibility', 'initial');
						anchor.removeAttr('disabled');
						anchor.fadeTo("fast", 1.0).attr("href", "#");
						//$('#formButtonSet button').not('#print').fadeTo("fast", 1.0).removeAttr('disabled');
					} else {
						anchor.attr('disabled', 'disabled');
						anchor.fadeTo("fast", .5).removeAttr("href"); 
						//$('#formButtonSet button').not('#print').fadeTo("fast", .5).attr('disabled', 'disabled');
					}
				
					//dataLink.attr('data-link').click();
					$("#" + $('#' + _currentForm).attr('data-link')).click();
					divGrid.hide();
					report_container.hide();
					flexslider_container.hide();
					resourceManagement.hide();						
					break;
				case 4:
					$('#' + _currentForm).hide();
					var keyFieldValue = $("#" + $('#' + _currentForm).attr('data-key-field')).val();
					
					printReport(function(reportName) {
						$.blockUI();
						$('<img src=\"' + _jasperReportsURL + '?reportName=' + reportName + '&applicationNumber=' + _applicationNumber + '&keyFieldValue=' + keyFieldValue + '&renderAs=png\" onload="$.unblockUI()" />').appendTo('#report-container');
					});
					
					$("#signatureImages .drag").each(function() {
						$(this).draggable( "option", "disabled", false );
					})
					
					report_container.show();
				/*
					$(".dragclone").each(function() {
						$(this).attr('id').search(/([0-9]*)$/);
						var id = RegExp.$1;
						if ($(this).attr('id').search(_currentForm) != -1) {
							$('#sign' + id).draggable( "option", "disabled", true );
							//$('#sign' + id).droppable( { accept: '#' + $(this).attr('id')} );
						}	
					})
				*/
					divGrid.hide();
					flexslider_container.hide();
					resourceManagement.hide();
					break;						
				case 6:
					(function() {
						if (_applicationNumber == "")
							return;
							
						url = "json_db_pdo.php";
						$.get(url, {"func":"getAttachmentList", "param":{applicationNumber: _applicationNumber}})
							.done(function( data ) {
								if (isAjaxError(data))
									return;
								
								var o, result;
								if (data.d == undefined)
									result = data;
								else
									result = data.d.Data;
								
								$("#flexslider-container").append("<div class='slider'></div>");
								$(".slider").html(_slider);
						
								$("#attachmentTitles>div").remove();
								var lastRow;
								var rand = Math.random();
								result.forEach(function(r, index) {
									$('<li><img src=\"my_fopen.php?applicationNumber=' + _applicationNumber + '&id=' + r.ID + '&thumb&rand=' + rand + '\" /></li>').appendTo('#carousel .slides');
									$('<li><img data-id=\"' + r.ID + '\" src=\"my_fopen.php?applicationNumber=' + _applicationNumber + '&id=' + r.ID + '&thumb&rand=' + rand + '\" /></li>').appendTo('#slider .slides');
									$('<div><a href="#" data-id=\"' + index + '\">' + r.Title + '</a><br/></div>').appendTo("#attachmentTitles");
								});

								$(function(){
								  $('#carousel').flexslider({
									animation: "slide",
									controlNav: false,
									animationLoop: false,
									slideshow: false,
									itemWidth: 160,
									itemMargin: 5,
									asNavFor: '#slider'
								  });

								  $('#slider').flexslider({
									animation: "slide",
									controlNav: false,
									animationLoop: false,
									slideshow: false,
									sync: "#carousel",
									start: function(slider){
									  $('body').removeClass('loading');
									}
								  });
								});

								$('#slider .slides img').on("click", function(event){
									var url = 'get_image.php?applicationNumber=' + _applicationNumber + '&id=' + this.getAttribute("data-id") + '&rand=' + rand;
									window.open(url, '_blank');
								});
							})
							.fail(function(jqXHR, textStatus, errorThrown) {
								alert("getAttachmentList - error: " + errorThrown);
							});
					
							$('#attachmentTitles>div>a').on("click", function(event){
								$('#slider').flexslider(parseInt(this.getAttribute("data-id")));
							});
					})();
					
					flexslider_container.show();
					divGrid.hide();
					$('#' + _currentForm).hide();
					report_container.hide();
					resourceManagement.hide();
					break;
				case 8:
				case 10:
					divGrid.hide();
					$('#' + _currentForm).hide();
					report_container.hide();
					flexslider_container.hide();
					if (ui.newHeader.index() == 8) {
						$('#jstree_resourcelist').hide();
						$('#jstree_userlist').show();
					} else {
						$('#jstree_userlist').hide();
						$('#jstree_resourcelist').show();
					}
					resourceManagement.show();
					break;
			}
		}
	});
	
//	setAccordionState();
	
	$("#accordion").bind("keydown", function (event) {
		//var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
		var keycode = event.keyCode || event.which;

		if (keycode == 13) {
			$('#getPicture').triggerHandler( "click" );
			//document.getElementById(btnFocus).click();
		}
	});
}

function getAreas() {
	url = "json_db_pdo.php";
	$.get(url, {"func":"getAreas", "param":{}})
		.done(function( data ) {
			if (isAjaxError(data))
				return;
		
			areaNames = [];
			var o, result;
			if (data.d == undefined)
				result = data;
			else
				result = data.d.Data;
			
			//data.d.Data.forEach(function(o) {
			result.forEach(function(r) {
				o = {};
				o.id = r.ID;
				o.label = r.AreaName;
				areaNames.push(o);
			});
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			alert("getAreas - error: " + errorThrown);
		});
	
	var area = $('#area, #area_search');
	var selectedItem;
	
	area.autocomplete({
		autoFocus: true,
		source: areaNames,
	});
}

function isAjaxError(data) {
	if (data && data.constructor == Array) {
		if (data[0] && data[0].error !== undefined) {
			alert (data[0].error);
			return true;
		}
	}
	
	return false;
}

/*
function delRowFunc(){
	//if ($('.tr-application-detail').length > 1) {
		//$(this).off();
		//$(this).off("click", delRowFunc);
		//$(this).off("mousedown", mouseDownFunc);
		//$(this).off("mouseup", mouseUpFunc);

		//$(this.parentNode.parentNode).remove();
	//}
	//else {
		//$('.tr-application-detail').find("input[type='text']").val("");
	//}
}

function mouseDownFunc(){
	$(this).animate({'top': '+=1px', 'left': '+=1px'}, 100);
}

function mouseUpFunc(){
	$(this).animate({'top': '-=1px', 'left': '-=1px'}, 100);
}
*/
function setEventToDeleteRowButtons() {
/*
	$(".deleteRow").each(function() {
		var data = $._data(this, 'events');
	
		$(this).off("click", delRowFunc);
		$(this).off("mousedown", mouseDownFunc);
		$(this).off("mouseup", mouseUpFunc);

		$(this).on("click", delRowFunc);
		$(this).on("mousedown", mouseDownFunc);
		$(this).on("mouseup", mouseUpFunc);
		
	})
	
	//$(".deleteRow").off("click", delRowFunc);
	//$(".deleteRow").off("mousedown", mouseDownFunc);
	//$(".deleteRow").off("mouseup", mouseUpFunc);

	//$(".deleteRow").on("click", delRowFunc);
	//$(".deleteRow").on("mousedown", mouseDownFunc);
	//$(".deleteRow").on("mouseup", mouseUpFunc);
*/	
/*
	$(".deleteRow").each(function() {
		var data = $._data(this, 'events');
		data = $._data(this, 'events');
	})

	$(".deleteRow").off();

	$(".deleteRow").each(function() {
		var data = $._data(this, 'events');
	
		$(this).on("click", delRowFunc);

		data = $._data(this, 'events');

		$(this).on("mousedown", mouseDownFunc);
		$(this).on("mouseup", mouseUpFunc);

		data = $._data(this, 'events');
		
	})
*/	
	
	$(".deleteRow").on("click", function(event){
		var parentNode = this.parentNode.parentNode;
		if ($('.' + $(parentNode).attr("class")).length > 1) {
			//$(this).off();
			$(parentNode).remove();
		}
		else {
			$(parentNode).find("input[type='text']").val("");
		}
	});

	//$(".deleteRow").each(function() {
	//	var data = $._data(this, 'events');
	//	data = $._data(this, 'events');
	//})
	
	$(".deleteRow").on("mousedown", function(){
		$(this).animate({'top': '+=1px', 'left': '+=1px'}, 100);
	});

	$(".deleteRow").on("mouseup", function(){
		$(this).animate({'top': '-=1px', 'left': '-=1px'}, 100);
	});
}	

function getAreaId(areaName) {
	var areaId = null;
	if (areaName != "") {
		areaNames.some(function(o){
			if (o.label == areaName) {
				areaId = o.id;
				return true;
			}
		});
	}
	return areaId;
}

function getAreaName(areaId) {
	var areaName = "";
	areaNames.some(function(o){
		if (o.id == areaId) {
			areaName = o.label;
			return true;
		}
	});
	return areaName;
}

function setAccordionState() {
	var accTabs = $("#accordion > span");
	if (!$(accTabs[0]).hasClass('ui-accordion-header'))
		return;

	accTabs.each(function(idx) {
		if ($(this).hasClass( "my-ui-state-disabled" ))
			$(this).addClass( "ui-state-disabled" );
		else
			$(this).removeClass( "ui-state-disabled" );
	})
		
	var s = jQuery('#grid').jqGrid('getGridParam','selrow');
	if (!s) {
		$(accTabs[2]).addClass( "ui-state-disabled" );			// report preview tab
		$(accTabs[3]).addClass( "ui-state-disabled" );			// drawings and scanned images tab
	}
	
	if (_directors.indexOf(userInfo[0].loginName) == -1)
		$(accTabs[5]).addClass( "ui-state-disabled" );			// access control tab

}

function start(userLoginName, actorsSource, func) {
//	rootDoc = null;
	_rootActors = null;
	userInfo = [];

	getUserIdentities("GetUserInfo", [{loginName:userLoginName}], function() {
//		getActors(false, func);	// false - get Actors from database
//	});		
	
		//var found = false;
		
		var url = 'json_db_pdo.php', param = {'func':'getActors'};
		if (actorsSource == 'xml') {
			url = 'actors.xml';
			param = {};
		}
		
		$.get(url, param)
			.done(function(data) {
				if (isAjaxError(data))
					return;

				_rootActors = data;

				var found = false;
				$(data).find('managers>manager, managers employee').each(function() {
					if ((this.nodeName == "manager" && $(this).attr("name") == userInfo[0].loginName) || userInfo[0].loginName == $(this).text()) {
						found = true;
						return;
					}
				});

				if (found || actorsSource == 'xml') {
					$("#main-div").removeClass("accessRejected");
				} else {
					$("#main-div").addClass("accessRejected");
					return;
				}

				if ($(data).find('department').attr('directors') != undefined)
					_directors = $(data).find('department').attr('directors').split(',');
				else
					_directors = [];
					
				var a = [], name;
				$(data).find('employees employee').each(function() {
					name = $(this).text();
						
					if (a.indexOf(name) == -1)
						a.push(name);
				});

				var loginNames = [];
				a.forEach(function(name){
					var personInfo = {};
					personInfo.loginName = name;
					loginNames.push(personInfo);
				});

				getUserIdentities("GetUserInfo", loginNames, function() {
					if (actorsSource != 'xml') {
						fillUserLoginCombo();
						getActorsStatus();
						loadUserSignatures();
						//applyAcl();
						//console.log("Start");

						//if (!$("#jstree").hasClass("jstree")) {
						//	console.log("SrartInit");
						initDepartmentTree();
							
						//}
						//getAcl();
					} else {
						initDepartmentTree(actorsSource);
						initUserTree();
					}

					if (func != null) {
						func();
					}
				});
			})
			.fail(function() {
				alert("getActors - error");
				//console.log("getActors - error");
			});
	});	
}

function getUserIdentities(url, json, func) {
	var	contentType, data;
	contentType = "application/x-www-form-urlencoded; charset=UTF-8";
	if (idp == "AD") {
		contentType = "application/json; charset=utf-8";
		url = "ASPNetWebService.asmx/" + url;
		data = "{\"loginNames\":" + JSON.stringify(json) + "}";
	//} else if (idp == "LDAP" || (idp == "SAML" && idpSource == "AD")) {
	} else if (idp == "LDAP" || (idp == "SAML" && idpSource == "AD")) {
		//contentType = "application/x-www-form-urlencoded; charset=UTF-8";
		url = "ldap_repo.php";
		data = {"param":{loginNames:JSON.stringify(json)}};
	//} else if (idp == "SAML" && idpSource == "DB") {
	} else if (idp == "SAML" && idpSource == "DB") {
		//url = "get_user_attributes.php";
		//contentType = "application/x-www-form-urlencoded; charset=UTF-8";
		//data = {loginNames:JSON.stringify(json)};
		
		url = "json_db_pdo.php";
		data = {"func":"getUserAttributes",	"param":{loginNames:JSON.stringify(json)}};
	} else {
		alert("Unknown user repository");
		return;
	}
	
	//url = "simpleSAMLSP.php";
	//var type = "GET";
	//var	data = "loginNames=" + JSON.stringify(json);
	//var	data = "loginNames=" + "{\"loginNames\":" + JSON.stringify(json) + "}";

	//var syncSuccess = false;
	$.ajax({ 
		type: "POST",
		url: url,
		//url: "ASPNetWebService.asmx/" + url,
		//url: 'simpleSAMLSP.php?loginName="roman"&password="roman"',
		//url: 'simpleSAMLSP.php',

		async: false,
		contentType: contentType,
		//contentType: "application/json; charset=utf-8",
		//contentType: "application/xml; charset=utf-8",
		//dataType: "xml",
		//dataType: "json",
		//data: { loginNames: json },
		//data: "{\"loginNames\":" + JSON.stringify(json) + "}",
		data: data,
		//data: {'loginNames':JSON.stringify(json)},
		//data: "{}",
		//processData: true,
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			//alert($.parseJSON(XMLHttpRequest.responseText).Message);
			alert("Status: " + errorThrown);
		
			//loginName = $.parseJSON(this.data).loginName;
			//displayName = loginName;
			//syncSuccess = false;
			//asyncSuccess = false;
		},
		success: function(data) {
			//$('#userName').text($(data).text());	//xml response
			//$('#userName').text(data.d);			//json response
			//data.d.Data[0].Groups[1].toString();
			//data.d.Data[0].Groups.length;
			//loginName = data.d.Data[0].LoginName;
			//displayName = data.d.Data[0].DisplayName;
			if (data && data.constructor == Array) {
				if (data[0] && data[0].error !== undefined) {
					if (data[0].error == "1008")
						if (json[0] != undefined)
							alert((jQuery.i18n.prop("UserDoesNotExist")).format(json[0].loginName));
						else
							alert("Unknown user");
					else
						alert("Error: " + data[0].error);
					return;
				}
			}

			var v, result;
			if (data.d == undefined)
				result = data;
			else
				result = data.d.Data;
			
			result.forEach(function(o) {
				v = new Object();
				v.loginName = o.LoginName;
				v.displayName = o.DisplayName;
				userInfo.push(v);
			});
			//syncSuccess = true;
			
			func();
		}
	});
	//return syncSuccess;
}

function getActorsStatus() {
	var v;
	actor = -1, sectionId = null; //, actorSectionNumber = -1;
	if ((v = $(_rootActors).find('manager[name="' + userInfo[0].loginName + '"]')).length != 0) {
		//$("#tabs>ul>li").find('a[href="#tabs-4"] span').text(jQuery.i18n.prop("UserAssignmentTab"));
		//actorSectionNumber = v.closest('section').index();		//a position of an actor's section to determine what records to show to approve
		sectionId =  v.closest('section').attr('id');		//an ID of an actor's section
		actor = actor_enum.manager;
		reportTo = v.closest('section').next().find('manager');		//reportTo[0].attributes[0].value
		//reportTo = v.closest('section').next().find('manager').attr('name');
	} else {
		$(_rootActors).find('manager>employee').each(function(){
			//$("#tabs>ul>li").find('a[href="#tabs-4"] span').text(jQuery.i18n.prop("CreateUpdateDocumentTab"));
			if ($(this).text() == userInfo[0].loginName) {
				//actorSectionNumber = v.index();			//a position of an actor's section to determine what records to show to edit
				actor = actor_enum.employee;
				//v = $(this).closest('manager').attr('name');
				//actorSectionNumber = $(data).find('manager[name="' + v + '"]').index(); 	//a position of an actor's section to determine what records to show
				sectionId = $(this).closest('section').attr('id');		//an ID of an actor's section
				//actorSectionNumber = $(this).closest('section').index(); 				//a position of an actor's section to determine what records to show
																						// or to edit if this is a first section
				return false;
			}
		})
	}
}


function addToUserLoginCombo() {
	$("#userLoginSelect option").remove();
	fillUserLoginCombo();
}

function fillUserLoginCombo() {
	var selectTag = $("#userLoginSelect");
	if (selectTag.children().length != 0)
		return;

	var a = [], val;
	userInfo.forEach(function(o, index) {
		if (index != 0)
			a.push(o.displayName + "|" + o.loginName);
			//a.push(o.displayName + " (" + o.loginName + ") ");
	});
	
	a.sort(
		function(a, b) {
			if (a.toLowerCase() < b.toLowerCase()) return -1;
			if (a.toLowerCase() > b.toLowerCase()) return 1;
			return 0;
		}
	);

	var html;
	var flag = true;
	a.forEach(function(name){
		name = name.split('|');
		if (flag && name[1] == userInfo[0].loginName) {
			flag = false;
			html = '<option selected="selected" value="' + name[1] + '">' + name[0] + '</option>';
		} else
			html = '<option value="' + name[1] + '">' + name[0] + '</option>';
		selectTag.append(html);
	});
	
	$(document).on("change", "#userLoginSelect", function(){
		var val = this.options[this.selectedIndex].value;
		setTimeout(function() {
			$("#resourceManagement").css("display", "none");
			
			resetAcl();
			start(val, 'db', function() {		// 'db' - get Actors from database
				$("#accordion").accordion( "option", "active", 0 );
				//setAccordionState();
				//applyAcl(true); 	// true - check for accordion access control list

				var found = false;
				_directors.some(function(name) {
					if (userInfo[0].loginName == name) {
						$("#jstree>ul>li").show();
						//$("#jstree").jstree("open_all");
						//$("#jstree").jstree("open_node", $("#jstree>ul>li"));	//doesn't work
						$("#jstree>ul>li").each(function(i) {
							$("#jstree").jstree("open_node", this);
						})
						found = true;
						return true;
					}
				})

				if (!found) {
					$("#jstree").jstree("close_all");
					$("#jstree>ul>li").each(function(i) {
						if (sectionId == $(this).attr('id')) {
							$('#jstree').jstree("open_node", $("#jstree>ul>li:nth-child(" + (i + 1) + ")"));
							$("#jstree>ul>li:nth-child(" + (i + 1) + ")").show();
						} else
							$("#jstree>ul>li:nth-child(" + (i + 1) + ")").hide();
					})

					/*
					//jQuery.jstree._reference('#jstree').close_all($("#jstree"));
					var tree_size = $("#jstree>ul>li").length;
					for (var i = 0; i < tree_size; i++) {
						if (i == actorSectionNumber) {
							$('#jstree').jstree("open_node", $("#jstree>ul>li:nth-child(" + (i + 1) + ")"));
							$("#jstree>ul>li:nth-child(" + (i + 1) + ")").show();
						} else
							$("#jstree>ul>li:nth-child(" + (i + 1) + ")").hide();
					}
					*/
				}
			});
		}, 10);
	});
}

function loadUserSignatures() {
	url = "json_db_pdo.php";

	$.get(url, {"func":"getUserSignatureList", "param":{"currentuser": userInfo[0].loginName}})
		.done(function( data ) {
			if (isAjaxError(data))
				return;
			
			var o, result;
			if (data.d == undefined)
				result = data;
			else
				result = data.d.Data;
			
			$("#signatureImages div").remove();
			
			if (result.length == 0)
				return;
			
			var lastRow;
			var rand = Math.random();
			result.forEach(function(r, index) {
				$('<div class="drag" id="sign' + r.ID + '"><li><img class="img-signature" data-id="' + r.ID + '" src="my_fopen.php?id=' + r.ID + '&rand=' + rand + '" /></li></div>').appendTo('#signatureImages');

				//$('.img-signature2').css({
				$('#sign' + r.ID).css({
						"height": r.Height * 72 / r.Resolution,
						"width": r.Width * 72 / r.Resolution,
					});
				
			});

			//$("#signatureImages").droppable( { accept: '.dragclone'} );
			$("#signatureImages>div").draggable({containment: "document", helper: "clone", revert: "invalid", cursor: "auto", scroll: false, opacity: 0.7,
				stop: function (event, ui) {
					var target = "#report-container";
					var pos = $(ui.helper).offset();
					var pos2 = $(target).offset();
					//var pos2 = $('#' + _currentForm).offset();
					//var pos2 = $('#' + _currentForm).offset();
					objName = '#' + _currentForm + ui.helper.find('img').attr('data-id');
					//objName = '#' + ui.helper.find('img').attr('data-id');

					if ($(objName).length == 0)
						return;
						
					$(objName).css({
						//"z-index": 1000,
						"border": "1px dotted green",
						"left": pos.left - pos2.left,
						"top": pos.top - pos2.top,
						"position": "absolute"
					});

					//var percentTop = ((pos.top - pos2.top) * 100) / $(target).height();
					//var percentLeft = ((pos.left - pos2.left) * 100) / $(target).width();
					//var percentTop = ((pos.top - pos2.top) * 100) / $('#' + _currentForm).height();
					//var percentLeft = ((pos.left - pos2.left) * 100) / $('#' + _currentForm).width();
					
					//saveSignature(ui.helper.find('img').attr('data-id'), percentHeght, percentWidth);
					//saveSignature(ui.helper.find('img').attr('data-id'), pos.top - pos2.top, pos.left - pos2.left, percentTop, percentLeft);
					saveSignature(ui.helper.find('img').attr('data-id'), pos.top - pos2.top, pos.left - pos2.left);

					$(objName).draggable({containment: "document", revert: "invalid", cursor: "auto", scroll: false,
						stop: function (event, ui) {
							var target = "#report-container";
							var pos = $(ui.helper).offset();
							var pos2 = $(target).offset();
							//var pos2 = $('#' + _currentForm).offset();
							//var percentTop = ((pos.top - pos2.top) * 100) / $(target).height();
							//var percentLeft = ((pos.left - pos2.left) * 100) / $(target).width();
					
							//saveSignature(ui.helper.find('img').attr('data-id'), percentHeght, percentWidth);							
							//saveSignature(ui.helper.find('img').attr('data-id'), pos.top - pos2.top, pos.left - pos2.left, percentTop, percentLeft);
							//if (pos.top > 0)
							if (!$(ui.helper).hasClass( "my-droppable-out" )) {
								saveSignature(ui.helper.find('img').attr('data-id'), pos.top - pos2.top, pos.left - pos2.left);
								var i = 0;
							}
						}
					});
					
				}
			});

			$("#report-container").droppable({
				over: function (event, ui) {
					$(ui.draggable).removeClass( "my-droppable-out" );
					//$(this).draggable( "option", "disabled", false );
					//$(ui.draggable).css("position","absolute");
				},
				out: function (event, ui) {
					$(ui.draggable).addClass( "my-droppable-out" );
					//$(ui.draggable).css("position","static");
				//var i = 0;
					//$(this).draggable( "option", "disabled", true );
				},
				drop: function(event, ui) {
					ui.draggable.attr('id').search(/sign([0-9]*)/);
					var elementId = _currentForm + RegExp.$1;
					//if (ui.draggable.hasClass('drag')) {
					if (!ui.draggable.hasClass('dragclone') && $(this).find('#' + elementId).length == 0) {
					//if ($('#' + elementId).length == 0) {
						//ui.draggable.removeClass("drag");
						var element = $(ui.draggable).clone();
						ui.draggable.attr('id').search(/sign([0-9]*)/);
						var elementId = _currentForm + RegExp.$1;
						element.attr("id", elementId);
						element.find('img').css({
							"height": element.css('height'),
							"width": element.css('width'),
						});
						
						element.addClass("dragclone");
						$(this).append(element);
						$(ui.draggable).draggable( "option", "disabled", true );
						
						setUserSignatureAsDroppable(ui.draggable, elementId);
					}
				}
			});
			
			//$("#signatureImages div").on("mousedown", function(){
			//	$(this).animate({'top': '+=1px', 'left': '+=1px'}, 100);
			//});

			//$("#signatureImages div").on("mouseup", function(){
			//	$(this).animate({'top': '-=1px', 'left': '-=1px'}, 100);
			//});

			//$('#signatureImages img').on("click", function(event){
			//	$('<div class="certificate-images"><img src="images/certificate.png" />' +
			//	'<a class="tooltip-for-image" href=""><span><img src="my_fopen.php?id=' + this.getAttribute("data-id") + '" /></span></a></div>').appendTo('#' + _currentForm);
			//});
			
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			alert("getUserSignatureList - error: " + errorThrown);
		});
}

function loadStampedSignatures() {
	var target = "#report-container";
	if ($(target + ' div').length != 0) {
		$(target + ' div').remove();
		$(target + ' img').remove();
	}
	
	$.get("json_db_pdo.php", {'func':'getStampedSignatures',
		'param': {
			'schema': _currentForm,
			'data-key-field' : $('#' + _currentForm).attr('data-key-field'),
			'data-key-field-val' : $('#' + $('#' + _currentForm).attr('data-key-field')).val(),
			}
		})
		.done(function( data ) {
			if (isAjaxError(data))
				return;
		
			// if (data && data.constructor == Array) {
				// if (data[0] && data[0].error != undefined) {
					// alert (data[0].error);
					// return;
				// }
			// }
			
			//$('#' + _currentForm + ' .dragclone')
			var element, signObj;
			var rand = Math.random();
			data.forEach(function(r, index) {
				signObj = $('#sign' + r.SignatureID);
				//if ($('#sign' + r.SignatureID).length != 0) {
				if (signObj.length != 0) {
					//element = $('#sign' + r.SignatureID).clone();
					element = signObj.clone();
					element.attr("id", _currentForm + r.SignatureID);
					element.addClass("dragclone");
					signObj.draggable( "option", "disabled", true );
					
					//var formWidth = document.getElementById(_currentForm).style.width;
						//"left": ($('#' + _currentForm).width() * r.LeftPos / 100).toString() + "px",
						//"top": $('#' + _currentForm).height() * r.TopPos / 100 + "px",
					element.css({
						"border": "1px dotted green",					
						"left": r.LeftPos + "px",
						"top": r.TopPos + "px",
						"position": "absolute"
					});
					
					element.find('img').css({
						"height": r.Height * 72 / r.Resolution,
						"width": r.Width * 72 / r.Resolution,
					});
					
					$(target).append(element);
					//$('#' + _currentForm).append(element);
					
					element.draggable({
						containment: "document", revert: "invalid", cursor: "auto", scroll: false,
						stop: function (ev, ui) {
							//var target = "#report-container";
							var pos = $(ui.helper).offset();
							var pos2 = $(target).offset();
							//var pos2 = $('#' + _currentForm).offset();
							
							//var percentTop = ((pos.top - pos2.top) * 100) / $(target).height();
							//var percentLeft = ((pos.left - pos2.left) * 100) / $(target).width();
						
							//saveSignature(ui.helper.find('img').attr('data-id'), percentHeght, percentWidth);							
							//saveSignature(ui.helper.find('img').attr('data-id'), pos.top - pos2.top, pos.left - pos2.left, percentTop, percentLeft);
							//if (pos.top > 0)
							if (!$(ui.helper).hasClass( "my-droppable-out" ))
								saveSignature(ui.helper.find('img').attr('data-id'), pos.top - pos2.top, pos.left - pos2.left);
						}
					});
					
					setUserSignatureAsDroppable(signObj, _currentForm + r.SignatureID);
					//setUserSignatureAsDroppable($('#sign' + r.SignatureID), _currentForm + r.SignatureID);
				} else {
					$('<div id="foreign' + r.SignatureID + '"><li><img class="img-signature" data-id="' + r.SignatureID + '" src="my_fopen.php?id=' + r.SignatureID + '&rand=' + rand + '" /></li></div>').appendTo(target);

					$('#foreign' + r.SignatureID).css({
						"left": r.LeftPos + "px",
						"top": r.TopPos + "px",
						"height": r.Height * 72 / r.Resolution,
						"width": r.Width * 72 / r.Resolution,
						"position": "absolute"
					});
				}
				
				//$('#sign' + r.SignatureID).draggable( "option", "disabled", true );
				//objName = '#' + _currentForm + r.SignatureID;
				//$(objName).css({
			});
		});
}


function setUserSignatureAsDroppable(ui, cloneId) {
	ui.droppable({ accept: '#' + cloneId,
		over: function (event, ui) {
			$(this).draggable( "option", "disabled", false );
		},
		out: function (event, ui) {
			$(this).draggable( "option", "disabled", true );
		},
		drop: function( event, ui ) {
			$.post("json_db_pdo.php", {'func':'deleteSignature',
				'param': {
					'schema': _currentForm,
					'data-key-field' : $('#' + _currentForm).attr('data-key-field'),
					'data-key-field-val' : $('#' + $('#' + _currentForm).attr('data-key-field')).val(),
					'signature-id': ui.draggable.find('img').attr('data-id'),
					}
				})
				.done(function( data ) {
					if (isAjaxError(data))
						return;
				});

			//$(this).attr('id').search(/sign([0-9]*)/);
			//var rg = RegExp.$1;
			//if (ui.draggable.find('img').attr('data-id') == rg) {
			$(ui.draggable).remove();
				//$(this).addClass("drag");
			$(this).draggable( "option", "disabled", false );
			//}
		}
	});
}

function saveSignature(id, top, left) {
	$.post("json_db_pdo.php", {'func':'saveSignature',
		'param': {
			'schema': _currentForm,
			'data-key-field' : $('#' + _currentForm).attr('data-key-field'),
			'data-key-field-val' : $('#' + $('#' + _currentForm).attr('data-key-field')).val(),
			'signature-id': id,
			'employee-name': userInfo[0].displayName,
			'top-pos': top,
			'left-pos': left,
			}
		})
		.done(function( data ) {
			if (isAjaxError(data))
				return;
		});
}


function setRadioButton(groupName, value) {
	if (value != null && value != -1 && $('input:radio[name=' + groupName + ']').length > value)
		$('input:radio[name=' + groupName + ']')[value].checked = true;
}

function setCheckBox(groupName, value, bitmask) {
	//if (value != null && value != -1) {
	if (value != null && value != 0) {
		$('input:checkbox[name=' + groupName + ']').each(function(index) {
			if (value & bitmask)
				$('input:checkbox[name=' + groupName + ']')[index].checked = true;
			bitmask >>= 1;
		});
		//$('input:checkbox[name=' + groupName + ']')[value].checked = true;
	}
}

function printReport(func) {
	error("");
	$.blockUI();
	_jasperReportsServerConnection = false;
	
	function onTimeOutHandler() {
		//if ($('#error-box2').length > 0)
		//	$('#error-box2').remove();
	
		$.unblockUI();
		if (_jasperReportsServerConnection) {
			//showError("");
//			error("");
			var reportName;
			if ("main-form" == _currentForm)
				reportName = "TawzeeApplicationForm";
			else
				reportName = "TawzeeLoadForm";
			
			func(reportName);
			
			//window.open('http://172.16.16.226:8084/TawsilatJasperReports/JasperServlet?ReportName=' + ReportName + '&ApplicationNumber=' + _applicationNumber + '&renderAs=pdf', '_blank');
		} //else
			//$.unblockUI();
	}
	
	var timeoutID = window.setTimeout(onTimeOutHandler, 5000);
	CheckConnection(timeoutID, onTimeOutHandler);
}

function CheckConnection(timeoutID, func) {
	var xhr = new XMLHttpRequest();
	//try {
	var url = _jasperReportsURL + "?CheckConnection&r=" + Math.random();
	xhr.open( "GET", url, true ); 	// true - the asynchronous operation 
	//xhrTimeout = window.setTimeout(onTimeOutHandler, 5000);

	xhr.onload = function(e) {
		//showError("");	
		error("");	
		if (xhr.status != 200) {
			//showError("Error connecting to Jetty Reporting Service: " + xhr.status);
			error("Error connecting to Jetty Reporting Service: " + xhr.status);
			//errBox.val("Error connecting to Jetty Reporting Service: " + xhr.status);
			//$('#error-box').val("Error connecting to Jetty Reporting Service: " + xhr.status);
			//return false;
		} else {
			_jasperReportsServerConnection = true;
			window.clearTimeout(timeoutID);

			func();
		}
	}
	
	xhr.onerror = function() {
		//showError("Jetty Reporting Service is not running");
		error("Jetty Reporting Service is not running");
	};
	
	xhr.timeout = 3000;
	xhr.ontimeout = function () {
		xhr.abort();
		//clearTimeout(xhrTimeout);
		//showError("Jetty Reporting Service is not running");
		error("Jetty Reporting Service is not running");
		//$('#error-box').val("Check if Jetty Reporting Service started");
		//return false;
	}
	/*
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 0) {
			errBox.val("Check if Jetty Reporting Service started");
		// JSON.parse does not evaluate the attacker's scripts.
		//var resp = JSON.parse(xhr.responseText);
		}
	}
	*/
	xhr.send();
}

function error(error) {
	if (error == "") {
		if ($('#error-box').length != 0)
			$('#error-box').remove();
	} else {
		if ($('#error-box').length == 0)
			$('#left-section').append('<input type="text" id="error-box" tabindex="-1" />');
		
		$('#error-box').val(error);
	}
}

toggleJsTree = function() {
	var idx = _directors.indexOf(userInfo[0].loginName);
	var nodes = $('#jstree').jstree(true).get_json("#", {flat:false});
	$(nodes).each(function() {
		if (idx > -1 || sectionId == this.id) {
			this.data.refresh = true;		// true - do not save actors in a database
			$('#jstree').jstree(true).rename_node(this, ($("body[dir='ltr']").length) ? this.data["data-name"] : this.data["data-arname"]);
		}
	})
}

initDepartmentTree = function(actorsSource) {
	var jstree = $('#jstree');

	if (jstree.hasClass("jstree")) {
		//console.log("InitDestroy");
		jstree.jstree('destroy').empty();
		//$("#jstree>ul").remove();
		//$("#jstree").append('<ul></ul>');
	}

	//console.log("InitStart");
	
	var managers, employees, val;
	var departments_data = [], managers_data = [], employees_data = [];
	var department_icon;
	
	$(_rootActors).find('section').each(function(dindex) {
		//$("#jstree>ul").append('<li data-jstree=\'{"type":"department"}\' id="' + $(this).attr('id') + '" data-name="' + $(this).attr('name') + '" data-arname="' + $(this).attr('arname') + '"><a href="#">' + (($("body[dir='ltr']").length) ? $(this).attr('name') : $(this).attr('arname')) + '</a></li>');

		switch($(this).attr('memberof')) {
			case "CONSULTANCY":
				department_icon = 'images/folder_consultancy.png';
				break;
			case "CONTROLOFFICE":
				department_icon = 'images/folder_lightning.png';
				break;
			case "HEADQUARTER":
			default:
				department_icon = 'jstree-folder';
		}
		
		departments_data.push({
			'id': $(this).attr('id'),
			'type': 'department',
			'icon': department_icon,
			//'icon': 'images/folder_consultancy.png',
			'text': ($("body[dir='ltr']").length) ? $(this).attr('name') : $(this).attr('arname'),
			//'state': { 'opened' : true, 'selected' : false },
			'data': {},
			'data': {
				'data-name': $(this).attr('name'),
				'data-arname': $(this).attr('arname'),
				'data-memberof': $(this).attr('memberof'),
			},
		});
		
		managers_data = [];
		managers = $(this).find('manager');
		if (managers.length != 0) {
			//$("#jstree>ul>li:last-child").append('<ul></ul>');
			managers.each(function(mindex) {
				val = $(this).attr('name');
				
				userInfo.every(function(o) {
					if (o.loginName == val) {
						val = o.displayName;
						return false;
					}
					return true;	// not like ".some()", it must be !!!!!!!!
				});

				//$("#jstree>ul>li:last-child>ul").append('<li data-jstree=\'{"type":"manager"}\' data-loginname="' + $(this).attr('name') + '"><a href="#">' + val + '</a></li>');
				
				managers_data.push({
					'type': 'manager',
					'text': val,
					'data': {
						'data-loginname': $(this).attr('name'),
					},
				});
				
				employees_data = [];
				employees = $(this).find('employee');
				if (employees.length != 0) {
					//$("#jstree>ul>li:last-child>ul>li:last-child").append('<ul></ul>');
					employees.each(function(eindex) {
						val = $(this).text();

						userInfo.some(function(o) {
							if (o.loginName == val) {
								val = o.displayName;
								return true;
							}
						});

						//$("#jstree>ul>li:last-child>ul>li:last-child>ul").append('<li data-jstree=\'{"type":"employee"}\' data-loginname="' + $(this).text() + '"><a href="#">' + val + '</a></li>');
						
						employees_data.push({
							'type': 'employee',
							'text': val,
							'data': {
								'data-loginname': $(this).text(),
							},
						});
						
					})
					
					managers_data[mindex].children = employees_data;
				}
			})
			
			departments_data[dindex].children = managers_data;
		}
		
	})

	jstree
		.on("ready.jstree", function (e, data) {
			var idx = _directors.indexOf(userInfo[0].loginName);
			var nodes = data.instance.get_json("#", {flat:false});
			$(nodes).each(function() {
				if (idx > -1)
					data.instance.open_node(this);
				else if (sectionId == this.id)
					data.instance.open_node(this);
				else
					$('#' + this.id).hide(); 
			
			})

			if (actorsSource == 'xml')
				saveActors();
			else //{
			//console.log("InitReady");
				//setTimeout(function() {		// less then 600ms can't debug getAcl();
				document.dispatchEvent(_myCustomEvent);

					//getAcl();
				//}, 100 );
			//}
/*			
			var found = false;
			_directors.some(function(name) {
				if (userInfo[0].loginName == name) {
					$("#jstree>ul>li").each(function(i) {
						$("#jstree").jstree("open_node", this);
					})
					found = true;
					return true;
				}
			})

			if (!found) {
				$("#jstree>ul>li").each(function(i) {
					if (sectionId == $(this).attr('id')) {
						$('#jstree').jstree("open_node", $("#jstree>ul>li:nth-child(" + (i + 1) + ")"));
					} else
						$("#jstree>ul>li:nth-child(" + (i + 1) + ")").hide();
				})						
			}
*/				
		})
		//.on("create_node.jstree", function (e, data) {
		//})
		.on("copy_node.jstree", function (e, data) {
			data.node.data = $.extend(true, {}, data.original.data);
			var nd = data.instance.get_node($("#" + data.node.parent));
			if (nd.type == "department") {
				data.instance.set_type(data.node, "manager");
			} else {
				data.instance.set_type(data.node, "employee");
				data.instance.open_node(nd);
			}
			
			data.original.instance.disable_node(data.original);
			
			saveActors();
		})
		.on("select_node.jstree", function (e, data) {
			var jstree = $('#jstree_resourcelist');
			if (jstree.is(":visible")) {
				jstree = jstree.jstree(true);
				/*
				if (data.node.type == "department") {
					//jstree.find('li>a>i').attr("disabled", "disabled");
					//jstree.disable_node(jstree.get_node($('#j2_1')));
					if (!jstree.element.find('ul').hasClass('jstree-no-checkboxes')) {
						jstree.hide_checkboxes();
						jstree.uncheck_all();
						data.instance.deselect_node(data.node, true);
						return;
					}
				} else {
					if (jstree.element.find('ul').hasClass('jstree-no-checkboxes')) {
						jstree.show_checkboxes();
					}
				}
				*/
				var nodes =	jstree.get_json("#", {flat:true});
				jstree.check_all();

				//var prop = node.type == "department" ? node.data["data-memberof"] : node.data["data-loginname"];
				//<i class="jstree-icon jstree-checkbox2">
				//node.children('.jstree-anchor').find('.jstree-checkbox2')[0]
				//jstree-checked jstree-checked2
				//					if ($(data.event.target).hasClass('jstree-checkbox'))

				var checked;
				var setAcl = function(propToFind) {
					for (var prop in _acl) {
						if (propToFind in _acl[prop]) {
							nodes.some(function(node) {
								if (prop == node.data.id) {
									node = jstree.get_node(node, true);	//get dom
									node = node.children('.jstree-anchor');
									//event = new Event('MouseEvents');
									if ('read' in _acl[prop][propToFind]) {
										//event.target = dnode.find('.jstree-checkbox')[0];
										checked = node.hasClass('jstree-checked');
										if (_acl[prop][propToFind].read && !checked || !_acl[prop][propToFind].read && checked)
											node.find('.jstree-checkbox').click();
											//dnode.children('.jstree-anchor').find('.jstree-checkbox').click();
									}
									if ('write' in _acl[prop][propToFind]) {
										//event.target = dnode.find('.jstree-checkbox2')[0];
										checked = node.hasClass('jstree-checked2');
										if (_acl[prop][propToFind].write && !checked || !_acl[prop][propToFind].write && checked)
											node.find('.jstree-checkbox2').click();
											//dnode.children('.jstree-anchor').find('.jstree-checkbox2').click();
									}
									
									return true;
								}
							})
						}
					}
				}
				
				var propToFind, node;
				if (data.node.type == "department") {
					propToFind = data.node.data["data-memberof"];
					setAcl(propToFind);
				} else if (data.node.type == "manager") {
					//propToFind = data.instance.get_node($("#" + data.node.parent)).data["data-memberof"];
					propToFind = data.instance.get_node(data.node.parent).data["data-memberof"];
					setAcl(propToFind);
					propToFind = data.node.data["data-loginname"];
					setAcl(propToFind);
				} else if (data.node.type == "employee") {
					//node = data.instance.get_node($("#" + data.node.parent));
					node = data.instance.get_node(data.node.parent);
					propToFind = data.instance.get_node(node.parent).data["data-memberof"];
					setAcl(propToFind);
					//propToFind = data.instance.get_node($("#" + data.node.parent)).data["data-loginname"];
					propToFind = data.instance.get_node(data.node.parent).data["data-loginname"];
					setAcl(propToFind);
					propToFind = data.node.data["data-loginname"];
					setAcl(propToFind);
				}
				
				
/*				
				var loggedUser = data.node.data["data-loginname"];		// selected department/manager/employee
				for (var prop in _acl) {
					if (loggedUser in _acl[prop]) {
						nodes.some(function(node) {
							if (prop == node.data.id) {
								node = jstree.get_node(node, true);	//get dom
								if ('read' in _acl[prop][loggedUser]) {
									if (!_acl[prop][loggedUser].read)
										node.children('.jstree-anchor').find('.jstree-checkbox').click();
								}
								if ('write' in _acl[prop][loggedUser]) {
									if (!_acl[prop][loggedUser].write)
										node.children('.jstree-anchor').find('.jstree-checkbox2').click();
								}
								
								return true;
							}
						})
					}
				}
*/				
			}
		})
		.on("rename_node.jstree", function (e, data) {
			if ($("body[dir='ltr']").length)
				data.node.data["data-name"] = data.text;
				//data.node.data.name = data.text;
			else
				data.node.data["data-arname"] = data.text;
				//data.node.data.arname = data.text;

			if (data.node.data != null && data.node.data.refresh != undefined && data.node.data.refresh == true) {
				data.node.data.refresh = false;
				return;
			}
			
			if (data.old != data.text) {
				saveActors();
			}
		})
		.on("delete_node.jstree", function (e, data) {
			var inst = $('#jstree_userlist').jstree(true);
			var nodes = inst.get_json("#", {flat:true});
			
			var found = false;
			for (var prop in _acl) {
				if (data.node.data["data-loginname"] in _acl[prop]) {
					found = true;
					delete _acl[prop][data.node.data["data-loginname"]];
					if (Object.keys(_acl[prop]).length === 0)
						delete _acl[prop];
				}
			}
			
			if (found)
				$("#saveAccessControlSettings").click();
			
			nodes.some(function(node) {
				if (node.data["data-loginname"] == data.node.data["data-loginname"]) {
					inst.enable_node(node);
					return true;
				}
			});
			
			//inst.enable_node($('#j2_18'));
			//data.node.data["data-loginname"]
			//inst.enable_node(inst.get_node(, true));
			saveActors();
		})
		.on("refresh.jstree", function (e, data) {
			var nodes = data.instance.get_json("#", {flat:false});
			$(nodes).each(function() {
				this.data.refresh = true;
				data.instance.rename_node(this, ($("body[dir='ltr']").length) ? this.data.name : this.data.arname);
			})
		})
		.on('keydown.jstree', '.jstree-anchor', function (e) {
		  // e.which 
		})
		.jstree({
			"core" : {
				"multiple" : false,
				"themes" : { "stripes" : true },
				"data" : departments_data,
				
				"check_callback" : function (operation, node, node_parent, node_position, more) {
					//console.log(operation);
					switch(operation) {
						case "create_node":
							var section = xmlHelper.createElementWithAttribute("section", 'id', node.id);
							xmlHelper.appendAttributeToElement(section, 'name', node.text);
							xmlHelper.appendAttributeToElement(section, 'arname', node.text);
							xmlHelper.appendAttributeToElement(section, 'memberof', "");
							xmlHelper.appendNewLineElement(section, 3);
							var sections = $(_rootActors).find("sections");
							sections.append(section);
							
							return true;
						case "copy_node":
							if (!node.data["data-loginname"])
								return false;
						
							var exit = false;
							var nodes = this.element.jstree(true).get_json("#", {flat:true});
							nodes.some(function(o) {
								if (o.type != "department" && o.data["data-loginname"] == node.data["data-loginname"]) {
									exit = true;
									return true;
								}
							})

							if (exit)
								return false;
								
							if (node_parent.type == "employee")
								return false;

							return true;
							break;
						case "move_node":
							return false;
							break;
						case "delete_node":
						case "rename_node":
							return true;
							break;
					}
				
				}
			},
			"contextmenu" : {         
				"items": function(node) {
					var tree = jstree.jstree(true);

					var items = {
						create : {
							"separator_before": false,
							"separator_after": false,
							"label": $.i18n.prop("CreateOffice"),
							"_disabled": function (obj) {
								return $('#jstree_resourcelist').is(":visible") ? true : false;
							},															
							"action": function (obj) {
								var deptname = 'New office';
								$.post("json_db_pdo.php", {'func':'createOU', 'param':{'name':deptname, 'memberof':'HEADQUARTER'}})	// OU - Organizational Unit
									.done(function(data){
										if (isAjaxError(data)) {
											error = true;
											return;
										}
										
										node = tree.create_node(node, {'id':data[0], 'text':deptname, 'type':'department', 'data':{"data-name":deptname, "data-arname":deptname, "data-memberof":"HEADQUARTER"}}, "before");
										tree.edit(node);
									})
									.fail(function(jqXHR, textStatus, errorThrown) {
										alert("createOU - error: " + errorThrown);
										//error = true;
										//return;
									});
							}
						},
						rename: {
							"separator_before": false,
							"separator_after": false,
							//"shortcut"			: 13,
							//"shortcut_label"	: 'F2',
							//"icon"				: "glyphicon glyphicon-leaf",
							"label": $.i18n.prop("Rename"),
							"_disabled": function (obj) {
								return $('#jstree_resourcelist').is(":visible") ? true : false;
							},								
							"action": function (obj) { 
								tree.edit(node);
							}
						},                         
						remove: {
							"separator_before": false,
							"separator_after": false,
							//"shortcut"			: 73,
							//"shortcut_label"	: 'Del',
							//"icon"				: "glyphicon glyphicon-leaf",
							"_disabled": function (obj) { 
								if ($('#jstree_resourcelist').is(":visible") || node.children.length != 0)
									return true;
							},								
							//disabled: node.children.length != 0,
							"label": $.i18n.prop("Remove"),
							"action": function (obj) { 
								tree.delete_node(node);
							}
						},
						membership: {
							"separator_before": false,
							"separator_after": false,
							//"shortcut"			: 73,
							//"shortcut_label"	: 'Del',
							//"icon"				: "glyphicon glyphicon-leaf",
							"_disabled": function (obj) { 
								if ($('#jstree_resourcelist').is(":visible"))
									return true;
							},								
							"label": $.i18n.prop("OUMembership"),
							"action": function (obj) {
								addOUMembership(tree);
							}
						}
					};
					
					if (_directors.indexOf(userInfo[0].loginName) == -1) {
						delete items.rename;
						delete items.create;
						delete items.membership;
						if (node.type == 'department')
							delete items.remove;
					} else {
						if (node.type != 'department') {
							delete items.rename;
							delete items.create;
							delete items.membership;
						}
					}

					//if (_directors.indexOf(userInfo[0].loginName) == -1)
					//	delete items.create;
					
//					if ($('#jstree_resourcelist').is(":visible")) {
//						items.create._disabled = true;
//					}
					
					return items;
				}
			},
			"types" : {
				"department" : {
					"icon" : "jstree-folder",
					"select_node" : false
				},
				//"department-consultancy" : {
				//	"icon": "images/folder_consultancy.png",
				//	"select_node" : false
				//},
				"director" : {
					"icon" : "images/director.png"
				},				
				"manager" : {
					"icon" : "images/manager.png",
				},
				"employee" : {
					"icon" : "images/user.png"
				}
			},
			
			"plugins" : [ "themes", "dnd", "types", "contextmenu" ]
		});
};

function initUserTree() {
	var jstree = $('#jstree_userlist');
	if (jstree.hasClass("jstree")) {
		jstree.jstree('destroy').empty();
	}

	var a = [];
	userInfo.forEach(function(o, index) {
		if (index != 0)
			a.push(o.displayName + "|" + o.loginName);
	});
	
	a.sort(
		function(a, b) {
			if (a.toLowerCase() < b.toLowerCase()) return -1;
			if (a.toLowerCase() > b.toLowerCase()) return 1;
			return 0;
		}
	);

	var idx, type;
	var data = [];
	var nodes = $('#jstree').jstree(true);
	if (nodes) {
		nodes = nodes.get_json("#", {flat:true});
		a.forEach(function(name){
			name = name.split('|');
			idx = _directors.indexOf(name[1]);
			if (idx > -1)
				type = "director";
			else
				type = "employee";
			
			data.push({
				'id': $(this).attr('id'),
				'type': (idx > -1) ? "director" : "employee",
				'text': name[0],
				'state' : (function() {
						var state = { 'opened' : true, 'selected' : false};
						nodes.some(function(node) {
							if (node.data["data-loginname"] == name[1]) {
								state.disabled = true;
								return true;
							}
						})

						return state;
				})(),
				'data': {
					'data-loginname': name[1],
				},
			});
		})
	}
		//$("#jstree_userlist>ul").append('<li data-jstree=\'{"type":"' + type + '"}\' data-loginname="' + name[1] + '">' + name[0] + '</li>');
		
	jstree
		.on("ready.jstree", function (e, data) {
			//jstree.jstree(true).deselect_all(true);
		})
		.on("create_node.jstree", function (e, data) {
			//alert(data.node.data.data);
			saveActors();
		})
		.on("delete_node.jstree", function (e, data) {
			saveActors();
		})
		.jstree({
			"core" : {
				//"themes" : { "stripes" : true },
				"multiple" : false,
				"data" : data,
				"check_callback" : function (operation, node, node_parent, node_position, more) {
					//console.log(operation);
					switch(operation) {
						case "create_node":
							//node.data('loginname', userInfo[1].loginName);
							//saveActors();
							return true;
							break;
						case "delete_node":
							return true;
							break;
						default:
							return false;
					}
				}
			},
			"contextmenu" : {         
				"items": function(node) {
					var tree = jstree.jstree(true);
					var items = {
						add: {
							"separator_before": false,
							"separator_after": false,
							"icon" : false,
							"label": $.i18n.prop("AddNewUser"),
							"action": function (obj) {
								addNewUser(tree);
							}
						},
						remove: {
							"separator_before": false,
							"separator_after": false,
							//"shortcut"			: 73,
							//"shortcut_label"	: 'Del',
							//"icon"				: "glyphicon glyphicon-leaf",
							"label": $.i18n.prop("Remove"),
							"action": function (obj) {
								userInfo.some(function(o, index) {
									if (index != 0 && o.loginName == node.data["data-loginname"]) {
										userInfo.splice(index, 1);
										var emp = $(_rootActors).find('employees>employee:contains("' + node.data["data-loginname"] + '")');
										if (emp.length != 0)
											emp.remove();
										
										return true;
									}
								});

								tree.delete_node(node);
							}
						},
						set: {
							"separator_before": false,
							"separator_after": false,
							"icon" : false,
							"label": "Set",
							"action": function (obj) {
								if (node.type == 'employee') {
									tree.set_type(node, "director");
									_directors.push(node.data["data-loginname"]);
								} else if (node.type == 'director') {
									tree.set_type(node, "employee");
									var idx = _directors.indexOf(node.data["data-loginname"]);
									if (idx > -1) {
										_directors.splice(idx, 1);
									}									
								}
								
								saveActors();
							}
						},
					};
					
					if (_directors.indexOf(userInfo[0].loginName) != -1) {
						if (node.type == 'employee')
							items.set.label = $.i18n.prop("PromoteToDirector");
						else if (node.type == 'director')
							items.set.label = $.i18n.prop("DemoteToEmployee");
					} else {
						delete items.add;
						delete items.remove;
						delete items.set;
					}

					return items;
				}
			},
			"dnd" : {
				"always_copy" : true,
			},
			"types" : {
				"employee" : {
					"icon" : "images/user.png"
				},
				"director" : {
					"icon" : "images/director.png"
				}
			},
			"themes" : {
				//"theme" : "classic",
				"stripes" : true,
				"dots" : true,
				"icons" : true
			},
			
			"plugins" : [ "dnd", "types", "themes", "contextmenu" ]
		});
}


function initResourceTree() {
	var jstree = $('#jstree_resourcelist');
	
	if (jstree.hasClass("jstree")) {
		jstree.jstree('destroy').empty();
	}
	
	var data = [], fields_data = [], node, id;
	var forms = $('.forms, #accordion>span');
	forms.push($('#printForm')[0]);
	forms.each(function(index) {
		data.push({
			'data': {
				'id': this.id,
			},
			'type': (this.nodeName.toLowerCase() == "span") ? "accordion" : (this.nodeName.toLowerCase() == "button") ? "print_button" : "form",
			'text': (function(obj){
				if (obj.nodeName.toLowerCase() == "span")
					return $(obj).text();
				if (obj.nodeName.toLowerCase() == "button")
					return obj.title;
				else
					return $('#' + obj.attributes['data-link'].value).text();
			})(this),
			'state' : { 'opened' : true, 'selected' : false },
		});
		
		fields_data = [];
		var fields = $(this).find('input, select, fieldset.access-control, table.access-control')
							.not('fieldset.access-control input, table.access-control input');
		var labelobj
		fields.each(function() {
			node = {
				'data': {
					'id': (this.type == "radio" || this.type == "checkbox")	? this.name : 
						(this.type == "select-one") ? this.id :
						(this.type == "fieldset" || this.type == "table") ? this.id :
						(this.type == "text" && this.id == "" && this.attributes.class != undefined) ? this.attributes.class.value : this.id,
				},
				'type': (this.nodeName.toLowerCase() == "fieldset") ? "fieldset" : (this.nodeName.toLowerCase() == "table") ? "table" : (this.nodeName.toLowerCase() == "select") ? "select" : "field",
				'text': (function(obj){
					if (obj.type == "radio" || obj.type == "checkbox") {
						labelobj = $('label[for="' + obj.name + '"]');
						if (labelobj.length != 0)
							return labelobj.text().replace(/:$/, "");
						else
							return $('fieldset[id="' + obj.name + '"] legend').text().replace(/:$/, "");
					} else if (obj.type == "text") {
						labelobj = $('label[for="' + obj.id + '"]');
						if (labelobj.length != 0)
							return labelobj.text().replace(/:$/, "");
						else {
							if (obj.attributes['class'] != undefined)
								return obj.attributes['class'].value;
							else {
								if (obj.id == "station-number") {
									fields_data.splice(-1,1);
									return $('label[for="station-no"]').text();
								}
							}
						}
					} else if (obj.nodeName.toLowerCase() == "fieldset") {
						return $('fieldset[id="' + obj.id + '"] legend').text().replace(/:$/, "");
					} else if (obj.nodeName.toLowerCase() == "select") {
						return $('label[for="' + obj.id + '"]').text().replace(/:$/, "");
					} else if (obj.nodeName.toLowerCase() == "table") {
						return $.i18n.prop(obj.attributes['data-label'].value);
					}
				})(this)
			};

			if (!fields_data.length || node.data.id != fields_data[fields_data.length-1].data.id)
				fields_data.push(node);
		})

		data[index].children = fields_data;
	})	
	
	jstree
		.on("ready.jstree", function (e, data) {
			$('#jstree').jstree(true).deselect_all(true);
		})
		.on("check_node.jstree", function (e, data) {
			check_uncheck_node(e, data);
		})
		.on("uncheck_node.jstree", function (e, data) {
			check_uncheck_node(e, data);
		})
		
		//.on("check_node.jstree", check_uncheck_node(e, data))
		//.on("uncheck_node.jstree", check_uncheck_node(e, data))
		
/*		
		.on("check_node.jstree", function (e, data) {
			if (data.event.originalEvent) {
				var jstree = $('#jstree').jstree(true);
				var node = jstree.get_selected(true);
				if (node.length) {
					node = node[0];
					
					var clearChildrenAcls = function(node) {
						$(node.children).each(function(i, id) {
							var node = jstree.get_node(id);
							var prop = node.data["data-loginname"];
							if (Object.keys(_acl[data.node.data.id]).indexOf(prop) != -1) {
								delete _acl[data.node.data.id][prop];
								//if (Object.keys(_acl[data.node.data.id]).length === 0) {
								//	delete _acl[data.node.data.id];
								//}
							}
							//node = jstree.get_node($('#' + id));
							clearChildrenAcls(node);
						})
					}
					//var children;
					//if (node.type == "department")
					//	children = $('#jstree').jstree(true).get_children_dom(node);
						
					var prop = node.type == "department" ? node.data["data-memberof"] : node.data["data-loginname"];
					
					_acl[data.node.data.id] = _acl[data.node.data.id] || {};
					_acl[data.node.data.id][prop] = _acl[data.node.data.id][prop] || {};
					if ($(data.event.target).hasClass('jstree-checkbox'))
						_acl[data.node.data.id][prop].read = true;
						//delete _acl[data.node.data.id][prop].read;
					else if ($(data.event.target).hasClass('jstree-checkbox2'))
						_acl[data.node.data.id][prop].write = true;
						//delete _acl[data.node.data.id][prop].write;
						
					clearChildrenAcls(node);
					
					//if (Object.keys(_acl[data.node.data.id][prop]).length === 0) {
					//	delete _acl[data.node.data.id][prop];
					//	if (Object.keys(_acl[data.node.data.id]).length === 0) {
					//		delete _acl[data.node.data.id];
					//	}
					//}
				}
			}
		})
		.on("uncheck_node.jstree", function (e, data) {
			if (data.event.originalEvent) {
				var node = $('#jstree').jstree(true).get_selected(true);
				if (node.length) {
					node = node[0];
					var prop = node.type == "department" ? node.data["data-memberof"] : node.data["data-loginname"];
					_acl[data.node.data.id] = _acl[data.node.data.id] || {};
					_acl[data.node.data.id][prop] = _acl[data.node.data.id][prop] || {};
					if ($(data.event.target).hasClass('jstree-checkbox'))
						_acl[data.node.data.id][prop].read = false;
					else if ($(data.event.target).hasClass('jstree-checkbox2'))
						_acl[data.node.data.id][prop].write = false;
				}
			}
		})
*/		
		.jstree({
			"core" : {
				"data" : data,
			},
			"checkbox" : {
				"tie_selection"	: false,
				"whole_node" : false,
				"keep_selected_style" : true,
				"three_state" : false
			},
			"types" : {
				"form" : {
					"icon" : "images/form.png"
				},
				"table" : {
					"icon" : "images/table.png"
				},
				"field" : {
					"icon" : "images/field.png"
				},
				"fieldset" : {
					"icon" : "images/fieldset.png"
				},
				"radio" : {
					"icon" : "images/radio.png"
				},				
				"checkbox" : {
					"icon" : "images/checkbox_yes.png"
				},				
				"select" : {
					"icon" : "images/combobox.png"
				},				
				"accordion" : {
					"icon" : "images/accordion.png"
				},
				"print_button" : {
					"icon" : "images/print16.png"
				}
				
			},
			"plugins" : [ "checkbox", "types" ]
		});
}

function check_uncheck_node(e, data) {
	if (data.event.originalEvent) {
		var jstree = $('#jstree').jstree(true);
		var node = jstree.get_selected(true);
		if (node.length) {
			node = node[0];
			
			var clearChildrenAcls = function(node) {
				$(node.children).each(function(i, id) {
					var node = jstree.get_node(id);
					var prop = node.data["data-loginname"];
					if (Object.keys(_acl[data.node.data.id]).indexOf(prop) != -1) {
						delete _acl[data.node.data.id][prop];
						//if (Object.keys(_acl[data.node.data.id]).length === 0) {
						//	delete _acl[data.node.data.id];
						//}
					}
					//node = jstree.get_node($('#' + id));
					clearChildrenAcls(node);
				})
			}
			//var children;
			//if (node.type == "department")
			//	children = $('#jstree').jstree(true).get_children_dom(node);
				
			var prop = node.type == "department" ? node.data["data-memberof"] : node.data["data-loginname"];
			
			_acl[data.node.data.id] = _acl[data.node.data.id] || {};
			_acl[data.node.data.id][prop] = _acl[data.node.data.id][prop] || {};
			if ($(data.event.target).hasClass('jstree-checkbox')) {
				if (e.type == "check_node") {
					_acl[data.node.data.id][prop].read = true;
					if (node.type == "department") {
						delete _acl[data.node.data.id][prop]['read'];
						if (Object.keys(_acl[data.node.data.id][prop]).length === 0) {
							delete _acl[data.node.data.id][prop];
							//if (Object.keys(_acl[data.node.data.id]).length === 0) {
							//	delete _acl[data.node.data.id];
							//}
						}
					}
				} else
					_acl[data.node.data.id][prop].read = false;
			} else if ($(data.event.target).hasClass('jstree-checkbox2')) {
				if (e.type == "check_node") {
					_acl[data.node.data.id][prop].write = true;
					if (node.type == "department") {
						delete _acl[data.node.data.id][prop]['write'];
						if (Object.keys(_acl[data.node.data.id][prop]).length === 0) {
							delete _acl[data.node.data.id][prop];
							//if (Object.keys(_acl[data.node.data.id]).length === 0) {
							//	delete _acl[data.node.data.id];
							//}
						}
					}
				} else
					_acl[data.node.data.id][prop].write = false;
			}
			
			clearChildrenAcls(node);
			
			if (Object.keys(_acl[data.node.data.id]).length === 0) {
				delete _acl[data.node.data.id];
			}
			
			//if (Object.keys(_acl[data.node.data.id][prop]).length === 0) {
			//	delete _acl[data.node.data.id][prop];
			//	if (Object.keys(_acl[data.node.data.id]).length === 0) {
			//		delete _acl[data.node.data.id];
			//	}
			//}
		}
	}
}

saveActors = function(actorsTarget) {	//actorsTarget == undefined - 'db'
	var department, section, mamagers, manager, employee;
	//var department = xmlHelper.createElementWithAttribute("department", 'directorss', $(_rootActors).find('department').attr('directorss'));
	var department = xmlHelper.createElementWithAttribute("department", 'directors', _directors);
	xmlHelper.appendNewLineElement(department, 1);
	var sections = document.createElementNS("", "sections");
	department.appendChild(sections);
	var nodes = $('#jstree').jstree(true).get_json("#", {flat:false});
	var name, arname;
	$(nodes).each(function() {
		xmlHelper.appendNewLineElement(sections, 2);
		section = xmlHelper.createElementWithAttribute("section", 'id', this.id);
		if ($("body[dir='ltr']").length) {
			name = this.text.trim();
			arname = this.data["data-arname"].trim();
			//arName = this.data.arname;
			//arName = $(_rootActors).find('section[id="' + this.id + '"]').attr('arName');
		} else {
			//name = $(_rootActors).find('section[id="' + this.id + '"]').attr('name');
			//name = this.data.name;
			name = this.data["data-name"].trim();
			arname = this.text.trim();
		}

		xmlHelper.appendAttributeToElement(section, 'name', name);
		xmlHelper.appendAttributeToElement(section, 'arname', arname);
		xmlHelper.appendAttributeToElement(section, 'memberof', this.data["data-memberof"].trim());

		sections.appendChild(section);
		xmlHelper.appendNewLineElement(section, 3);
		managers = document.createElementNS("", "managers");
		section.appendChild(managers);
		//$(this).siblings('ul').children('li').children('a').each(function(index) {
		$(this.children).each(function() {
			xmlHelper.appendNewLineElement(managers, 4);
			manager = xmlHelper.createElementWithAttribute("manager", 'name', this.data["data-loginname"]);
			//manager = xmlHelper.createElementWithAttribute("manager", 'name', this.data.loginname);
			managers.appendChild(manager);
			$(this.children).each(function() {
			//$(this).siblings('ul').children('li').children('a').each(function(index) {
				xmlHelper.appendNewLineElement(manager, 5);
				employee = xmlHelper.createElement("employee", this.data["data-loginname"]);
				//employee = xmlHelper.createElement("employee", this.data.loginname);
				manager.appendChild(employee);
			})
			xmlHelper.appendNewLineElement(manager, 4);
		}) 
		xmlHelper.appendNewLineElement(managers, 3);
		xmlHelper.appendNewLineElement(section, 2);
	})
/*	
	$("#jstree>ul>li").children('a').each(function(index) {
		xmlHelper.appendNewLineElement(sections, 2);
		section = xmlHelper.createElementWithAttribute("section", 'id', $(this).parent().attr('id'));
		xmlHelper.appendAttributeToElement(section, 'name', $(this).text().trim());
		xmlHelper.appendAttributeToElement(section, 'arName', $(_rootActors).find('section[id="' + $(this).parent().attr('id') + '"]').attr('arName'));
		sections.appendChild(section);
		xmlHelper.appendNewLineElement(section, 3);
		managers = document.createElementNS("", "managers");
		section.appendChild(managers);
		$(this).siblings('ul').children('li').children('a').each(function(index) {
			xmlHelper.appendNewLineElement(managers, 4);
			manager = xmlHelper.createElementWithAttribute("manager", 'name', $(this).children('span').text());
			managers.appendChild(manager);
			$(this).siblings('ul').children('li').children('a').each(function(index) {
				xmlHelper.appendNewLineElement(manager, 5);
				employee = xmlHelper.createElement("employee", $(this).children('span').text());
				manager.appendChild(employee);
			})
			xmlHelper.appendNewLineElement(manager, 4);
		}) 
		xmlHelper.appendNewLineElement(managers, 3);
		xmlHelper.appendNewLineElement(section, 2);
	});
*/	
	
	xmlHelper.appendNewLineElement(sections, 1);
	xmlHelper.appendNewLineElement(department, 1);
	var employees = $(_rootActors).find("department>employees").clone();
	$(department).append(employees);
	xmlHelper.appendNewLineElement(department, 0);

	$.ajaxSetup({ cache: false, async: true });
	//$.post("xml-write.php", {'fileName': 'actors.xml', 'xml' : $.xml(department)})
	//$.post("json_db_pdo.php", {'func':'saveActors', 'param' : $.xml(department)})
	
	var url = 'json_db_pdo.php', param = {'func':'saveActors', 'param' : $.xml(department)};
	if (actorsTarget == 'xml') {
		url = 'save_file.php';
		param = {'fileName': 'actors.xml', 'param' : $.xml(department)};
	}
	
	$.post(url, param)
		.done(function( data ) {
			if (isAjaxError(data))
				return;
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			errorFound = true;
			alert("saveActors - error: " + errorThrown);
		})
		.always(function() {
			$.ajaxSetup({ cache: false, async: false });
		});
}


this.addOUMembership = function(jstree) {
	var form = $("<div/>");
	var html = '<select id="OUMembershipSelect">' +
				'<option selected="selected" value="CONSULTANCY">' + $.i18n.prop('ConsultancyOffice') + '</option>' +
				'<option value="CONTROLOFFICE">' + $.i18n.prop('EmergencyControlOffice') + '</option>' +
				'<option value="HEADQUARTER">' + $.i18n.prop('Headquarter') + '</option></select>';
    form.html(html);
	form.data(jstree);
    form.dialog({
		title:$.i18n.prop('SelectOUMembership'),
		dialogClass: "no-close",
        height: "auto",
        width: 300,
        modal: true,
		autoOpen: true,
		resizable: false,
		open: function( event, ui ) {
			$(this).dialog( "option", "buttons",
				[{	text: $.i18n.prop('Ok'),
					id: 'OUMembershipButton',
					click: function() {
						//$( this ).dialog( "destroy" )
					}
				},
				{	text: $.i18n.prop('Cancel'),
					click: function() {
						$( this ).dialog( "destroy" )
					}
				}]
			); 
			//$('.ui-dialog-buttonpane button:first').focus();
			$(this).keyup(function(e) {
				if (e.keyCode == 13) {
				   $('.ui-dialog-buttonpane button:first').trigger('click');
				}
			});

		},
		close: function( event, ui ) {
		},
    });
}

this.addNewUser = function(jstree) {
	var form = $("<div/>");
	var html = '<input id="newUserInput" style="margin: 3px 0 3px 13px;" type="text" value="" size="15" />';
				//'<button id="addUserButton">Add User</button>';
    form.html(html);
	form.data(jstree);
    form.dialog({
		title:$.i18n.prop('EnterUserLoginName'),
		dialogClass: "no-close",
        height: "auto", //640,
        width: 300,
        modal: true,
		autoOpen: true,
		resizable: false,
		open: function( event, ui ) {
			$(this).dialog( "option", "buttons",
				[{	text: $.i18n.prop('Ok'),
					id: 'addUserButton',
					click: function() {
						//$( this ).dialog( "destroy" )
					}
				},
				{	text: $.i18n.prop('Cancel'),
					click: function() {
						$( this ).dialog( "destroy" )
					}
				}]
			); 
			//$('.ui-dialog-buttonpane button:first').focus();
			$(this).keyup(function(e) {
				if (e.keyCode == 13) {
				   $('.ui-dialog-buttonpane button:first').trigger('click');
				}
			});

		},
		close: function( event, ui ) {
		},
    });
}

$(function() {
	$(document).on("click", "#OUMembershipButton", function(){
		error("");
		var input = $("#OUMembershipSelect");
		var val = input[0].options[input[0].selectedIndex].value;
		
		var jstree = $(input.parent()).data();
		$(input.parent()).dialog("destroy");
		var node = jstree.get_selected(true)[0];
		//jstree.get_selected(true)[0].data["data-memberof"] = val;
		node.data["data-memberof"] = val;
			//var nd = data.instance.get_node($("#" + data.node.parent));
			//if (nd.type == "department") {
			//	data.instance.set_type(data.node, "manager");

		switch(val) {
			case "CONSULTANCY":
				department_icon = 'images/folder_consultancy.png';
				break;
			case "CONTROLOFFICE":
				department_icon = 'images/folder_lightning.png';
				break;
			case "HEADQUARTER":
			default:
				department_icon = 'jstree-folder';
		}

		jstree.set_icon(node, department_icon);
		
		saveActors();
	});

	$(document).on("click", "#addUserButton", function(){
		//$("#addUserError").detach();
		error("");
		//var obj = $("#jstree_userlist>input");
		var input = $("#newUserInput");
		var loginname = input.val();
		
		var jstree = $(input.parent()).data();
		$(input.parent()).dialog("destroy");
		
		if (loginname.length == 0)
			return;

		var found = false;
		userInfo.some(function(o, index) {
			if (index != 0 && loginname == o.loginName) {
		//html = ('<option {0} disabled="disabled" value="' + i + '">' + nam + '</option>').format((i + 1 == $(date).length) ? 'selected="selected"' : '');
			
				//$(".leftSection").append('<div id="addUserError" style="position:absolute; top:0px; right:10px; color:red; font-size:1.3em;">The user ' + obj.val() + ' already exists</div>');
				//$(".leftSection").append(('<div id="addUserError" style="position:absolute; top:0px; right:10px; color:red; font-size:1.3em;">The user {0} already exists</div>').format(obj.val()));
				//$(".leftSection").append('<div id="addUserError" style="position:absolute; top:0px; right:10px; color:red; font-size:1.3em;">' + (jQuery.i18n.prop("UserExists")).format(obj.val()) + '</div>');
				error((jQuery.i18n.prop("UserExists")).format(loginname));
				//obj.val("");
				found = true;
				return true;
			}
		});
		
		if (found)
			return;
		
		getUserIdentities("GetUserInfo",  [{loginName: loginname}], function () {
			var index = userInfo.length;
			if (userInfo[index - 1].loginName == userInfo[index - 1].displayName) {
				//$(".leftSection").append('<div id="addUserError" style="position:absolute; top:0px; right:10px; color:red; font-size:1.3em;">The user ' + obj.val() + ' does not exist</div>');
				//$(".leftSection").append(('<div id="addUserError" style="position:absolute; top:0px; right:10px; color:red; font-size:1.3em;">The user {0} does not exist</div>').format(obj.val()));
				//$(".leftSection").append('<div id="addUserError" style="position:absolute; top:0px; right:10px; color:red; font-size:1.3em;">' + ($.i18n.prop("UserDoesNotExist")).format(obj.val()) + '</div>');
				error((jQuery.i18n.prop("UserDoesNotExist")).format(loginname));
				userInfo.splice(index - 1, 1);
				//obj.val("");
				//found = true;
				//return false;			// ???????
				return;
			}

			//if (found)
			//	return;

			//initUsersTree();
			addToUserLoginCombo();

			var employees = $(_rootActors).find("employees");
			//employees.append(xmlHelper.createSpaceElement(1));
			employees.append(xmlHelper.createElement("employee", loginname));
			//employees.append(xmlHelper.createNewLineElement(1));
			
			jstree.create_node("#", {'text':userInfo[index - 1].displayName, 'data':{'data-loginname':userInfo[index - 1].loginName}}, "first");
			//jstree.create_node("#", {'text':userInfo[index - 1].displayName, 'data-loginname':userInfo[index - 1].loginName}, "first");
			//jstree.create_node("#", {'text':userInfo[index - 1].displayName}, "first");

			//obj.val("");
/*			
			$.post("json_db_pdo.php", {'func':'saveActors', 'param' : $.xml(_rootActors)})
				.done(function( data ) {
					if (isAjaxError(data))
						return;
				
					// if (data && data.constructor == Array) {
						// if (data[0] && data[0].error != undefined) {
							// alert (data[0].error);
							// return;
						// }
					// }
				})
				.fail(function(jqXHR, textStatus, errorThrown) {
					errorFound = true;
					alert("saveActors - error: " + errorThrown);
				});
*/			
			//$.post("xml-write.php", {'fileName': 'actors.xml', 'xml' : $.xml(_rootActors)},
			//function(data, status){
			//	if (data.error) {
			//		alert("Data: " + data + "\nStatus: " + status);
			//	}
			//}, "text");
		});
	});
	
	$(document).on("click", "#userImportButton", function(){
		start(_userLoginName, 'xml', null);		// 'xml' - get Actors from actors.xml file
	});

	$(document).on("click", "#userExportButton", function(){
		saveActors('xml');
	});
	
	$(document).on("click", "#saveAccessControlSettings", function(){
		if (_acl) {
			$.post('save_file.php', {'fileName': 'acl.json', 'param' : JSON.stringify(_acl)})
				.done(function( data ) {
					if (isAjaxError(data))
						return;
				})
				.fail(function(jqXHR, textStatus, errorThrown) {
					errorFound = true;
					alert("Save Access Control List - error: " + errorThrown);
				});
		}
	});
	
})
/*
function insertForm(that) {
	saveForm(that, "insertForm");
}

function updateForm(that) {
	saveForm(that, "updateForm");
}
*/
//function saveForm(that, func) {
function saveForm(that) {
	var currForm = $('#' + _currentForm);
	
	var formFields, radioFields, checkboxFields; //, tableFields;
	var param = {};

	if ("main-form" == _currentForm) {
		formFields = currForm.find("input[type='text']").not("#error-box").filter(function() {
			return $(this).parent()[0].tagName != "TD";
		});
		formFields = formFields.add($('#controlCenterId'));
		//tableFields = currForm.find(".Switch, .K1000KWT, .K1000AMP, .K1250KWT, .K1250AMP, .K1600KWT, .K1600AMP");
	} else if ("load-form" == _currentForm) {
		formFields = currForm.find("input[type='text']").not("#error-box, #owner-name2, #project-name2, #area2, #block2, #plot2, #construction-exp-date2, #feed-points2").filter(function() {
			return $(this).parent()[0].tagName != "TD";
		});
		formFields = formFields.add($('#application-number')).add($('#power-factor-summer')).add($('#power-factor-winter')).add($('#maximum-loads-summer')).add($('#maximum-loads-winter'));
		//tableFields = currForm.find(".description, .connector-load, .summer-load, .winter-load, .remarks");
	}
	
	radioFields = currForm.find("input[type='radio']");
	checkboxFields = currForm.find("input[type='checkbox']");

	
		//formFields = currForm.find('.app-table');
		
	
		//currForm.find("input[type='text']").not("#application-number, #application-date, #owner-name, #project-name, #area, #block, #plot, #construction-exp-date").val("");
		//currForm.find(':radio').not("input[name='project-type']").prop('checked', false);
		//currForm.find(':checkbox').prop('checked', false);
	
	//var checkup_number = $('#checkup_number'), date_ins = $('#date_ins'), load_new = $('#elc_load_new'), load_old = $('#elc_load_old'), tip = $('#validationCheckupTip');
	//var allFields = $([]).add(checkup_number).add(date_ins).add(load_new).add(load_old).add(tip);
	//tip.html("&nbsp;");	
	//allFields.removeClass( "ui-state-error" );

	var valid = true, areaName;
	
	if ($('#error-box').length == 0)
		$('#left-section').append('<input type="text" id="error-box" tabindex="-1" />');
	
	myHelper.setValidationTip($('#error-box'));
	
	formFields.each(function() {
		//if ($(this).css('visibility') != 'hidden' && $(this).attr('disabled') != 'disabled') {
		if ($(this).attr('disabled') != 'disabled' && $(this).attr('readonly') != 'readonly' && $(this).css('visibility') != 'hidden') {
			if ($(this).attr('data-is-required') == "true") {
				valid = valid && myHelper.isRequired($(this), $.i18n.prop(myHelper.hyphensToCamel(this.id)));
			}
			
			if ($(this).attr('data-validation')) {
				valid = valid && myHelper.checkRegexp($(this), $(this).attr('data-validation'), eval($(this).attr('data-validation-message'))); 
			}
		}
		
		if (!valid)
			return false;

		$(this).removeClass( "ui-state-error" );

		if (this.id == 'area') {
			param['area-id'] = getAreaId(this.value);
			areaName =  this.value;
		}
		param[this.id] = this.value;
	});

	if (!valid)
		return;
	
	$('#error-box').val('');
	
	var indx = 0;
	radioFields.each(function() {
		//var that = $(this);
		if (param[this.name] == undefined) {
			indx = -1;
			param[this.name] = -1;
		}
		indx++;
		//} else {
		//	param[this.name] += this.checked ? 1 : 0;
/*		
			if (param[this.name] == 0)
				return true;	//continue
			if (param[this.name] % 10 != 0) {
				param[this.name] = Math.floor(param[this.name] / 10);
				return true;	//continue
			}
			
			param[this.name] += 10;
*/			
		//}
		
		if (this.checked)
			param[this.name] = indx;
			
		//param[this.name] += this.checked ? 1 : 0;
	});
	
	var bitmask = 8;
	checkboxFields.each(function() {
		if (param[this.name] == undefined) {
			bitmask = 8;
			param[this.name] = 0;
		}
		bitmask >>= 1;
		
		if (this.checked)
			param[this.name] |= bitmask;
		
	})
	
	
	param["table"] = []; param["table"].push({});
	
	var indx = 0;
	var foundValue = false;
	var tr_detail = {};
	
	if ("main-form" == _currentForm)
		tr_detail = $('.tr-application-detail');
	else if ("load-form" == _currentForm)
		tr_detail = $('.tr-load-detail');
	
	//var currentClass = tr_detail.attr('class');
	
	tr_detail.each(function(i, tr) {
		$(tr).find("input[type='text']").each(function(i2, el) {
			if (!foundValue && el.value != "")
				foundValue = true;

			if ($(el).hasClass("ui-state-error"))
				$(el).removeClass("ui-state-error");
			
			param["table"][indx][el.className] = el.value;
		})
		
		if (foundValue) {
			indx++;
			foundValue = false;
			param["table"].push({});
		} else {
			if ($('.' + $(tr).attr('class')).length > 1)
				$(tr).remove();
		}
	})
	
	param["table"].splice(-1, 1);
/*
	if ("main-form" == _currentForm)
		tr_detail = $('.tr-application-detail');
	else if ("load-form" == _currentForm)
		tr_detail = $('.tr-load-detail');
*/	
	tr_detail = $('.' + tr_detail.attr('class'));
	
	valid = true;
	$.each(param["table"], function(i, row) {
		$.each(row, function(key, val) {
			var el = $(tr_detail[i]).find('.' + key);
			if (el.attr('data-is-required'))
				valid = valid && myHelper.isRequired( el, $.i18n.prop(myHelper.hyphensToCamel(el.attr('class'))) );

			if (!valid)
				return false;

			//el.removeClass( "ui-state-error" );
		})

		if (!valid)
			return false;
	})
	
	if (!valid)
		return;
		
		
	var ConnectorLoad = 0, SummerLoad = 0, WinterLoad = 0;
	
	var v;
	tr_detail.each(function(i, tr) {
		v = $(tr).find('td:nth-child(2)>input').val()
		ConnectorLoad += parseFloat(v == "" ? 0 : v);
		v = $(tr).find('td:nth-child(3)>input').val()
		SummerLoad += parseFloat(v == "" ? 0 : v);
		v = $(tr).find('td:nth-child(4)>input').val()
		WinterLoad += parseFloat(v == "" ? 0 : v);
	})

	var r;
	//if (!(ConnectorLoad == 0 && SummerLoad == 0 && WinterLoad == 0)) {
		r = $('.tr-load-detail:last').next();
		r.find('td:eq(1)>input').val(format( "#,##0.##0", ConnectorLoad));
		r.find('td:eq(2)>input').val(format( "#,##0.##0", SummerLoad));
		r.find('td:eq(3)>input').val(format( "#,##0.##0", WinterLoad));
	//} else {
		
/*	
	//Switch, K1000KWT, K1000AMP, K1250KWT, K1250AMP, K1600KWT, K1600AMP - 7 columns
	tableFields.each(function(index) {
		if (index / 7 == Math.floor(index / 7)) {
		
			param["table"].push({});
		}
		
		param["table"][Math.floor(index / 7)][this.className] = this.value;
	});
*/
/*
	param["schema"] = [];
	param["schema"].push({});
	param["schema"][0]["parent-table"] = currForm.attr('data-parent-table');
	param["schema"].push({});
	param["schema"][1]["child-table"] = currForm.attr('data-child-table');
	param["schema"].push({});
	param["schema"][2]["primary-key"] = currForm.attr('data-key-field');
*/

	//if (func == "insertForm") {
	//	param["application-date"] = myHelper.getCurrentDate();
	//}

	if ("main-form" == _currentForm) {
		param["application-number-old-value"] = $('#application-number')[0].defaultValue;
		if (param["application-number-old-value"] == "")
			param["application-date"] = myHelper.getCurrentDate();
	} else if ("load-form" == _currentForm)
		param["file-number-old-value"] = $('#file-number')[0].defaultValue;

	
	param["application-creator"] = userInfo[0].displayName;
	param["office-id"] = sectionId;	
	param["schema"] = _currentForm;
	
	if ( valid ) {
		url = "json_db_pdo.php";
		//data = {"func": func,
		data = {"func": "insertUpdate",
			"param": param
				// docFileNumber:file_number.val(),
				// docApprover:userInfo[0].displayName,
				// docDate:getDate(),
				// //docAreaId:areaId,
				// docAreaId:getAreaId(area.val()),
				// docAreaName:area.val(),
				// //docAreaName:areaName,
				// docBlock:block.val(),
				// docPlot:plot.val(),
				// //docBuilding: building.val(),
				// //docPACINumber:paci_number.val(),
				// docTitle:title.val(),
				// originFileNumber: (newDoc) ? null : $("#newForm").data('originFileNumber'),
				// udateIfExists: udateIfExists,
			};

		var errorFound = false;
		$.post(url, data)
		.done(function(data){
			//if (data.error) {
			//	alert("Data: " + data + "\nStatus: " + status);
			//}
			if (data && data.constructor == Array) {
				if (data[0] && data[0].error !== undefined) {
					errorFound = true;
					if (data[0].error == "1003")
						alert(($.i18n.prop("ApprovedCannotBeModified")).format($('#' + $('#' + _currentForm).attr('data-key-field')).val()));
					else if (data[0].error == "23000")
						alert(($.i18n.prop("AlreadyExists")).format($('#' + $('#' + _currentForm).attr('data-key-field')).val()));
					else
						alert(data[0].error);
					
					return;
				}
			}

			if (data.areaId != undefined) {
				//areaId = data[0];
				areaNames.push({id:data.areaId, label:areaName});
				//areaId = data[0];
				//areaNames.push({id:areaId, label:areaName});
			}
			
			if ("main-form" == _currentForm) {
				//if (func == "insertForm") {
				if (param["application-number-old-value"] == "") {
					if (data.applicationNumber != undefined) {
						param["application-number"] = data.applicationNumber;
						param["application-date"] = data.applicationDate;
					}
					addRowToGrid(param);
				} else {
					//gridReload('customReset', $('#application-number').val());	// custom reset
					gridReload('reset');	// custom reset
				}
				updateLoadForm();
			} else if ("load-form" == _currentForm) {
				if (param["file-number"] != param["file-number-old-value"])
					$('#file-number')[0].defaultValue = $('#file-number')[0].value;
					
				//if ($('#file-number').attr('readonly') == undefined)
				//	$('#file-number').attr('readonly', 'readonly');
			}
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			errorFound = true;
			alert("saveForm - error: " + errorThrown);
		})
		.always(function() {
		});
	}
}

function deleteForm() {
	var attr = $('#' + _currentForm).attr('data-key-field');
	var param = {};
	param[attr] = $('#' + attr).val();
	param["schema"] = _currentForm;
/*
	//param[$('#' + _currentForm).attr('data-key-field')] = $('#' + $('#' + _currentForm).attr('data-key-field')).val();
	param["schema"] = [];
	param["schema"].push({});
	param["schema"].push({});
	param["schema"].push({});
	param["schema"][2]["primary-key"] = attr;
	//param["schema"][2]["primary-key"] = $('#' + _currentForm).attr('data-key-field');
*/

	var url = "json_db_pdo.php";
	var data = {"func":"delete", "param":param};

	$.post(url, data)
		.done(function(data){
			if (data && data.constructor == Array) {
				if (data[0] && data[0].error !== undefined) {
					if (data[0].error == "1003")
						alert(($.i18n.prop("ApprovedCannotBeDeleted")).format(fileNumber));
					else
						alert(data[0].error);
				}
			} else {
				if ("main-form" == _currentForm)
					$grid.jqGrid("delRowData", _rowId);
					
				clearForm();
				
			}
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			alert("deleteDoc - error: " + errorThrown);
		});
}

function clearForm() {
	var currForm = $('#' + _currentForm);
	currForm.find("input[type='text']").val("");
	currForm.find(':radio').prop('checked', false);
	currForm.find(':checkbox').prop('checked', false);

	if ("main-form" == _currentForm) {
		$('#application-number')[0].defaultValue = "";
		$('#app-number-search').val("");
		_applicationNumber = "";
		//$('#application-number').removeAttr('readonly');
		$('#controlCenterId').attr('data-controlcenterid', 0);
		
		$("#grid").jqGrid('resetSelection');
		_rowId = _page = 0;
	} else if ("load-form" == _currentForm) {
		$('#file-number')[0].defaultValue = "";
		//$('#file-number').removeAttr('readonly');
	}

	//$(".dragclone").remove();
	$('#' + _currentForm + ' .dragclone').remove();

/*
	$(".dragclone").each(function() {
		$(this).attr('id').search(/([0-9]*)$/);
		var id = RegExp.$1;
		if ("main-form" == _currentForm || $(this).attr('id').search(_currentForm) != -1) {
			$(this).remove();
		}	
	})
*/	
	updateLoadForm();

	if (currForm.is(':visible')){
		setTimeout(function() {
			setFocus();
			//$('#application-number').focus();
		}, 100 );
	}
}

function updateLoadForm() {										// id="load-form"
	$('#owner-name2').val($('#owner-name').val());
	$('#project-name2').val($('#project-name').val());
	$('#area2').val($('#area').val());
	$('#block2').val($('#block').val());
	$('#plot2').val($('#plot').val());
	$('#construction-exp-date2').val($('#construction-exp-date').val());
	$('#feed-points2').val($('#feed-points').val());

	setAccordionState();
}

function alert(text) {
	var form = $('<div/>');
    form.html(text);
    form.dialog({
        title:jQuery.i18n.prop('Alert'),
		dialogClass: "no-close",
        height: 200,
        width: 400,
        modal: true,
		autoOpen: true,
        buttons: { 
			Ok: function() {
				//console.log("alert dialog box closed");
				$(this).dialog( "close" );
			},
		},
	});
}

this.confirm = function(text, param, func) {
	var form = $("<div/>");
	var html = '<div>' + $.i18n.prop(text) + '</div>';
    form.html(html);
    form.dialog({
		title:$.i18n.prop('Confirm'),
		dialogClass: "no-close",
        height: "auto", //640,
        width: 300,
        modal: true,
		autoOpen: true,
		resizable: false,
		open: function( event, ui ) {
			$(this).dialog( "option", "buttons",
				[{	text: "Ok",
					//id: "buttSave",
					click: function() {
						if (func != null)
							func(param);

						$( this ).dialog( "destroy" )
					}
				},
				{	text: "Cancel",
					click: function() {
						$( this ).dialog( "destroy" )
					}
				}]
			); 
			
			//ui-dialog-buttonset
			$(this).parent().find(".ui-button-text").each(function() {
				var that = $(this);
				if (that.text() == "Ok")
					that.text(jQuery.i18n.prop('Ok'));
				else if (that.text() == "Cancel")
					that.text(jQuery.i18n.prop('Cancel'));
			});
		},
		close: function( event, ui ) {
		},
    });
}

function toggleLanguage(lang, dir) {
	var left = $(".floatLeft");
	var right = $(".floatRight");
	var direction = false; 
	$(left).toggleClass("floatLeft", direction);
	$(left).toggleClass("floatRight", !direction);
	$(right).toggleClass("floatRight", direction);
	$(right).toggleClass("floatLeft", !direction);
	$('body').attr('dir', dir);
	$('html').attr('lang', lang);

	jQuery.i18n.properties({
		name:'Messages', 
		path:'bundle/', 
		mode:'both',
		language: lang,
		callback: function() {

			if (dir == 'ltr') {
				$("body").css("font-size", "80%");
				//$(".ui-jqgrid").css("font-size", "0.8em");
			} else {
				$("body").css("font-size", "90%");
				//$(".ui-jqgrid").css("font-size", "1.0em");
			}
		
			if (dir == 'ltr') {
				$.datepicker.setDefaults( $.datepicker.regional[ lang == "en" ? "" : lang ] );
				$(".rid50-datepicker").datepicker("option", "changeMonth", lang == "en" ? true : false);
			} else {
				$(".rid50-datepicker").datepicker("option", "changeMonth", lang == "en" ? true : false);
				$.datepicker.setDefaults( $.datepicker.regional[ lang == "en" ? "" : lang ] );
			}

			//userAssignment();
			//$('#jstree').jstree("refresh_node", {'id':'#'});
			//$('#jstree').jstree("refresh");
			toggleJsTree();
			toggleGrid(lang);

			$('#copyright').text(jQuery.i18n.prop('Copyright'));

			if (dir == 'ltr') {
				//$.datepicker.setDefaults( $.datepicker.regional[ lang == "en" ? "" : lang ] );
				//$(".rid50-datepicker").datepicker("option", "changeMonth", lang == "en" ? true : false);
			
				//$("#left-section").css("margin", "0 6px 0 0");
				$("#left-section").css("margin-left", "0");
				$("#left-section").css("margin-right", "6px");
				$("#right-section").css("text-align", "right");
				//$("#accordion>span").css("text-align", "right");
				$("#left-section, #right-section").css("box-shadow", "4px 4px 2px #999");
				//$(".ui-accordion .ui-accordion-content").css({'padding': '1em 8px 1em 0px'});
				
				$('form input.text')
				.not('#residence-total-area, #construction-area, #ac-area, #current-load, #extra-load, #load-after-delivery, #conductive-total-load')
				.css({'margin-right':'20px', 'margin-left':'0px'});

				$('label[for="square-meters"]').css({'margin-right':'20px', 'margin-left':'0px'});
				$('label[for="kilo-watt"]').css({'margin-right':'20px', 'margin-left':'0px'});
				
				$('form input[type="radio"]').css({'margin':'0 0 10px 20px'});
				$("#main-form>div>div:first").css('left','auto').css('right','0');
				$("#load-form>div>div:first").css('left','auto').css('right','0');

				$("#possibility-yes, #possibility-no").css("text-align", "left");
				
				//$('#formButtonSet').css({'left':'auto', 'right':'10px'});
				//$('#terms').css({'left':'-20'});
				//$('.vakata-context li > a .vakata-contextmenu-sep').css({'margin':'0 0.5em 0 0 !important'});
				
			} else {
				//$('.vakata-context li > a .vakata-contextmenu-sep').css({'margin':'0 0 0 0.5em !important'});

				//$("#left-section").css("margin", "0, 0, 0, 6px");
				$("#left-section").css("margin-left", "6px");
				$("#left-section").css("margin-right", "0");
				$("#right-section").css("text-align", "left");
				//$("#accordion>span").css("text-align", "right");				
				$("#left-section, #right-section").css("box-shadow", "-4px 4px 2px #999");
				//$(".ui-accordion .ui-accordion-content").css({'padding': '1em 0px 1em 8px'});

				//$('form input.text').css({'margin':'0 0 10px 20px'});
				
				$('form input.text')
				.not('#residence-total-area, #construction-area, #ac-area, #current-load, #extra-load, #load-after-delivery, #conductive-total-load')
				.css({'margin-right':'0px', 'margin-left':'20px'});

				$('label[for="square-meters"]').css({'margin-right':'0px', 'margin-left':'20px'});
				$('label[for="kilo-watt"]').css({'margin-right':'0px', 'margin-left':'20px'});
				
				$('form input[type="radio"]').css({'margin':'0 20px 10px 0'});
				$("#main-form>div>div:first").css('left','0').css('right','auto');
				$("#load-form>div>div:first").css('left','0').css('right','auto');

				//$(".rid50-datepicker").datepicker("option", "changeMonth", lang == "en" ? true : false);
				//$.datepicker.setDefaults( $.datepicker.regional[ lang == "en" ? "" : lang ] );
				
				$("#possibility-yes, #possibility-no").css("text-align", "right");
				
				//$('#formButtonSet').css({'left':'10px', 'right':'auto'});
				//$('#terms').css({'right':'-20'});				
			}

			//$('#terms-button').attr({title: $.i18n.prop('TermsConditions')});
			$('#terms-button').button({ label: $.i18n.prop('TermsConditions')});
			
			$('#add, #newForm').attr({title: $.i18n.prop('AddForm')});
			$('#editForm').attr({title: $.i18n.prop('EditForm')});
			$('#save').attr({title: $.i18n.prop('SaveForm')});
			$('#print, #printForm').attr({title: $.i18n.prop('PrintForm')});
			$('#delete').attr({title: $.i18n.prop('DeleteForm')});
			$('.addRow').attr({title: $.i18n.prop('AddRow')});
			$('.deleteRow').attr({title: $.i18n.prop('DeleteRow')});
			$('#sync').attr({title: $.i18n.prop('GoToLastSelectedRow')});
			
			$("#userImportExport legend").text(($.i18n.prop('Users')));
			$("#userImportButton").button({ label: $.i18n.prop('ImportUsers')});
			$("#userExportButton").button({ label: $.i18n.prop('ExportUsers')});

			$("#userLegend legend, #aclLegend legend").text(($.i18n.prop('Legend')));
			$("#userLegend>span:nth-of-type(1)").text(($.i18n.prop('Office')));
			$("#userLegend>span:nth-of-type(2)").text(($.i18n.prop('ConsultancyOffice')));
			$("#userLegend>span:nth-of-type(3)").text(($.i18n.prop('EmergencyControlOffice')));
			$("#userLegend>span:nth-of-type(4)").text(($.i18n.prop('User')));
			$("#userLegend>span:nth-of-type(5)").text(($.i18n.prop('Manager')));
			$("#userLegend>span:nth-of-type(6)").text(($.i18n.prop('Director')));
			$("#aclLegend>span").text(($.i18n.prop('ReadWrite')));

			$("#saveAccessControlSettings").button({ label: $.i18n.prop('SaveACSettings')});
			
			$('#application-form-link').html($.i18n.prop('ApplicationForm'));
			$('#load-form-link').html($.i18n.prop('LoadRequirements'));
					
			$($("#divGrid>form>div:first:nth-child(1)>span")[0]).text(($.i18n.prop('SearchByFileNumber')));
			$($("#divGrid>form>div:first:nth-child(1)>span")[1]).text(($.i18n.prop('EnableAutosearch')));
			$('#gridSubmitButton').button({ label: $.i18n.prop('Go')});
						
			var obj = $("#accordion>span:nth-child(1)").contents().filter(function() {return this.nodeType == 3;});
			obj.get()[0].textContent = jQuery.i18n.prop('Application');
			$("#accordion>div>div:first>span").text(jQuery.i18n.prop('App'));
			//$("#getPicture").button({ label: $.i18n.prop('getPicture')});

			obj = $("#accordion>span:nth-child(3)").contents().filter(function() {return this.nodeType == 3;});
			obj.get()[0].textContent = jQuery.i18n.prop('Form');

			obj = $("#accordion>span:nth-child(5)").contents().filter(function() {return this.nodeType == 3;});
			obj.get()[0].textContent = jQuery.i18n.prop('ReportPreview');
			$("#signButton").button({ label: $.i18n.prop('Sign')});
			$("#signButton").attr({title: jQuery.i18n.prop('SignDocument')});

			obj = $("#accordion>span:nth-child(7)").contents().filter(function() {return this.nodeType == 3;});
			obj.get()[0].textContent = jQuery.i18n.prop('Drawings');

			obj = $("#accordion>span:nth-child(9)").contents().filter(function() {return this.nodeType == 3;});
			obj.get()[0].textContent = jQuery.i18n.prop('UserAssignment');

			obj = $("#accordion>span:nth-child(11)").contents().filter(function() {return this.nodeType == 3;});
			obj.get()[0].textContent = jQuery.i18n.prop('AccessControl');

			$('label[for="application-number"]').html($.i18n.prop('ApplicationNumber'));
			$('label[for="application-date"]').html($.i18n.prop('ApplicationDate'));
			$('label[for="owner-name"]').html($.i18n.prop('OwnerName'));
			$('label[for="project-name"], label[for="project-name2"]').html($.i18n.prop('ProjectName'));
			$('label[for="controlCenterId"]').html($.i18n.prop('ControlCenter'));
			$('label[for="area"], label[for="area2"]').html($.i18n.prop('Area'));
			$('label[for="block"], label[for="block2"]').html($.i18n.prop('Block'));
			$('label[for="plot"], label[for="plot2"]').html($.i18n.prop('Plot'));
			$('label[for="construction-exp-date"]').html($.i18n.prop('ConstructionExpDate'));

			$('label[for="project-type"]').html($.i18n.prop('ProjectType'));
			$('label[for="private-housing"]').html($.i18n.prop('PrivateHousing'));
			$('label[for="investment"]').text($.i18n.prop('Investment'));
			$('label[for="commercial"]').text($.i18n.prop('Commercial'));
			$('label[for="governmental"]').text($.i18n.prop('Governmental'));
			$('label[for="agricultural"]').text($.i18n.prop('Agricultural'));
			$('label[for="industrial"]').text($.i18n.prop('Industrial'));

			$('label[for="residence-total-area"]').text($.i18n.prop('ResidenceTotalArea'));
			$('label[for="construction-area"]').text($.i18n.prop('ConstructionArea'));
			$('label[for="ac-area"]').text($.i18n.prop('ACArea'));
			$('label[for="square-meters"]').text($.i18n.prop('SquareMeters'));			
			
			$('label[for="current-load"]').text($.i18n.prop('CurrentLoad'));
			$('label[for="extra-load"]').text($.i18n.prop('ExtraLoad'));
			$('label[for="kilo-watt"]').text($.i18n.prop('KiloWatt'));			

			$('label[for="load-after-delivery"]').text($.i18n.prop('MaximumLoadAfterDelivery'));
			$('label[for="conductive-total-load"]').text($.i18n.prop('ConductiveTotalLoad'));

			$('label[for="feed-points"]').text($.i18n.prop('FeedPoints'));
			$('label[for="site-feed-point"]').text($.i18n.prop('SiteFeedPoint'));
			$('label[for="vault"]').text($.i18n.prop('Vault'));
			$('label[for="ground"]').text($.i18n.prop('Ground'));
			$('label[for="mezzanine"]').text($.i18n.prop('Mezzanine'));
			$('label[for="other"]').text($.i18n.prop('Other'));

			$('label[for="requirements"]').text($.i18n.prop('Requirements'));
			$('label[for="build"]').text($.i18n.prop('Build'));
			$('label[for="build2"]').text($.i18n.prop('Build2'));
			$('label[for="build3"]').text($.i18n.prop('Build3'));

			$('#cable-size legend').html($.i18n.prop('CableSize'));
			$('#fuze legend').html($.i18n.prop('Fuze'));
			$('#meter legend').html($.i18n.prop('Meter'));
			
			$('#possibility-yes legend').html($.i18n.prop('PossibilityYes'));
			$('label[for="station-no"]').text($.i18n.prop('StationNo'));
			$('label[for="special-adapter"]').text($.i18n.prop('SpecialAdapter'));
			$('label[for="private-station"]').text($.i18n.prop('PrivateStation'));
			
			$('#possibility-no legend').html($.i18n.prop('PossibilityNo'));
			$('label[for="no-electric-grid-access"]').text($.i18n.prop('NoElectricGridAccess'));
			$('label[for="wanted-another-site"]').text($.i18n.prop('WantedAnotherSite'));
			$('label[for="required-secondary-site"]').text($.i18n.prop('RequiredSecondarySite'));
			
			$('label[for="switchCapacity"]').text($.i18n.prop('SwitchCapacity'));
			$('label[for="kqa"]').text($.i18n.prop('Kqa'));
			$('label[for="number"]').text($.i18n.prop('Number'));
			$('label[for="loadAfterDelivery"]').text($.i18n.prop('LoadAfterDelivery'));
			$('label[for="summer"]').text($.i18n.prop('Summer'));
			$('label[for="winter"]').text($.i18n.prop('Winter'));
			$('label[for="meterSize"]').text($.i18n.prop('MeterSize'));
			$('label[for="amp"]').text($.i18n.prop('Amp'));
			
			$('label[for="load-date"]').text($.i18n.prop('LoadDate'));
			$('label[for="file-number"]').text($.i18n.prop('FileNumber'));
			
			$('label[for="construction-exp-date2"]').text($.i18n.prop('ConstructionExpDate2'));
			$('label[for="owner-name2"]').text($.i18n.prop('ConsumerName'));
			$('label[for="feed-points2"]').text($.i18n.prop('FeedPoints2'));
			
			$('label[for="load_required"]').text($.i18n.prop('load_required'));
			
			$('label[for="description"]').text($.i18n.prop('Description'));
			$('label[for="connector-load"]').text($.i18n.prop('ConnectorLoad'));
			$('label[for="kw"]').text($.i18n.prop('KW'));

			$('label[for="maximum-loads"]').text($.i18n.prop('MaximumDiverseLoads'));
			$('label[for="remarks"]').text($.i18n.prop('Remarks'));
			$('label[for="total-load"]').text($.i18n.prop('TotalLoad'));
			$('label[for="total-load"]').text($.i18n.prop('TotalLoad'));
			$('label[for="power-factor"]').text($.i18n.prop('PowerFactor'));
			
		}				
	});
	
	setScroller();
	//moveScroller();			

}
/*	
this.confirm = function(text, param, func) {
	var form = $("<div/>");
	html = '<div>' + $.i18n.prop(text) + '</div>';
    form.html(html);
    form.dialog({
		title:$.i18n.prop('Confirm'),
		dialogClass: "no-close",
        height: "auto", //640,
        width: 300,
        modal: true,
		autoOpen: true,
		resizable: false,
		open: function( event, ui ) {
			$(this).dialog( "option", "buttons",
				[{	text: "Ok",
					//id: "buttSave",
					click: function() {
						if (func != null)
							func(param);

						$( this ).dialog( "destroy" )
					}
				},
				{	text: "Cancel",
					click: function() {
						$( this ).dialog( "destroy" )
					}
				}]
			); 
			
			//ui-dialog-buttonset
			$(this).parent().find(".ui-button-text").each(function() {
				var that = $(this);
				if (that.text() == "Ok")
					that.text(jQuery.i18n.prop('Ok'));
				else if (that.text() == "Cancel")
					that.text(jQuery.i18n.prop('Cancel'));
			});
		},
		close: function( event, ui ) {
		},
    });
}
*/