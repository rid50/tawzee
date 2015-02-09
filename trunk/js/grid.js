/*
	(function($){
		$.jgrid = {
			defaults : {
				//recordtext: "View!!!!!!! {0} - {1} of {2}",
				//emptyrecords: "No records to view",
				//loadtext: "Loading...",
				//pgtext : "Page#### {0} of {1}"
			},
		}
	})(jQuery);
*/

//$(document).ready(function () {
//    CheckupGrid.setupGrid($("#grid"), $("#pager"), $("#search"), (lang == 'en') ? 'ltr' : 'rtl');
//});
var myCustomSearch;

var $grid;
var _rowId;
var _page;
//    lastSelected, i, n, $ids, id;

function toggleGrid(lang) {
	//$(".tagButton").text($.i18n.prop('Checkup'));
	if (!$grid)
		$grid = $('#grid');
	//var jgrid = $('#grid');
	//jgrid.jqGrid('GridUnload');
	$grid.jqGrid('GridUnload');
	$grid = $('#grid');
	if ($.jgrid.hasOwnProperty("regional") && $.jgrid.regional.hasOwnProperty(lang))
		$.extend($.jgrid,$.jgrid.regional[lang]);

	Grid.setupGrid($grid, $("#pager"), $("#grid_search_field"), (lang == 'en') ? 'ltr' : 'rtl');
	//Grid.setupGrid($("#grid"), $("#pager"), $("#grid_search_field"), (lang == 'en') ? 'ltr' : 'rtl');
	//CheckupGrid.setupGrid($("#grid"), $("#pager"), $("#mysearch"), (lang == 'en') ? 'ltr' : 'rtl');
}

