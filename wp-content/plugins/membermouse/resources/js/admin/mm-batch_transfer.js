/**
 * This library is intended to facilitate the transfer of potentially large amounts of data by requesting the data from the server in chunks, 
 * which are then reassembled client-side
 * 
 * Requirements: 
 * + The Dexie.js library must be loaded prior to this library being used to perform a receive (server export). 
 * + TextDecoder support is required for sends (server imports), so browsers that lack TextDecoder support must load the polyfill
 *   located in lib/fast-text-encoding/text.min.js prior to the send being initiated.
 * + jQuery must be in scope and bound to the variable 'jQuery'
 */ 


/**
 * Envelope class meant to carry data in a known format
 * 
 * @namespace MemberMouse Plugin
 * @name MembermouseBatchTransferData
 * @class MembermouseBatchTransfer value object
 */


function MembermouseBatchTransferData(command,payload)
{
	this.payload = payload?payload:{};
	this.chunkSize = 100;
	this.chunkNumber;
	this.batchID = "";
	this.command = command;
}


/**
 * MembermouseBatchTransfer is a utility class to provide async batch management
 * 
 * @namespace MemberMouse Plugin
 * @name  MembermouseBatchTransfer
 * @class MembermouseBatchTransfer chunk/batch manager
 * 
 */

/**
 * MembermouseBatchTransfer constructor
 * 
 * @param chunkSize The max number of records or datapoints to retrieve/send at once
 * @param initialUrl The server-side url to call for batch initialization/registration
 * @param chunkUrl The server-side url to call when retrieving chunked data
 * @param postvars additional custom post variables
 */
function MembermouseBatchTransfer(configChunkSize, configInitialUrl, configChunkUrl, postVariables, callbackFunctionRef)
{
	this.STATUS_OK = 1;
	this.STATUS_FAILED = 0;
	this.STATUS_CANCELLED = 2;
	this.cancelled = false;
	
	this.chunkSize = 100; //default, modify this on extension when appropriate
	this.initialUrl = "";
	this.chunkUrl = "";
	
	this.chunkCounter = 0; //current count of successfully transferred chunks, used for status
	this.totalChunks = 0; //set at runtime
	this.currentRecord = 0; //current count of successfully transferred records. Stored separately from chunk count in case chunk count changes dynamically
	this.totalRecords = 0; //set at runtime
	
	this.dbName = "membermouse_batch_storage"; //name of indexeddb database
	this.batchID = "";
	this.errorQueue = [];
	
	this.tracker = [];
	this.chunkBoundaryMap = [];
	
	this.indexedDB =  window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB || window.shimIndexedDB; //compatible in most browsers
	this.db = "";
	this.srcArrayBuffer = "";
	this.maxSimultaneous = 4;
	this.defaultExportFilename = "membermouse_export.csv";
	this.defaultExportMimeType = "application/octet-stream";
	
	this.reader = "";
	 
	this.callbackFunction = callbackFunctionRef;
	this.postvars = postVariables;
	
	
	if (!isNaN(configChunkSize))
	{
		this.chunkSize = configChunkSize;
	}
	
	if ((typeof(configInitialUrl) !== 'undefined') && (configInitialUrl != ""))
	{
		this.initialUrl = configInitialUrl;
	}
	
	if ((typeof(configChunkUrl) !== 'undefined') && (configChunkUrl != ""))
	{
		this.chunkUrl = configChunkUrl;
	}
	else
	{
		this.chunkUrl = this.initialUrl;
	}
}


/**
 * Contact the server, send configuration information,  and retrieve information necessary to administer the batch (like total number of records)
 * The return value may also contain the first chunk of data. If chunk size > total records, the entire resultset is returned in the first chunk
 * 
 * @memberOf MembermouseBatchTransfer
 * @param {Object} values Object containing data necessary to configure the server. IE. in an export, values will contain the export criteria
 */
