/*
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_EnhancedDatagridView = MM_Core.extend({
	
	grid: "",
	bulkOperationHandlers: {},
	bulkOperationCancelRequested: {},
	showHideControlsContainer: "#mm-show-hide-controls-container", //jQuery selector of the container for the controls to show/hide
	totalHiddenContainer: "#mm-total-hidden", //jQuery selector of element containing the number of items hidden
	
	
	init: function(moduleName, entityName) 
	{	
		this.bulkOperationHandlers = {
			"hide" : this.hideSingle.bind(this),
			"show" : this.showSingle.bind(this),
			"delete" : this.deleteSingle.bind(this),
			"set_active" : this.setActiveSingle.bind(this),
			"set_inactive" : this.setInactiveSingle.bind(this),
		};

		return this._super( moduleName, entityName );
	},
	
	setGrid: function(gridReference)
	{
		this.grid = gridReference;		
	},
	
	
	duplicate: function(dialogId, id, width, height)
  	{
		var params = { "id":id, "duplicate":true };
		mmdialog_js.showDialog(dialogId, this.module, width, height, "Create "+this.entityName, params);
  	},


	search: function(queryMeta)
	{
		return this.asyncRequest({ "mm_action" : "search", "queryMeta": JSON.stringify(queryMeta)});
	},
	
	
	asyncRequest: function(params)
	{
		let request = Object.assign({
				"action"	: this.action,
				"module"	: this.module,
				"method"	: this.method
		},params);
		
		return new Promise((resolve,reject) =>
		{
			try 
			{
				jQuery.ajax({  type: "POST",
						  url: wpadmin_url+'admin-ajax.php',
						  data: request,
						  dataType: 'json',
						  dataFilter: mmJSONDatafilter
				}).then(function(data)
				{
					resolve(data);
				});
			}
			catch(err)
			{
				reject(err);
			}
		});
	},
	
	
	htmlentities: function(stringToEncode)
	{
		var encodedString = stringToEncode.replace(/[\u00A0-\u9999<>\&]/g, function(i) {
   			return '&#'+i.charCodeAt(0)+';';
		});
		return encodedString
	},
	
	
	renderStatus: function(value,dataCell,dataObj,grid)
	{
		return (Number(value) == 1) ? { "images":"green-light" } : { "images":"red-light" };
	},
	
	
	refreshView: function()
	{
		if ((this.grid) && (this.grid.dumpCacheAndRefresh))
		{
			this.grid.dumpCacheAndRefresh();
		}
	},
	
	
	abbrevString: function(longString, maxLength)
	{
		maxLength = parseInt(maxLength);
		maxLength = isNaN(maxLength) ? 40 : maxLength;
		return (longString.length > maxLength) ? (longString.substring(0,maxLength) + "...") : longString;
	},

	
	createImageButton: function(image,className,attributes,link)
	{
		let ib = document.createElement("a");
		ib.setAttribute("role","button");
		if (className)
		{
			ib.classList.add(className);
		}
		
		if ((attributes) && (attributes != null))
		{
			for (const attr in attributes)
			{
				ib.setAttribute(attr,attributes[attr]);
			}
		}
		
		if (link)
		{
			ib.href = link;
		}
		ib.innerHTML = image;
		return ib;
	},
	
	
	getBulkopHandler: function(bulkOperationId)
	{
		if (this.bulkOperationHandlers[bulkOperationId])
		{
			return this.bulkOperationHandlers[bulkOperationId];
		}
		else
		{
			return false;
		}
	},
	
	
	bulkOperationCancel: function(operationId,clear)
	{
		if (clear)
		{
			delete this.bulkOperationCancelRequested.operationId;
		}
		else
		{
			this.bulkOperationCancelRequested[operationId] = true;
		}
	},
	
	
	getDefaultBulkOperations: function()
	{
		let operations = [{ "id":"show", "display_name": "Show", "inProgress":"showing", "completed":"set visible"},
						  { "id":"hide", "display_name": "Hide", "inProgress":"hiding", "completed":"hidden"},
						  { "id":"set_active", "display_name": "Set Status to Active", "inProgress":"setting active status for", "completed":"set to active status"},
						  { "id":"set_inactive", "display_name": "Set Status to Inactive", "inProgress":"setting inactive status for", "completed":"set to inactive status"},
					   	  { "id":"delete", "display_name": "Delete", "inProgress":"deleting", "completed":"deleted"}
					  	 ];
		return operations;
	},
	
	
	/**
	 * Administers the processing of a bulk operation.  
     *
	 * @param bulkopMetadata The object representing the operation, as seen in the supportedBulkOperations array
	 * @param selectedIds An array of ids to process
	 */
	bulkopAdminister: async function(bulkopMetadata,selectedIds)
	{
		if (bulkopMetadata.id && Array.isArray(selectedIds) && (selectedIds.length > 0))
		{
			let handler = this.getBulkopHandler(bulkopMetadata.id);
			if (!handler)
			{
				//TODO: log that there was no handler for the operation
				return false;
			}
			let status = "";
			let successCount = 0;
			let errorList = [];
			let concurrent = 3;
			let queue = [];
			let pluralItemName = "Items";
			let title = this.grid.tr(`Bulk ${bulkopMetadata.display_name} ${pluralItemName}`);
			let ipv = bulkopMetadata.inProgress;
			let cpv = bulkopMetadata.completed;
			let pdid = `grid_${this.grid.gridId}_bulkop_progress_dialog`;
			let finishButtonId = `${pdid}_finish`;
			let progId = `grid_${this.grid.gridId}_bulkop_progress_ind`;
			let numItems = selectedIds.length;
			let progressUIHTML = `<div class="mbtDialog" id="${pdid}">` +
    							 `<h3>${title}</h3><span id='${pdid}_inprogress'>` + 
    							 `<p>${ipv} ${selectedIds.length} ${pluralItemName}. ${this.grid.tr("Please wait")} ... </p>` + 
    							 `<div class="mbtBox"><progress id="${progId}" max=${numItems} value=0></div>` + 
								 `<p><span class="mm-ehd-bulkop-current">0</span> ${this.grid.tr("of")} ${numItems} ${pluralItemName} ${cpv}</p></span>` + 
								 `<div id='${pdid}_stats' style='display:none'><i style="font-size:1.3em; position:relative; top:2px; margin-right:4px;" class="fa fa-caret-right mm-icon grey"></i>` + 
								 `<span id='${pdid}_success'></span><br>` +
								 `<i id="${pdid}_error_toggle" style=" font-size:1.3em; position:relative; top:2px; margin-right:4px;" class="fa fa-caret-right mm-icon grey"></i><span id='${pdid}_error_count'></span>`+
								 `<br/><textarea id='${pdid}_errors' style='display:none'></textarea></div>` + 	
    							 `<p><input type='button' id="${finishButtonId}" value="Cancel" onclick="mmjs.bulkOperationCancel(${pdid});" class="mm-ui-button" /></p></div>`;
			if (jQuery && jQuery.blockUI)
			{
				let processQueue = async (pq) => { 
						let promiseArray = pq.map((itemId) => handler(itemId).then(result => { return {"id":itemId,"result":result} }));
						let results = await Promise.all(promiseArray);
						for (const res of results)
						{
							if (res.result && (res.result.type !== "error"))
							{
								successCount++;
							}
							else
							{
								let errorMsg = (res.result.message) ? res.result.message : this.grid.tr("An unknown error occurred");
								errorList.push(`id ${res.id}: ${errorMsg}`);
							}
						}
				};
				jQuery.blockUI({  css: { width: '700px' }, message: progressUIHTML });
				jQuery(`#${finishButtonId}`).click(() => {
					this.bulkOperationCancel(pdid);
				});
				for (let i=0; i<numItems; i++)
				{
					if (this.bulkOperationCancelRequested[pdid])
					{
						status = "cancelled";
						this.bulkOperationCancel(pdid,true);
						break;
					}
					queue.push(selectedIds[i]);
					if (queue.length % concurrent == 0 )
					{
						await processQueue(queue);
						queue = [];
					}
					document.getElementById(progId).value = i+1;
					document.querySelector(`#${pdid} .mm-ehd-bulkop-current`).innerHTML = i+1;
				}
				
				if (queue.length > 0)
				{
					await processQueue(queue);
				}
				
				//operation complete
				jQuery("*").css("cursor", "default"); //restore cursor
				jQuery(`#${pdid}_inprogress`).hide();
				jQuery(`#${finishButtonId}`).off("click");
				jQuery(`#${finishButtonId}`).prop("value","Close");
				jQuery(`#${finishButtonId}`).click(() => {
					this.grid.dumpCacheAndRefresh();
					console.log("Unblocking UI");
					jQuery.unblockUI();
				});
				
				let errorCount = errorList.length;
				jQuery(`#${pdid}_success`).html(`${successCount} ${pluralItemName} ${cpv}`);
				jQuery(`#${pdid}_error_count`).html(`${errorCount} ${this.grid.tr('errors')}`);
				if (errorCount > 0)
				{
					jQuery(`#${pdid}_error_toggle`).click(() => {
						if (jQuery(`#${pdid}_errors`).is(":visible"))
						{
							jQuery(`#${pdid}_error_toggle`).removeClass("fa-caret-down");
							jQuery(`#${pdid}_error_toggle`).addClass("fa-caret-right");
							jQuery(`#${pdid}_errors`).hide();
						}
						else
						{
							jQuery(`#${pdid}_error_toggle`).removeClass("fa-caret-right");
							jQuery(`#${pdid}_error_toggle`).addClass("fa-caret-down");
							jQuery(`#${pdid}_errors`).show();
						}
					});
				}
				jQuery(`#${pdid}_errors`).html(errorList.join("\n"));
				jQuery(`#${pdid}_stats`).show();	
			}
			return true;
		}
		else
		{
			//TODO: log that this function was called incorrectly
			if (!"id" in bulkopMetadata)
			{
				console.log("bulkopAdminister was called, but bulkopMetadata had no 'id' property. Aborting...");
			}
			else
			{
				console.log("bulkopAdminister was called, but selectedIds was empty or not an array. Aborting...");
			}
			return false;
		}
	},
	
	
	hideSingle: async function(itemId)
	{
		//override in subclass
		return this.asyncRequest({ id: itemId, mm_action: "hide" + this.bulkActionKey});
	},
	
	
	showSingle: async function(itemId)
	{
		//override in subclass
		return this.asyncRequest({ id: itemId, mm_action: "show" + this.bulkActionKey});
	},
	
	
	deleteSingle: async function(itemId)
	{
		//override in subclass
		return this.asyncRequest({ id: itemId, mm_action: "remove" });
	},
	
	
	setActiveSingle: async function(itemId)
	{
		//override in subclass
		return this.asyncRequest({ id: itemId, mm_action: "setActive" });
	},
	
	
	setInactiveSingle: async function(itemId)
	{
		//override in subclass
		return this.asyncRequest({ id: itemId, mm_action: "setInactive" });
	},
	
	
	bindByClassName: function(containerSelector, eventName, className, dataProperties, boundFunc)
	{
		if (containerSelector && eventName && className && (boundFunc && ({}.toString.call(boundFunc) === '[object Function]')))
		{
			jQuery(containerSelector).on(eventName,className, (e) => {
				if (dataProperties && Array.isArray(dataProperties))
				{
					let dps = dataProperties.filter( (prop) => { return (prop in e.currentTarget.dataset);})
											.map( prop => e.currentTarget.dataset[prop]);
					boundFunc(...dps);
				}
				else
				{
					boundFunc();
				}
			});
		}
	},
});