Grid = {
    setupGrid: function (grid, pager, search, direction) {
        //        debugger; 
/*		
		var oldFrom = $.jgrid.from;
		
		// "subclass" $.jgrid.from method and save the last
		// select results in lastSelected variable
		$.jgrid.from = function (source, initalQuery) {
			var result = oldFrom.call(this, source, initalQuery),
				old_select = result.select;
			result.select = function (f) {
				lastSelected = old_select.call(this, f);
				return lastSelected;
			};
			return result;
		};
*/		
        grid.jqGrid({
			direction: direction,
            url: "json_db_pdo.php",
			postData:{"func": "getApps"},
			loadonce: false,
            mtype: "get",
            datatype: "json",
            colNames: [$.i18n.prop('ApplicationNumber'), $.i18n.prop('ApplicationDate'), $.i18n.prop('OwnerName'), $.i18n.prop('ProjectName'), , $.i18n.prop('Area'), $.i18n.prop('Block'), $.i18n.prop('Plot'), $.i18n.prop('ConstructionExpDate'), $.i18n.prop('FeedPoints'), $.i18n.prop('ControlCenter')],
            colModel: [ //http://php.net/manual/en/function.date.php
                        {name: 'ApplicationNumber', index: 'ApplicationNumber', align: 'left', width: '120px', sortable: true, resizable: true, frozen: true, 
							cellattr: function(rowId, val, rawObject) {
								var m = val.match("[/\\\\]");
								if (!m) {
									return " class='make-a-note'";
								}
							}
						},
                        {name: 'ApplicationDate', index: 'ApplicationDate', align: 'center', width: '100px', sortable: true, hidden: false, resizable: false, sorttype: 'date', formatter: 'date', formatoptions: { srcformat: 'Y-m-d', newformat: 'd-M-Y'} }, //DateEntry (src) = "12/31/1999 00:00:00"
                        //{name: 'ApplicationDate', index: 'ApplicationDate', align: 'center', width: '100px', sortable: true, hidden: false, resizable: false, sorttype: 'date', formatter: 'date', formatoptions: { srcformat: 'Y-m-d', newformat: 'd/m/Y'} }, //DateEntry (src) = "12/31/1999 00:00:00"
                        {name: 'OwnerName', index: 'OwnerName', align: 'right', width: '150px', sortable: true, editable: false, resizable: false },
                        {name: 'ProjectName', index: 'ProjectName', align: 'right', width: '120px', sortable: true, editable: false, resizable: true },
                        {name: 'ProjectType', hidden: true },
                        {name: 'Area', index: 'Area', align: 'right', width: '120px', sortable: true, editable: false, resizable: false, searchoptions: { sopt: ['bw']} },
                        {name: 'Block', index: 'Block', align: 'right', width: '40px', sortable: true, editable: false, resizable: false },
                        {name: 'Plot', index: 'Plot', align: 'right', width: '40px', sortable: true, editable: false, resizable: false },
                        {name: 'ConstructionExpDate', index: 'ConstructionExpDate', align: 'right', width: '80px', sortable: true, editable: false, resizable: false },
                        {name: 'FeedPoints', hidden: true },
                        {name: 'ControlCenterId', hidden: true },
                      ],
            rowNum: 20,
            rowList: [20, 40, 60],
			altclass: 'gridAltRows',
			altRows: true,
            loadui: "block",
			hidegrid: false,
            pager: pager,
            sortname: 'ApplicationDate',
            sortorder: "desc",
            viewrecords: true,
            width: 860,
            height: '460px',
            shrinkToFit: false,
            autowidth: false,
            rownumbers: true,
            caption: $.i18n.prop('Application'),
            toppager: false,
			gridComplete:  function() {
				//jQuery("#grid").jqGrid('setFrozenColumns');
			},
			afterInsertRow: function(rowid, rowdata, rowelem) {
			/*
				if (_applicationNumber != null) {
					if (rowdata['ApplicationNumber'] == _applicationNumber) {
						jQuery('#grid').jqGrid('setSelection', rowid);
						_applicationNumber = null;
					}
				}
			*/
			},			
            loadError: function (xhr, st, err) {
                if (window.console) window.console.log('failed');
				alert ("Type: " + st + "; Response: " + xhr.status + " " + xhr.statusText);
                $('#alertContent').html("Type: " + st + "; Response: " + xhr.status + " " + xhr.statusText);
                $('#alert').dialog('open');
            },
            loadComplete: function (event) {
                if (event && event[0] && event[0].error != "") {
					if (window.console) window.console.log(event[0].error);
					alert (event[0].error);
				} else {
					if (_rowId && _page == $grid.jqGrid("getGridParam", "page")) {
						$grid.jqGrid('setSelection', _rowId);
					}
					//this.p.lastSelected = lastSelected; // set this.p.lastSelected
					//scrollTo(5);

					//var s = jQuery('#grid').jqGrid('getGridParam','selrow');
					//if (s != null)
					//	s.focus();
				}
            },
            onSelectRow: function (rowId, status, e) {
				_rowId = rowId;
				_page = $grid.jqGrid("getGridParam", "page");
				
				fillFormFields(this, rowId);
				setAccordionState();
				if (!$('.forms').is(':visible'))
					_currentForm = "main-form";
				//$(".dragclone").remove();
            },
			ondblClickRow: function (rowId) {
				$("#accordion").accordion( "option", "active", 1 );				
			}
            //        }).jqGrid('navGrid', pager, { edit: true, add: false, del: false, search: false, refresh: true });
        }).navGrid("#pager",{ view: true, edit: false, add: false, del: false, search: true, refresh: true, 
								beforeRefresh: function () {
									clearForm();
								}
							},
                            {}, // settings for edit
                            {}, // settings for add
                            {}, // settings for delete
							{
								closeOnEscape:true, 
								onClose: function(){
									delete jQuery('#grid').jqGrid('getGridParam' ,'postData' )['searchField'];
									delete jQuery('#grid').jqGrid('getGridParam' ,'postData' )['searchString'];
									delete jQuery('#grid').jqGrid('getGridParam' ,'postData' )['searchOper'];
								}
							}  // search options
                            //{sopt: ["cn"]} // Search options. Some options can be set on column level        
        );
    }
};

/*
function negativeNumbersFormatter(cellvalue, options, rowObject) {
	console.log(cellvalue);
	console.log(options);
	console.log(rowObject);
}
*/