MembermouseBatchTransfer.prototype.initializeBatchReceive = function(values)
{
	if ((this.initialUrl == "") || (this.chunkUrl == ""))
	{
		//Not configured correctly, exit
		return this.errorHandler("Either the initial url or chunk url were not set, exiting batch receive");
	}
	var commandPacket = new MembermouseBatchTransferData("initBatchReceive",values);
	commandPacket.chunkSize = this.chunkSize;
	
	if(this.postvars!=null && this.postvars != undefined)
	{
		for(var eachvar in this.postvars)
		{
			commandPacket[eachvar] = this.postvars[eachvar];
		}
	}
	var instance = this;
	jQuery.ajax({
		  type: "POST",
		  url: this.initialUrl,
		  data: commandPacket,
		  dataType: "json"
		}).then(function(response) 
		{
			if (((response == null) || (typeof(response) !== "object")) || !('payload' in response) || !('batchID' in response) || (response.batchID.length <1))
			{
				return instance.errorHandler();
			}
			instance.batchID = response.batchID;
			instance.totalRecords = parseInt(response.totalRecords);
			instance.totalChunks = Math.ceil(instance.totalRecords/instance.chunkSize);
			instance.tracker = [];
			for (var i=0; i< instance.totalChunks; i++) 			
			{
				instance.tracker[i] = false;
			}
			instance.administerReceiveBatch(instance.batchID,response);
		}).fail(function(jqXHR, textStatus) 
		{
			console.log(jqXHR);

			return instance.errorHandler("Failed to initialize batch receive");
		});
}

/**
 * Store retrieved data for later reassembly
 *  
 * @memberOf MembermouseBatchTransfer
 * @param {Object} payload
 */
MembermouseBatchTransfer.prototype.processResponse = function(response)
{
	if (("chunkNumber" in response) && ("payload" in response) && ("batchID" in response) && (response.batchID == this.batchID))
	{
		var chunkNumber = parseInt(response.chunkNumber);
		
		//store buf locally
		this.insertDBData(chunkNumber, response.payload);
		
		//mark chunk retrieval as complete
		this.tracker[chunkNumber] = true;
		
		//increment local counters
		this.chunkCounter++;
		this.currentRecord += this.chunkSize;
		
		//update status
		this.updateStatus(this.chunkCounter, this.totalChunks, this.currentRecord, this.totalRecords);
	}
}


/**
 * Administer the defined batch and retrieve the chunks 
 *  
 * @memberOf MembermouseBatchTransfer
 */
MembermouseBatchTransfer.prototype.administerReceiveBatch = function(batchID, initialResponse)
{
	var instance = this;
	var asyncCalls = [];
	
	instance.openDB().then(function() 
	{
		instance.registerBatch(batchID).then(function() 
		{
			instance.processResponse(initialResponse);
			var concurrent = (instance.maxSimultaneous > instance.totalChunks) ? instance.totalChunks : instance.maxSimultaneous;
			for (var i=0; i<concurrent; i++)
			{
				if(instance.cancelled)
				{  
					instance.callbackFunction(instance.STATUS_CANCELLED, "Cancelled");
					return false;
				}
				
				var commandPacket = instance.findNextChunk();
				if (commandPacket === false)
				{
					break;
				}
				asyncCalls.push(instance.retrieveLoop(commandPacket,i));
			}
			Promise.all(asyncCalls).then(function() 
			{
				var remainingChunk = instance.findNextChunk();
				if (remainingChunk === false)
				{
					return instance.reassembleBatch(instance.batchID);
				}
				else
				{
					//still more chunks left, even after promises are resolved, which means errors not resolved by retries.
					instance.errorHandler("Unable to retrieve one or more pieces of the file");
					return false;
				}
			});
		});
	});
}


/**
 * Reassemble the chunks 
 *  
 * @memberOf MembermouseBatchTransfer
 */
MembermouseBatchTransfer.prototype.reassembleBatch = function(batchID)
{
	var chunks = [];
	var instance = this;
	
	this.db.mm_batch_items.where('batchID').equals(batchID).until(function() { return false;}).each(function(sortedChunk) 
	{
		chunks[sortedChunk.chunkNumber] = sortedChunk.chunkData;
	}).then(function() 
	{     
		instance.callbackFunction(instance.STATUS_OK, "Success");
		instance.exportOutput(instance.defaultExportFilename,new Blob(chunks, {type: instance.defaultExportMimeType}));
	});	
}


