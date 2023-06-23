/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_ImportWizardViewJS = MM_Core.extend({
  
	//1 measures progress in csv records vs a total, 0 means the total is available, so a simple count is displayed
	progressMode: 0, 

	importQueue: [],
	importProgress: 0,
	importStatistics: { "success":0, "errors":0 },
	maxProgress: 0,
	maxSliceSize: 50,
	
	downloadTemplate: function(moduleUrl, templateName)
	{ 
		document.location.href = moduleUrl + "/export_file.php?name="+templateName;
	},
	
	validateForm: function()
	{
		let importFileFromComputer = jQuery('#mm-import-file-from-computer-radio').is(':checked');
		let importType = "local";
		let importSrc = "";

		if (importFileFromComputer)
		{			
			if(document.getElementById('fileToUpload').files.length < 1)
			{
				alert("Please select a file before importing members");
				return false;
			}
			importSrc = document.getElementById('fileToUpload').files[0];
		}
		else
		{
			importType = "remote";
			importSrc = jQuery("#mm-import-file-from-url-source").val();
			
			if ((importSrc == undefined) || (importSrc == ""))
			{
				alert("Please specify an import file URL before importing members");
				return false;
			}
		}
		
		var msg = "When you click OK, the import process will start. The amount of time it takes varies ";
		msg += "based on the number of members being imported. Please be patient and let it run to completion.\n\n";
		msg += "Do you want to continue and import these members as '" + jQuery('#mm-membership-selector :selected').text() + "' members?";
		
		if (confirm(msg))
		{
			return mmjs.scanFile(importType,importSrc);
		}
		else
		{
			return false;
		}
	},
	
	
	scanFile: function(importType, importSrc)
	{
		this.importQueue = [];
		this.progressMode = 0;
		this.importProgress = 0;
		this.importStatistics = { "success":0, "errors":0 } ;
		this.maxProgress = 0;
		this.importCancelled = false;
		
		let instance = this;
		try
		{
			//clear results from previous run and reset dialog state
			this.updateStatus();
			jQuery(".importComplete").hide();
			jQuery(".importProgress").show();
		
			jQuery.blockUI({  css: { width: '700px' }, message: jQuery('#import_status_dialog') });
			let fileSrc = jQuery("input[name='import-file-location']:checked").val();
			let sendNewMemberEmails = jQuery("#mm-send-welcome-email").is(":checked");
			let importMembershipLevel = jQuery("#mm-membership-selector").val();
			let importOpts = {
					"sendNewMemberEmails"   : sendNewMemberEmails,
					"importMembershipLevel" : importMembershipLevel
			};
			if (fileSrc == "computer")
			{
				this.preprocessFile(importType, importSrc, importOpts);
			}
			else if (fileSrc == "url")
			{
				this.runImport(importType, importSrc, importOpts);
			}
			else
			{
				instance.closeImportStatusDialog("No import source selected");
			}
			return false;
		}
		catch (error)
		{
			instance.closeImportStatusDialog(error);
			return false;
		}
	},


	preprocessFile: function(importType, importSrc, importOpts)
	{
		this.progressMode = 1;
		let config = {
			chunk: (results,parser) => {
				if (results.data)
				{
					this.maxProgress += results.data.length;
				}
			},
			complete: () => { this.runImport(importType, importSrc, importOpts); },
			worker: false,
			header: true
		};
		
		return Papa.parse(importSrc, config);
	},
	
	
	runImport: function(importType, importSrc, importOpts)
	{
		let instance = this;
		let config = {
			chunk: (results,parser) => {
				try 
				{
					if (results.data)
					{
						parser.pause();
						this.importQueue.push(...results.data);
						this.processChunk(parser,importOpts);
						this.updateStatus();
					}
				}
				catch (error)
				{
					console.log(error);
					parser.abort();
					this.closeImportStatusDialog(error);
					return false;
				}
			},
			worker: false,
			header: true,
			transformHeader: this.mapHeader,
			complete: () => { this.showSuccessStatusDialog(); }
		};
		
		if (importType == "remote")
		{
			config.download = true; //TODO: is this necessary? does download param prevent streaming?
		}
		
		try
		{
			Papa.parse(importSrc, config);
		}
		catch (error)
		{
			instance.closeImportStatusDialog(error);
		}
		finally
		{
			return false;
		}
	},


	updateStatus: function() 
	{
		if (this.progressMode > 0)
		{
			let unitOfMeasure = (this.progressMode == 1) ? "Members" : "Bytes";
			jQuery("#importProgress").attr("max", this.maxProgress);
			jQuery("#importProgress").attr("value",this.importProgress);
			jQuery("#importProgressText").html(`${this.importProgress}/${this.maxProgress} ${unitOfMeasure}`);
		}
	},
	
	
	processChunk: function(parser,importOpts) 
	{
		if (this.importCancelled)
		{
			parser.abort();
			return false;
		}

		if (this.importQueue.length > 0)
		{
			let sliceSize = (this.maxSliceSize > this.importQueue.length) ? this.importQueue.length : this.maxSliceSize;
			let dataPacket = { "module":this.module, "action":this.action, "method":this.method, "mm_action":"importChunk", "importOpts":JSON.stringify(importOpts) };
			dataPacket.data = JSON.stringify(this.importQueue.splice(0,sliceSize));
			var instance = this;
			jQuery.ajax({
				type: "POST",
				url: MemberMouseGlobal.ajaxurl,
				data: dataPacket,
				dataType: "json"
				}).then(function(response) 
				{
					if ((response == null) || (typeof(response) !== "object"))
					{
						//handle chunk error from server (bail?)
						instance.closeImportStatusDialog("Error communicating with server");
						return false;
					}
					
					//gather the statistics
					if (response.message && (response.type == "success"))
					{
						instance.importStatistics.success += parseInt(response.message.success);
						instance.importStatistics.errors += parseInt(response.message.errors);
					}
					else
					{
						console.log("MM Importer - Received error from server:" + JSON.stringify(response));
						instance.closeImportStatusDialog("An error was received from the server while processing the import data: Aborting");
						return false;
					}

					console.log("Processed " + sliceSize);
					instance.importProgress += sliceSize;
					instance.updateStatus();
					instance.processChunk(parser,importOpts);
				}).fail(function(jqXHR, textStatus) 
				{
					//handle chunk error from server (bail)
					instance.closeImportStatusDialog("Error communicating with server");
					return false;
				});
		}
		else
		{
			parser.resume();
		}
	},

	mapHeader: function(columnName,index)
	{
		let normalized = columnName.toLowerCase().trim();
		let mapping = {
			"username" : "username", 
			"email" : "email", 
			"phone" : "phone", 
			"registration date" : "registrationDate", 
			"expiration date" : "expirationDate", 
			"password" : "password",
			"first name" : "firstName", 
			"last name" : "lastName",
			"billing address" : "billingAddress",
			"billing address line 2" : "billingAddress2",
			"billing city" : "billingCity",
			"billing state" : "billingState", 
			"billing zip code" : "billingZip", 
			"billing country" : "billingCountry", 
			"shipping address" : "shippingAddress", 
			"shipping address line 2" : "shippingAddress2", 
			"shipping city" : "shippingCity", 
			"shipping state" : "shippingState", 
			"shipping zip code" : "shippingZip", 
			"shipping country" : "shippingCountry", 
			"status" : "status", 
			"bundles" : "bundles", 
			"bundle status" : "bundleStatus", 
			"bundle start dates" : "bundleStartDates", 
			"bundle expiration dates" : "bundleExpirationDates"
		};
		if (mapping[normalized])
		{
			return mapping[normalized];
		}
		else
		{
			let positionMapping = {
				0 : "username", 
				1 : "email", 
				2 : "phone", 
				3 : "registrationDate", 
				4 : "expirationDate", 
				5 : "password",
				6 : "firstName", 
				7 : "lastName",
				8 : "billingAddress",
				9 : "billingAddress2",
				10 : "billingCity",
				11 : "billingState", 
				12 : "billingZip", 
				13 : "billingCountry", 
				14 : "shippingAddress", 
				15 : "shippingAddress2", 
				16 : "shippingCity", 
				17 : "shippingState", 
				18 : "shippingZip", 
				19 : "shippingCountry", 
				20 : "status", 
				21 : "bundles", 
				22 : "bundleStatus", 
				23 : "bundleStartDates", 
				24 : "bundleExpirationDates"
			};
			if (positionMapping[index])
			{
				return positionMapping[index];
			}
		}
		return columnName;
	},


	closeImportStatusDialog: function(msg)
	{
		this.importCancelled = true;
		if (msg)
		{
			jQuery.unblockUI({ onUnblock: (data) => { 
				this.messageHandler(msg);
			 }});
		}
		else
		{
			jQuery.unblockUI();
		}
	},


	showSuccessStatusDialog: function()
	{
		//TODO: get success and error counts
		jQuery("#importSuccessCount").html(mmjs.importStatistics.success);
		jQuery("#importErrorCount").html(mmjs.importStatistics.errors);
		jQuery(".importProgress").hide();
		jQuery(".importComplete").show();
	},

	cancelImport: function()
	{
		this.importCancelled = true;
	},
	
	
	errorHandler: function(error)
	{
		alert(error);
	},

	messageHandler: function(msg)
	{
		alert(msg);
	},
});

var mmjs = new MM_ImportWizardViewJS("MM_ImportWizardView", "Import Wizard");