function fillFormFields(that, rowId) {
	var row = $(that).getRowData(rowId);
	_applicationNumber = row['ApplicationNumber'];
	
	var $applicationNumber = $('#application-number');
	var m = row['ApplicationNumber'].match("[/\\\\]");
	if (!m)
		$applicationNumber.css({'color':'#f00'});
	else
		$applicationNumber.css({'color':'currentcolor'});
	
	$applicationNumber.val(row['ApplicationNumber']);
	$applicationNumber[0].defaultValue = $applicationNumber.val();
	//$('#application-number')[0].defaultValue = $('#application-number')[0].value;
	
	//$applicationNumber.attr('readonly','readonly');
	//$('#application-number').val(row['ApplicationNumber']);
	//$('#application-number').attr('readonly','readonly');


	$('#app-number-search').val(row['ApplicationNumber']);
	//var dt = $.datepicker.parseDate('dd-MM-yy', $('#grid').getCell(rowId, 'ApplicationDate'), {monthNames:$.datepicker.regional[ lang == "en" ? "" : lang ].monthNames});

	var dt;
	if (lang == 'en')
		dt = $.datepicker.parseDate('dd-M-yy', $('#grid').getCell(rowId, 'ApplicationDate'));
	else
		dt = $.datepicker.parseDate('dd-MM-yy', $('#grid').getCell(rowId, 'ApplicationDate'));

	$('#application-date').val($.datepicker.formatDate('dd/mm/yy', dt));
	//$('#application-date').val(row['ApplicationDate']);
	$('#owner-name, #owner-name2').val(row['OwnerName']);
	$('#project-name, #project-name2').val(row['ProjectName']);
	$('#area, #area2').val(row['Area']);
	$('#block, #block2').val(row['Block']);
	$('#plot, #plot2').val(row['Plot']);
	$('#construction-exp-date, #construction-exp-date2').val(row['ConstructionExpDate']);
	$('#feed-points, #feed-points2').val(row['FeedPoints']);

	$('#controlCenterId').attr('data-controlcenterid', row['ControlCenterId']);
	
	//$('#owner-name2, #project-name2, #area2, #block2, #plot2, #construction-exp-date2').attr('readonly','readonly');
	setRadioButton('project-type', row['ProjectType']);
	//$('input:radio[name=project-type]')[row['ProjectType']].checked = true;
	
}


var timeoutHnd = null;
var flAuto = false;
function doSearch(e) {
	if (timeoutHnd) {
		clearTimeout(timeoutHnd)
		timeoutHnd = null;
	}
	
	if (e.keyCode == 13) {
		timeoutHnd = setTimeout(gridReload, 500);
		return;
	}
	if (!flAuto) return;
	// var elem = ev.target||ev.srcElement;
	//if (timeoutHnd) {
	//    clearTimeout(timeoutHnd)
	//}
	
	//var keyCode = (e.keyCode ? e.keyCode : (e.which ? e.which : e.charCode));
	var keyCode = e.keyCode || e.which;

	// 65-90 	: A to Z
	// 8 		: Backspace
	// 46		: Delete
	// 48-57	: 0 to 9
	// 96-105	: 0 to 9 (Numpad)
	//if ((ev.keyCode >= 65 && ev.keyCode <= 90) || ev.keyCode == 8 || ev.keyCode == 46 || (ev.keyCode >= 48 && ev.keyCode <= 57) || (ev.keyCode >= 96 && ev.keyCode <= 105)) {
	if ((keyCode >= 65 && keyCode <= 90) || keyCode == 8 || keyCode == 46 || (keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105)) {
		timeoutHnd = setTimeout(gridReload, 500)
	}
}
function gridReload(searchOrReset, applicationNumber) {
	if (!searchOrReset) {
		if (timeoutHnd) {
			clearTimeout(timeoutHnd);
			timeoutHnd = null;
		}

		clearForm();
/*		
		$('#app-number-search').val("");
		_applicationNumber = "";
		_rowId = _page = 0;
		$("#grid").jqGrid('resetSelection');
*/		
		if ($('#grid').jqGrid('getGridParam' ,'postData' ) != undefined) {
			$('#grid').jqGrid('setGridParam',{postData:{'searchField':'ApplicationNumber'} });
			$('#grid').jqGrid('setGridParam',{postData:{'searchString':$('#grid_search_field').val()} });
			$('#grid').jqGrid('setGridParam',{postData:{'searchOper':'bw'} });
			
			//$($("#grid").navGrid("#pager")[0]).prop('p').postData.searchField = "file_no";
			//$($("#grid").navGrid("#pager")[0]).prop('p').postData.searchString = $('#grid_search_field').val();
			//$($("#grid").navGrid("#pager")[0]).prop('p').postData.searchOper = "bw";
			$("#grid").trigger("reloadGrid", [{current:true}]);

			delete jQuery('#grid').jqGrid('getGridParam' ,'postData' )['searchField'];
			delete jQuery('#grid').jqGrid('getGridParam' ,'postData' )['searchString'];
			delete jQuery('#grid').jqGrid('getGridParam' ,'postData' )['searchOper'];
		}

		//var searchField = "file_no";
		//var searchString = $('#grid_search_field').val();
		//var searchOper = "bw";
		
		//myCustomSearch.triggerSearch();
		
		//jQuery("#grid").jqGrid('searchGrid', options );

		
		//jQuery("#grid").setGridParam({ url: "json_db_pdo.php?searchField=" + searchField + "&searchString=" + searchString + "&searchOper=" + searchOper, page: 1 }).trigger("reloadGrid");
		//jQuery("#grid").setGridParam({ url: "json_db_pdo.php", page: 1 });

		//$("#grid").jqGrid("setColProp", "file_no", { searchoptions: { sopt: ['cn']} }).trigger("reloadGrid");
		//jQuery("#grid").setGridParam({ url: "Checkup/List?search=" + search, page: 1 }).trigger("reloadGrid");
	} else if (searchOrReset == 'search') {
		if ($('#grid').jqGrid('getGridParam' ,'postData' ) != undefined) {
			$('#grid').jqGrid('setGridParam',{postData:{'param':{filter:getSearchFilter()}} });
			$("#grid").trigger("reloadGrid", [{current:true}]);
		}
	} else if (searchOrReset == 'reset') {
		if ($('#grid').jqGrid('getGridParam' ,'postData' ) != undefined) {
			var par = $('#grid').jqGrid('getGridParam' ,'postData');
			if (par.param != undefined && par.param.filter != undefined)
				delete par.param.filter;
			//_applicationNumber = applicationNumber;
			//jQuery("#grid").trigger("reloadGrid");
			//$("#grid").trigger("reloadGrid", [{page:1}]);
			//$("#grid").trigger("reloadGrid", [{current:true}]);
			//$("#grid").setGridParam({page:1}).trigger('reloadGrid');
			if (_page)
				$("#grid").setGridParam({page:_page, current:true}).trigger('reloadGrid');
			else
				$grid.jqGrid('setSelection', _rowId);
		}
	}
}