MembermouseBatchTransfer.prototype.registerBatch = function(batchID)
{
	var instance = this;
	
	//database must be open for this to work
	if ((this.db == null) || (this.db == ""))
	{
		this.openDB();
	}
	var currentTimestamp = new Date().getTime(); //in milliseconds
	
	//delete old batches (more than 6hrs old)
	var sixHoursAgo = currentTimestamp - (6 * 60 * 60 * 1000);
	return instance.db.mm_batches.where('dateCreated').below(sixHoursAgo).modify(function(oldBatch,ref)
	{
		instance.db.mm_batch_items.where('batchID').equals(oldBatch.batchID).delete().then(function() 
		{
			delete ref.oldBatch;
		});
	}).then(function() 
	{
		//check if the batch id we want to register already exists in the db. If it does, reuse it
		return instance.db.mm_batches.get(batchID);
	}).then(function(existingBatch) 
	{
		if (existingBatch == null) 
		{
			instance.db.mm_batches.put({batchID: batchID, dateCreated: currentTimestamp}).then(function()
			{
				//clear out any old orphaned entries
				return instance.db.mm_batch_items.where('batchID').equals(batchID).delete(); 
			});
		}
		else 
		{	//clear out any old entries
			return instance.db.mm_batch_items.where('batchID').equals(batchID).delete(); 
		}
	});
}


MembermouseBatchTransfer.prototype.openDB = function()
{
	this.db = new Dexie(this.dbName);
    this.db.version(1).stores({
        mm_batches: '&batchID,dateCreated',
        mm_batch_items: '++id,batchID'
    });
    
    this.db.version(2).stores({
        mm_batches: '&batchID,dateCreated',
        mm_batch_items: '++id,batchID,chunkNumber'
    });
    return this.db.open();
}


MembermouseBatchTransfer.prototype.insertDBData = function(chunkNumber,chunkData)
{
	if (!isNaN(chunkNumber) && (typeof(chunkData) != 'undefined'))
	{
		return this.db.mm_batch_items.put({'batchID': this.batchID, 'chunkNumber': chunkNumber, 'chunkData': chunkData});
	}
}


MembermouseBatchTransfer.prototype.findNextChunk = function()
{
	
	var i = 0;
	var nextChunk = 0;
	var found = false;
	var retry = false;
	var instance = this;  
	
	while ((i < instance.tracker.length) && !found) 
	{
		if (instance.tracker[i] === false)
		{
			found = true;
			instance.tracker[i] = true;
			nextChunk = i;
		}
		else
		{
			i++;
		}
	}
	
	if ((found === false) && (instance.errorQueue.length > 0))
	{
		retry = true;
		nextChunk = instance.errorQueue.shift();
	}
	
	if (found || retry)
	{
		var values = {'chunkNumber':nextChunk};
		values.type = found?'regular':'retry';
		var commandPacket = new MembermouseBatchTransferData("retrieveChunk",values);
		commandPacket.chunkSize = this.chunkSize;
		commandPacket.batchID = this.batchID;
		return commandPacket;
	}
	else 
	{
		return false;
	}
}


MembermouseBatchTransfer.prototype.retrieveLoop = function(commandPacket,threadNumber)
{
	if(this.postvars!=null && this.postvars != undefined)
	{
		for(var eachvar in this.postvars)
		{
			commandPacket[eachvar] = this.postvars[eachvar];
		}
	}
	
	var instance = this; 
	if(instance.cancelled)
	{  
		instance.callbackFunction(instance.STATUS_CANCELLED, "Cancelled");
		return false;
	}
	return jQuery.ajax({
		  type: "POST",
		  url: instance.chunkUrl,
		  data: this.formatChunkRequestData(commandPacket),
		  dataType: "json"
		}).then(function(response) 
		{
			//TODO: can this trigger even on success? Like php error but 200 status?
			instance.processResponse(response);
			nextCommandPacket = instance.findNextChunk(); 
			
			if (nextCommandPacket != false)
			{
				return instance.retrieveLoop(nextCommandPacket,threadNumber);
			}
			else
			{
				return true;
			}
		})
		.fail(function(jqXHR, textStatus) 
		{
			if (!('retry' in commandPacket) || (commandPacket.retry != true))
			{
				commandPacket.retry = true;
				instance.errorQueue.push(commandPacket);
			}
		});
}

MembermouseBatchTransfer.prototype.cancel = function() 
{  
	this.cancelled = true;
}	

MembermouseBatchTransfer.prototype.generateBatchID = function() 
{
	var length = 64;
	var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var result = '';
	for (var i = length; i > 0; --i) 
	{
		result += chars[Math.floor(Math.random() * chars.length)];
	}
	return result;
}

//TODO: hook this to shutdown and cancel
MembermouseBatchTransfer.prototype.batchReceiveCleanup = function()
{
	
}

/*************************************************************
 * Batch send (server import) methods
 *************************************************************/


/**
 * Contact the server, send configuration information,  and perform setup necessary to administer the batch (like total number of records, file reader object, etc)
 * The initial value may also contain the first chunk of data. If chunk size > total records, the entire dataset may be sent in the first chunk
 * 
 * Important: The first argument to this call must be a File object generated by an HTML5 file input. For security reasons, the spec requires that the user 
 * select the local file using that control
 * 
 * @memberOf MembermouseBatchTransfer
 * @param {Object} fileObj The File object 
 * @param {Object} values Extra configuration information
 */
MembermouseBatchTransfer.prototype.initializeBatchSend = function(fileObj, values)
{
	this.reader = new FileReader()
	if (typeof(fileObj) == undefined)
	{
		//Not configured correctly, exit
		return this.errorHandler("Batch send initiated without supplying a file object, exiting");
	}
	
	if ((this.initialUrl == "") || (this.chunkUrl == ""))
	{
		//Not configured correctly, exit
		return this.errorHandler("Either the initial url or chunk url were not set, exiting batch send");
	}
	
	var instance = this;
	this.readFileIntoArrayBuffer(fileObj).then(function(srcArrayBuffer) //arraybuffer must be used so the entire file isn't read as once
	{
		instance.srcArrayBuffer = srcArrayBuffer;
		
		instance.batchID = instance.generateBatchID();
		
		instance.totalRecords = instance.getFileSize(instance.srcArrayBuffer);
		instance.totalChunks = instance.chunkBoundaryMap.length;
		
		var commandPacket = new MembermouseBatchTransferData("initBatchSend",values);
		commandPacket.chunkSize = instance.chunkSize;
		commandPacket.batchID = instance.batchID;
		
		return jQuery.ajax({
			  type: "POST",
			  url: this.initialUrl,
			  data: commandPacket,
			  dataType: "json"
			});
		
	}).then(function(response)
	{
		if (((response == null) && (typeof(response) !== "object")) || !('payload' in response) || !('batchID' in response) || (response.batchID.length <1))
		{
			return instance.errorHandler("Invalid response received from server");
		}
		
		instance.tracker = [];
		for (var i=0; i< instance.totalChunks; i++) 			
		{
			instance.tracker[i] = false;
		}
		instance.administerSendBatch(instance.batchID,response);
	});
	/*
	.catch(function(error) 
	{
		return instance.errorHandler("Error loading file, aborting");
	});
	*/
}


/**
 * getFileSize performs two important functions; First it counts how many lines are in the import file. The FileReader returns an array of bytes, 
 * so to get a line count, the function converts the bytes to utf-8 strings, and scans for and counts the number of newlines (\n). Second because the 
 * file is supplied as an ArrayBuffer, it is possible that when splitting the buffer into chunks, the split might occur in the middle of a character. To prevent this, 
 * we will mark known chunk boundaries at the same time we are counting, to be used later for batch administration
 */