function addRowToGrid(param) {
	var rowid = 1;
	var len = $grid.getDataIDs().length;
	if (len > 0) {
		rowid = parseInt(len) + 1;
	}

    var colNames = $grid.jqGrid("getGridParam", "colModel");
	var rowData = {};
	$.each(colNames, function(index, value) {
		rowData[value.name] = param[myHelper.camelToHyphens(value.name)];
	})
	
	$grid.jqGrid("delRowData", len);
	$grid.jqGrid("addRowData", rowid, rowData, "first");
	$grid.jqGrid('setSelection', rowid);
}

function scrollTo(idToSelect) {
    var filteredData = $grid.jqGrid("getGridParam", "lastSelected"), j, l,
        idName = $grid.jqGrid("getGridParam", "localReader").id,
        //idToSelect = $("#selectedId").val(),
        rows = $grid.jqGrid("getGridParam", "rowNum"),
        currentPage = $grid.jqGrid("getGridParam", "page"),
        newPage;
    if (filteredData) {
        for (j = 0, l = filteredData.length; j < l; j++) {
            if (String(filteredData[j][idName]) === idToSelect) {
                // j is the 0-based index of the item
                newPage = Math.floor(j / rows) + 1;
                if (newPage === currentPage) {
                    $grid.jqGrid("setSelection", idToSelect);
                } else {
                    // set selrow or selarrrow
                    if ($grid.jqGrid("getGridParam", "multiselect")) {
                        $grid.jqGrid("setGridParam", {
                            page: newPage,
                            selrow: idToSelect,
                            selarrrow: [idToSelect]
                        });
                    } else {
                        $grid.jqGrid("setGridParam", {
                            page: newPage,
                            selrow: idToSelect
                        });
                    }
                    $grid.trigger("reloadGrid", [{current: true}]);
                    break;
                }
            }
        }
        if (j >= l) {
            $grid.jqGrid("resetSelection");
            alert("The id=" + idToSelect + " can't be seen because of the current filter.");
        }
    }
}

function enableAutosubmit(state) {
	flAuto = state;
	jQuery("#gridSubmitButton").attr("disabled", state);
	$('#griid_search_field').focus();
} 