MembermouseBatchTransfer.prototype.getFileSize = function(srcArrayBuffer)
{
	//if the src is greater than 100mb of data split it into 100 chunks, otherwise 10 chunks
	var len = srcArrayBuffer.byteLength;
	var counterChunks = (len >= 104857600) ? 100 : 10; //the number of chunks
	var chunkSize = Math.ceil(srcArrayBuffer.byteLength/counterChunks);
	var curPos = 0;
	var newLines = 0;
	var endPos = 0;
	var td = new TextDecoder("utf-8");
	var te = new TextEncoder();
	var currChunk = 0;
	var theEnd = false;
	
	//in this next section, loop through the file, identify byte boundaries for the chunks to be sent, gather statistics necessary to display progress
	while (curPos < len) 
	{
		endPos = ((curPos + chunkSize) >= len) ? len : (curPos + chunkSize);
		var arr = new Uint8Array(srcArrayBuffer.slice(curPos,endPos));
		var arrAsString = td.decode(arr);
		var lastNewline = -1;
	    for (var i = 0; i <arrAsString.length;  i++) 
	    {
	        if( arrAsString[i] === '\n' ) 
	        {
	            newLines++;
	            lastNewline = i;
	        }
	    }
	    
	    if (lastNewline != -1)
	    {
	    	arrAsString = arrAsString.substring(0,lastNewline+1);
	    }
	    arr = te.encode(arrAsString); //turn back into bytes
	    this.chunkBoundaryMap[currChunk] = { start: curPos, end: (curPos + arr.length)};
	    this.updatePreflightStatus(curPos,len);
	    curPos = curPos + arr.length + 1;
	    currChunk++;
	}
    return newLines;
}


/**
 * reads the src file into an arraybuffer
 * 
 * @param srcFile The url location of the file to read
 * @return Promise which contains a reference to the arraybuffer when resolved
 */
MembermouseBatchTransfer.prototype.readFileIntoArrayBuffer = function(srcFile)
{
	  var fr = new FileReader();

	  return new Promise(function(resolve, reject)
	  {
		  fr.onerror = function()
		  {
			  fr.abort();
			  reject(new DOMException("Problem parsing input file."));
		  };

		  fr.onload = function()
		  {
			  resolve(fr.result);
		  };
		  fr.readAsArrayBuffer(srcFile);
	  });
}

//TODO: import cleanup method, hooked to shutdown and cancel

/*************************************************************
 * Override the following receive (server export) methods to adapt to the application
 *************************************************************/

/**
 * The output of formatChunkRequestData is sent straight into the 'data' parameter of a jQuery.ajax call.
 * This method is meant to be overridden for instances where the chunkUrl expects requests in a specific format
 * 
 * @param commandPacket An object of type MembermouseBatchTransferData
 * 
 */
MembermouseBatchTransfer.prototype.formatChunkRequestData = function(commandPacket)
{
	return commandPacket;
}


/**
 * Initiates a browser download of the supplied blob
 * 
 * @param filename The filename to prompt the user to download. This can be changed by the user in the file download dialog
 * @param blob A variable containing the blob object the user will download
 */
MembermouseBatchTransfer.prototype.exportOutput = function (filename, blob) 
{       
	var url = location.pathname+"?"+window.location.search.substr(1);
	var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.responseType = 'arraybuffer';
    xhr.onload = function(e) {
       if (this.status == 200) { 
          var link=document.createElement('a');
          link.href=window.URL.createObjectURL(blob);
          link.download=filename;
          link.click();
       }
    };
xhr.send();
}


/**
 * Is called whenever transfer status is updated
 */
MembermouseBatchTransfer.prototype.updateStatus = function(currentChunk, totalChunks, currentRecord, totalRecords)
{
	//override with a function that displays status as necessary
}


/**
 * Import files must be preflighted, this method is called to update status on the preflight operation
 */
MembermouseBatchTransfer.prototype.updatePreflightStatus = function(currentByte, totalBytes)
{
	
}


/**
 * Override to provide custom error handling. By default, the error is just sent to the console
 */
MembermouseBatchTransfer.prototype.errorHandler = function(errMessage)
{
	console.log(errMessage);
	instance.callbackFunction(instance.STATUS_FAILED, errMessage);
}


//TODO: close db when finished