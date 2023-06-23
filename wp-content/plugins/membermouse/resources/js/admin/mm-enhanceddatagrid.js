/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

/** 
 *  MM_EnhancedDatagridJS renders a datagrid into a designated container, and provides support for translation, pagination, page caching
 *  bulk operations, prefetching, and AJAX search
 * 
 *  Dependencies: Dexis.js
 *  Requirements: A valid grid configuration must be supplied, and the objects being displayed must have a member named "id" that contains a unique id referencing each distinct object
	(the id column is used internally by the grid for dupe detection)
 */
class MM_EnhancedDatagridJS
{
	constructor(container,type,config)
	{
		this.container = container;
		this.config = config;
		this.type = type;
		
		this.gridId = "";
		if ((this.container !== undefined) && ("gridId" in this.container.dataset))
		{
			this.gridId = this.container.dataset.gridId;
		}
		
		//database
		this.db = new Dexie(type);
		
		//external image definitions and translations
		this.translations = false;
		this.imageObj = false;
		
		//state
		this.cacheInitialized = false;
		this.currentPage = 1;
		this.totalPages = 1;
		this.totalResults = 0;
		this.pageSize = (this.config.pageSize !== undefined) ? this.config.pageSize : 20; 
		this.renderTableControls = ((this.config.renderTableControls !== undefined) && (this.config.renderTableControls === false)) ? false : true;
		this.bottomControlsThreshold = (this.config.bottomControlsThreshold !== undefined) ? this.config.bottomControlsThreshold : 10;
		this.fullResultsetInMemory = false;
		this.currentSelection = {};
		this.headerNeedsRedraw = false
		
		this.activeQuery = "";
		this.activeSort = "";
		
		this.defaultSortColumnId = "";
		this.defaultSortType = "";
		
		let defaultSortColumn = this.config.columns.find( (column) => { return (column.defaultSortType) });
		if (defaultSortColumn.id)
		{
			this.defaultSortColumnId = defaultSortColumn.id;
			this.defaultSortType = (defaultSortColumn.defaultSortType.trim().toUpperCase() == "DESC") ? "DESC" : "ASC";
			this.activeSort = `${defaultSortColumn.id} ${this.defaultSortType}`;
		}
		
		this.tableBody = ""; 
		this.typeMap = false;
		
		this.ajaxSearchTimer = "";
		
		//options
		this.pagesizeOptions = [10,20,50,100,500,1000];
		this.bulkOperationsEnabled = true;
		this.supportedBulkOperations = false;
		this.bulkAdminFunction = false;
		this.ajaxSearchEnabled = true;
		this.customSearchEnabled = false;
		this.useCache = true;
		this.cacheTime = 5; //in minutes
		
		this.css = {
			"table":"widefat",
			"tableAlternateRow":"alternate"	
		};
		
	}
	
	render()
	{
		//TODO: only render if there is data
		if (this.bulkOperationsEnabled && this.bulkAdminFunction)
		{
			if (this.supportedBulkOperations === false)
			{
				if (mmjs && mmjs.getDefaultBulkOperations)
				{
					this.supportedBulkOperations = mmjs.getDefaultBulkOperations();
				}
			}
		}
		
		this.readInitialData().then( (initialData) => {
			let tmpContainer = new DocumentFragment();
			this.renderTopControls(tmpContainer);
			this.renderTable(tmpContainer,initialData);
			if (this.totalResults && (this.totalResults >= this.bottomControlsThreshold))
			{
				this.renderBottomControls(tmpContainer);
			}
			this.container.appendChild(tmpContainer);
			this.setupEventListeners();
		}).catch(err => {
			console.log("Error rendering table: " + err);
		});

	}	
	
	
	renderTopControls(parent)
	{
		let container = parent ? parent : this.container;
		let controlContainer = this.ce(false,"div",`${this.type}-${this.gridId}-topcontrols`)
		this.renderControls(controlContainer);
		container.appendChild(controlContainer);
	}
	
	
	renderBottomControls(parent)
	{
		let container = parent ? parent : this.container;
		let controlContainer = this.ce(false,"div",`${this.type}-${this.gridId}-bottomcontrols`)
		this.renderControls(controlContainer);
		container.appendChild(controlContainer);
	}
	
	
	renderControls(parent)
	{
		//TODO: mmt all strings referenced by this.tr()
		let controlContainer = this.ce(false,"div","mm-ehd-controls");
		controlContainer.innerHTML = this.getImage("cog");
		
		let pageControlSpan = this.ce(controlContainer,"span","mm-ehd-controls-pagecontrols");
		pageControlSpan.innerHTML = this.tr("Page");
		
		let prevPageSpan = this.ce(pageControlSpan,"span","mm-ehd-controls-prevpage");
		prevPageSpan.title = this.tr("Previous"); 
		prevPageSpan.innerHTML = this.getImage("prevPage");
		prevPageSpan.addEventListener('click',()=> { console.log("clicked prev!"); this.displayPrevPage();});
		
		let currentPageSpan = this.ce(pageControlSpan,"span","mm-ehd-controls-currentpage");
		currentPageSpan.innerHTML = this.currentPage;
		
		let nextPageSpan = this.ce(pageControlSpan,"span","mm-ehd-controls-nextpage");
		nextPageSpan.title = this.tr("Next");
		nextPageSpan.innerHTML = this.getImage("nextPage");
		nextPageSpan.addEventListener('click',() => this.displayNextPage());
		
		//this next section is ugly, but it avoids reflow
		pageControlSpan.appendChild(document.createTextNode(this.tr("of")));
		let totalPagesSpan = this.ce(pageControlSpan,"span","mm-ehd-controls-totalpages-value");
		totalPagesSpan.appendChild(document.createTextNode(this.totalPages));
		pageControlSpan.appendChild(document.createTextNode(" "));
		pageControlSpan.appendChild(document.createTextNode(this.tr("pages")));
		
		//pagesize section
		let pagesizeSpan = this.ce(controlContainer,"span","mm-ehd-controls-pagesize");
		pagesizeSpan.innerHTML = `${this.tr("Show")} `;
		let pagesizeSelect = this.ce(pagesizeSpan,"select","mm-ehd-controls-pagesize-select")
		//TODO: create "live" click handlers for select elements
		this.pagesizeOptions.forEach((size) => {
			pagesizeSelect.options.add(new Option(size,size,size==this.pageSize,size==this.pageSize));
		});
		pagesizeSpan.innerHTML += `${this.tr("per page")}`;
		
		//total records section
		let totalResultsSpan = this.ce(controlContainer,"span","mm-ehd-controls-totalresults");
		let totalResultsValueSpan = this.ce(totalResultsSpan,"span","mm-ehd-controls-totalresults-value");
		totalResultsValueSpan.innerHTML = this.totalResults;
		totalResultsSpan.innerHTML += ` ${this.tr("found")}`;
		
		//cache status icon
		let cacheStatusSpan = this.ce(controlContainer,"span","mm-ehd-controls-cachestatus");
		cacheStatusSpan.innerHTML = (this.useCache) ? this.getImage("cacheIcon") : this.getImage("cacheIcon-disabled");
		cacheStatusSpan.title = this.tr("Click here to toggle local result caching");
		cacheStatusSpan.addEventListener('click',() => this.toggleCacheUsage());

		if (this.bulkOperationsEnabled && this.supportedBulkOperations)
		{
			let bulkOperationsSpan = this.ce(controlContainer,"span","mm-ehd-controls-bulkoperations");
			let bulkOperationsSelect = this.ce(bulkOperationsSpan,"select","mm-bulk-operation-chooser");
			let bulkOperationsOptionsArray = this.generateBulkOperationsOptions(bulkOperationsSelect);
			let bulkOperationsApplyButton = this.ce(bulkOperationsSpan,"button","mm-bulk-operation-apply-button button action");
			bulkOperationsApplyButton.textContent = this.tr("Apply");
			//TODO: hook events to the apply button
		}
		
		if (this.ajaxSearchEnabled)
		{
			let ajaxSearchSpan = this.ce(controlContainer,"span","mm-ehd-controls-search");
			let ajaxSearchInput = this.ce(ajaxSearchSpan,"input","mm-ajax-search-field");
			ajaxSearchInput.type="search";
			ajaxSearchInput.placeholder = "\u{1F50D} Search";
		}
				
		//attach to parent container
		parent.appendChild(controlContainer);
	}
	
	
	renderTable(parent,initialData)
	{
		//TODO: only render if there is data
		if (this.config && this.config.columns)
		{
			let container = parent ? parent : this.container;
			let tableClass = this.css.table ? `mm-ehd-table ${this.css.table}` : "mm-ehd-table";
			let tableEl = this.ce(container,"table",tableClass);
			//Create table header
			let tableHeader = this.renderTableHeader(tableEl);
			this.tableBody = this.ce(tableEl,"tbody");
			if ((initialData) && (initialData.data) && (initialData.data[this.currentPage]))
			{
				this.renderTableRows(initialData.data[this.currentPage],this.currentPage,true);
			}
			else
			{
				this.displayPage(this.currentPage,true);
			}	
		}
	}
	
	
	/** 
	* Allows users to provide a custom render function to translate the data received from the datasource into a tablecell with custom attributes
	* If the renderFunc returns a value, that value is used as-is. If the returned value is an object, the members of that object as used to configure the cell.
	* Supported attributes on the return object:
	* 	value - the contents for the new cell
	*	className - the class to set on the table cell (can contain multiple classes separated by spaces)
	*	image - an image name to look up and insert
	* Alternatively, renderFunc can construct the cell using the supplied references to the cell and the grid
	*
	*	@param dataCell The table cell
	*	@param value The unmodified column value
	*	@param renderFunc The function to call to render the cell contents
	*	@param dataObj Object representing the entire row of data
	*/
	renderCustomCell(dataCell,value,renderFunc,dataObj)
	{
		//custom render functions can either return a simple value, or an object containing properties related to configuring the cell itself
		let renderedValue = renderFunc(value,dataCell,dataObj, this);
		if ((typeof renderedValue === 'object') && (renderedValue !== null))
		{
			let container = document.createElement("span");
			let valueContainer = container;
			let innerVal = "";
			if (renderedValue.images)
			{
				if (!Array.isArray(renderedValue.images))
				{
					renderedValue.images = [renderedValue.images];
				}
				renderedValue.images.forEach((imageName) => { innerVal += this.getImage(imageName);});
			}
			
			if (renderedValue.value)
			{
				innerVal += ((renderedValue.maxValueLength) ? renderedValue.value.subString(0,renderedValue.maxValueLength) : renderedValue.value);
			}
			
			if (renderedValue.className)
			{
				container.className = renderedValue.className;
			}
			
			if (renderedValue.link)
			{
				valueContainer = document.createElement("a");
				container.appendChild(valueContainer);
				valueContainer.setAttribute('href',renderedValue.link);
			}
			
			if (renderedValue.title)
			{
				valueContainer.setAttribute("title",renderedValue.title)
			}
			
			valueContainer.innerHTML = innerVal;
			dataCell.appendChild(container);
		}
		else if ((renderedValue !== null) && (renderedValue !== undefined))
		{
			dataCell.innerHTML = renderedValue;
		}	
	}
	
	
	redrawTableHeader()
	{
		while (this.tableBody.firstChild) 
		{
			this.tableBody.removeChild(this.tableBody.lastChild);
		}
		
		let oldTableHeader = document.querySelector(`[data-grid-id='${this.gridId}'] .mm-ehd-table-thead`);
		let tableEl = document.querySelector(`[data-grid-id='${this.gridId}'] .mm-ehd-table`);
		if (tableEl && oldTableHeader)
		{
			let newTableHeader = this.renderTableHeader(document.createDocumentFragment());
			tableEl.replaceChild(newTableHeader,oldTableHeader);
		}
		else
		{
			console.log("Redraw table header failed, could not find either table or existing header");
		}
		this.setupColumnHeaderListeners();
		this.headerNeedsRedraw = false;
	}
	
	
	renderTableHeader(parent)
	{
		if (parent && this.config && this.config.columns)
		{
			let thead = this.ce(parent,"thead","mm-ehd-table-thead");
			let headerRow = thead.insertRow();
			
			if (this.bulkOperationsEnabled)
			{
				let bulkSelectorCell = grid.ce(headerRow,"th");
				bulkSelectorCell.className = "mm-ehd-table-header";
				let bulkSelector = this.ce(bulkSelectorCell,"input");
				bulkSelector.type = "checkbox";
				bulkSelector.className = `mm-ehd-bulk-selector-all`;
			}
			
			let visibleColumns = this.config.columns.filter((col) => !col.hidden || (!col.hidden == true));
			visibleColumns.forEach( (col) => {
				let cell = grid.ce(headerRow,"th");
				let className = "mm-ehd-table-header";
				if (col.sortable)
				{
					className += " mm-ehd-table-header-sortable";
					cell.dataset.sortableId = col.id;
					
					let sortAnchor = this.ce(cell,"a");
					let sortDecorator = "";
					sortAnchor.setAttribute("role","button");
					if (col.defaultSortType)
					{
						if (col.defaultSortType.toUpperCase() == "DESC")
						{
							className += " mm-ehd-table-header-sortable-sortdesc";
							sortDecorator = " " + this.getImage("sort-desc");
						}
						else
						{
							className += " mm-ehd-table-header-sortable-sortasc";
							sortDecorator = " " + this.getImage("sort-asc");
						}
					}
					sortAnchor.innerHTML = this.tr(col.name) + `<span class="mm-ehd-table-header-sortable-sorticon">${sortDecorator}</span>`;
				}
				else
				{
					cell.innerHTML = this.tr(col.name);
				}
				cell.className = className;
				
			});
			return thead;
		}
	}
	
	
	renderNoDataCell()
	{
		while (this.tableBody.firstChild) 
		{
			this.tableBody.removeChild(this.tableBody.lastChild);
		}
		
		let numCols = this.config.columns.length + (this.bulkOperationsEnabled ? 1 :0);
		let tr = this.tableBody.insertRow();
		let noDataCell = document.createElement("td");
		let tn = document.createTextNode(this.tr("No items found"));
		noDataCell.setAttribute("colspan",numCols);
		noDataCell.className = "mm-ehd-table-cell-nodata";
		noDataCell.appendChild(tn);
		tr.appendChild(noDataCell);
	}
	
	
	getPrefetchSize()
	{
		//TODO: add bulk op column to table render
		if (this.pageSize <= 100)
		{
			return 5;
		}
		else if (this.pageSize <= 500)
		{
			return 3;
		}
		else
		{
			return 2;
		}
	}
	
	/**
	 * Generates the bulk options available for this grid. If no options have been specifically configured, uses a default configuration
     * with the most common options
	 */
	generateBulkOperationsOptions(parent)
	{
		let operations = this.supportedBulkOperations
		let options = [];
		
		if (this.bulkOperationsEnabled)
		{
			if (!operations || (typeof(operations) != "object") && mmjs)
			{
				if (mmjs.getDefaultBulkOperations)
				{
					operations = this.getDefaultBulkOperations();
				}
			}
			
			if (Array.isArray(operations) && (operations.length > 0))
			{
				let titleOption = this.ce(parent,"option","mm-bulk-operation-chooser-option");
				titleOption.value = "bulk-operations-title";
				titleOption.text = this.tr("Bulk Actions");
				options = operations.map( (op) => {
					let option = this.ce(parent,"option","mm-bulk-operation-chooser-option");
					option.value = op.id;
					option.text = op.display_name;
				});
			}
		}
		return options;
	}
	
	
	getImage(imageName)
	{
		if (this.imageObj && this.imageObj[imageName])
		{
			return this.imageObj[imageName];
		}
		return "";
	}
	
	
	setTranslationObject(trObj)
	{
		if (typeof(trObj) == "object")
		{
			this.translations = trObj;
		}
	}
	
	
	setImageReferences(imageObj)
	{
		if (typeof(imageObj) == "object")
		{
			this.imageObj = imageObj;
		}
	}
	
	
	/** 
	* Translates a string if a translation is present, otherwise returns the value that was passed in
	*/
	tr(value)
	{
		return this.translations[value] ? this.translations[value] : value;		
	}
	
	ce(parent,elementType,classNames,id)
	{
		let el = document.createElement(elementType);
		if (classNames)
		{
			el.className = classNames;
		}
		
		if (id)
		{
			el.id = id;
		}
		
		if (parent)
		{
			parent.appendChild(el);
		}
		return el;
	}
	
	
	cacheInit()
	{
		if (!this.cacheInitialized && this.useCache && this.config.version && !isNaN(this.config.version))
		{
			console.log("initializing cache");
			let indexFields = this.config.columns.filter( field => (field.sortable || field.searchType))
												 .map( field => field.id)
												 .join(",");
											
			this.db.version(this.config.version).stores({
				datacache: `[queryIndexId+id],[queryIndexId+EHDPageNumber],queryIndexId,EHDPageNumber,${indexFields},timestamp`,
				queryIndex: "++id,gridId,query,sortCol,pageSize,timestamp"
			});
			this.cacheInitialized = true;
		}
		else
		{
			console.log("cache already initialized... skipping");
		}
	}
	
	
	/**
	 * Reads an initial block of data from the hosting page and stores it in the cache (if cache is enabled). 
	 * This data is expected to be in a block with an id of the format '<type>-<grid id>-data'. The first page of data
 	 * (if it exists) is then returned. If the data cannot be read, or if there is no first page of data, null is returned
	 *
	 *	@param {string} dataContainerId The id of the container holding the initial data
	 *  @return {array|null} An array of data objects representing the first page of data, or null if not found or an error was encountered
	 */
	async readInitialData(dataContainerId)
	{
		this.cacheInit();
		
		//if there is cached data for this grid, delete it
		//await this.db.datacache.where("gridId").equals(this.gridId).delete();
		
		//detect embedded data
		let initialDataId = dataContainerId ? dataContainerId : `${this.type}-${this.gridId}-data`;
		let initialDataContainer = document.getElementById(initialDataId);
		
		if (initialDataContainer != null)
		{
			let initialData = JSON.parse(initialDataContainer.innerHTML);
			initialDataContainer.remove();
			if (initialData.total && initialData.data)
			{
				this.totalResults = initialData.total;
				this.totalPages = Math.ceil(this.totalResults / this.pageSize);
				if (initialData.query && (this.customSearchEnabled === true))
				{
					this.activeQuery = JSON.stringify(initialData.query);
				}
				
				if (this.useCache)
				{
					await this.cacheStorePageData(initialData);	//TODO: do we need to wait for this?
					return initialData;
				}
			}
		}
		return null;
	}
	
	
	/**
	 * Examines the supplied query and creates filters. Default functionality is to use the supplied query to search the fields flagged in the config
	 * If no query is supplied, uses the active query
	 * 
	 */
	createFiltersFromQuery(query)
	{
		let currentQuery = query ? query : this.activeQuery;
		let queryStruct = { conditions: [[]] };
		
		if (this.customSearchEnabled === true)
		{
			let customQueryStruct = JSON.parse(this.activeQuery||'{}'); //empty string (query with no conditions) causes a SyntaxError, hence the OR
			if (customQueryStruct.conditions)
			{
				return customQueryStruct;
			}
			else
			{
				//custom search is enabled, but the active query isnt a custom search. Do default search
				return { conditions: [[]] };
			}
		}
		else
		{
			//implement the simplest case for now
			if (currentQuery.trim() !== "")
			{
				this.config.columns.forEach((col) => {
					if (col.defaultSearchField && (col.defaultSearchField == true))
					{
						let condition = { name:col.id, value:currentQuery };
						let searchType = col.searchType.toLowerCase();
						if (searchType == "numeric")
						{
							condition.rel = "eq";
						}
						else if (searchType == "date")
						{
							condition.rel = "eq";
						}
						else
						{
							//text type is default
							condition.rel = "like";
						}
						queryStruct.conditions[0].push(condition); 
					}
				});
			}
		}
		return queryStruct;
	}
	
	
	async fetchData(pageNumber,storeinCache, query, sortCol, pageSize, prefetchSize)
	{
		let currentQuery = query ? query : this.activeQuery;
		let currentSortCol = sortCol ? sortCol : this.activeSort;
		let currentPageSize = pageSize ? pageSize : this.pageSize;
		let currentPrefetchSize = prefetchSize ? prefetchSize : this.getPrefetchSize();
		
		if (this.config.datasource instanceof Function)
		{
			//TODO: do this right
			let queryCriteria = { "pageNum"      : pageNumber, 
								  "pageSize"     : currentPageSize, 
								  "prefetchSize" : currentPrefetchSize,
						          "sortCol"      : currentSortCol
								};
			
			queryCriteria.query = this.createFiltersFromQuery(currentQuery);
			
			try
			{
				let pageData = await this.config.datasource(queryCriteria);
				if (pageData.total)
				{
					//TODO: dump cache if total results or max id changes, in all places where total is updated. Max id is still left to do
					if ((this.totalResults) && (this.totalResults != pageData.total))
					{
						this.dumpCache();
					}
					
					this.updateTotalResults(pageData.total);
				}
				
				if (this.useCache && pageData)
				{
					//MARKER : muultiple pages may be returned, cache all of them and then return the requested page 
					await this.cacheStorePageData(pageData); 
				}
				
				return (pageData.data && pageData.data[pageNumber]) ? pageData.data[pageNumber] : null;
			}
			catch(err)
			{
				console.log("Error fetching data:" + err);
				throw(err);
			}
		}
		else
		{
			//TODO: error handling for problem with datasource function
			if (!this.gridId)
			{
				console.log("Grid configuration incorrect: no grid id!");
				return null;
			}
			let err = `Grid Id ${this.gridId}: Datasource is not callable!`
			console.log(err);
			throw(err);
		}
	}
	
	
	async fetchPageData(pageNumber)
	{
		//TODO: what to do if storage is full
		//TODO: disable input while fetch is happening
		try
		{
			let pageData = null;
			if (this.useCache)
			{
				pageData = await this.cacheFetchPageData(pageNumber);
			}
			
			if (pageData == null) //cache disabled, or cache miss, fetch from source
			{
				this.setLoadingState();
				pageData = await this.fetchData(pageNumber);
				this.setLoadingState(true);
				//TODO: handle if the requested page doesn't exist
			}
			
			return pageData ? pageData : null;
		}
		catch(err)
		{
			//TODO: error fetching page data - insert error handling, display to user
			this.setLoadingState(true);
			console.log("Error fetching page data: " + err);
			return null;
		}
	}
	
	
	async cacheStorePageData(pageData,query,sortCol,pageSize)
	{
		if (pageData.data)
		{
			let currentQuery = query ? query : this.activeQuery;
			let currentSortcol = sortCol ? sortCol : this.activeSort;
			let currentPagesize = pageSize ? pageSize : this.pageSize;
			
			let queryIndex = await this.cacheGetQueryIndex(currentQuery,currentSortcol,currentPagesize,true);
			if (queryIndex != null)
			{
				if (!this.typeMap)
				{
					this.typeMap = {};
					this.config.columns.filter(colObj => colObj.searchType).forEach( colObj => { this.typeMap[colObj.id] = colObj.searchType });	
				}
				
				for (const pageNumber in pageData.data)
				{
					if (!isNaN(pageNumber) && Array.isArray(pageData.data[pageNumber])) //make sure the page number is numeric
					{
						pageData.data[pageNumber].forEach( row => {
							//wordpress returns all data as strings, do type conversion if necessary
							if (!this.isEmptyObject(this.typeMap))
							{
								for (const dataVal in row) 
								{
									if (this.typeMap[dataVal])
									{
										if (this.typeMap[dataVal].toUpperCase() == "NUMERIC")
										{
											row[dataVal] = Number(row[dataVal]);
										}
										else if (this.typeMap[dataVal].toUpperCase() == "DATE")
										{
											row[dataVal] = this.dateFromMysqlDate(row[dataVal]);
										}
									}
								}
							}
							row.queryIndexId = queryIndex.id;
							row.EHDPageNumber = Number(pageNumber);
						});
						
						//TODO: is the delete necessary if the primary key is working?
						await this.db.datacache.where("[queryIndexId+EHDPageNumber]").equals([queryIndex.id,pageNumber]).delete(); //remove old page
						await this.db.datacache.bulkPut(pageData.data[pageNumber]);
					}
				}
			}
		}
	}
	
	
	async cacheFetchPageData(pageNumber)
	{
		//TODO: add error handling for when server communication fails
		pageNumber = Number(pageNumber);
		await this.cacheCleanup();
		
		let queryIndex = await this.cacheGetQueryIndex(this.activeQuery,this.activeSort,this.pageSize,true);
		if (queryIndex)
		{
			let pageData = "";
			let doDefaultSort = false;
			if (this.activeSort)
			{
				let sortComponents = this.activeSort.trim().toUpperCase().split(" ");
				if (Array.isArray(sortComponents) && (sortComponents.length == 2))
				{
					if (sortComponents[1] == "DESC")
					{
						pageData = await this.db.datacache.where("[queryIndexId+EHDPageNumber]").equals([queryIndex.id,pageNumber]).reverse().sortBy(sortComponents[0]);
					}
					else
					{
						pageData = await this.db.datacache.where("[queryIndexId+EHDPageNumber]").equals([queryIndex.id,pageNumber]).sortBy(sortComponents[0]);
					}
				}
				else
				{
					doDefaultSort = true;
				}
			}
			else
			{
				doDefaultSort = true;
			}
			
			if (doDefaultSort)
			{
				if (this.defaultSortColumnId)
				{
					pageData = await this.db.datacache.where("[queryIndexId+EHDPageNumber]").equals([queryIndex.id,pageNumber]).sortBy(this.defaultSortColumnId);
					//TODO: assuming the sort type is ASC for now, add in if necessary later
				}
				else
				{
					pageData = await this.db.datacache.where("[queryIndexId+EHDPageNumber]").equals([queryIndex.id,pageNumber]).toArray();
				}
			}
			
			if (Array.isArray(pageData) && pageData.length)
			{
				return pageData;
			}
		}
		return null;
	}
	
	
	async cacheGetQueryIndex(query,sortCol,pageSize, createIfNotExists)
	{
		if ((query != undefined) && (sortCol != undefined))
		{ 
			let queryIndex = await this.db.queryIndex.where("gridId").equals(this.gridId)
													 .and(value=> value.query == query)
													 .and(value => value.sortCol == sortCol)
													 .and(value => value.pageSize == pageSize)
													 .first();
			if (typeof(queryIndex) == "object")
			{
				return queryIndex;
			}
			else if (createIfNotExists)
			{
				return await this.cacheCreateQueryIndex(query,sortCol,pageSize);
			}
		}
		return null;
	}
	
	
	async cacheCreateQueryIndex(query,sortCol,pageSize)
	{
		let queryIndex = await this.db.queryIndex.add({ "gridId":this.gridId, "query":query, "sortCol":sortCol, "pageSize":pageSize, "timestamp":Date.now()});
		return await this.db.queryIndex.get(queryIndex);
	}
	
	
	async cacheCleanup()
	{
		let cacheExpiration = Date.now() - (this.cacheTime * 60 * 1000); //cache expiration time in milliseconds
		let data = await Promise.all([this.db.queryIndex.where("timestamp").belowOrEqual(cacheExpiration).primaryKeys(),
									  this.db.queryIndex.toCollection().primaryKeys()]); //these run in parallel
		let expiredQueryIds = data[0];
		let allQueryIds = data[1];
		
		if (expiredQueryIds && Array.isArray(expiredQueryIds) && (expiredQueryIds.length > 0))
		{
			await Promise.all([this.db.queryIndex.bulkDelete(expiredQueryIds),
							  this.db.datacache.where("queryIndexId").anyOf(expiredQueryIds).delete()]); //also parallel
		}
		
		if (allQueryIds && Array.isArray(allQueryIds) && (allQueryIds.length > 0))
		{
			await this.db.datacache.where("queryIndexId").noneOf(allQueryIds).delete();
		}
	}
	
	/**
	 * Test if an object is a function. All credit to underscore.js for this implementation
	 */
	isFunction(obj) 
	{
  		return !!(obj && obj.constructor && obj.call && obj.apply);
	}
	
	
	isEmptyObject(obj)
	{
		return obj && Object.keys(obj).length === 0 && obj.constructor === Object;
	}
	
	displayPrevPage()
	{
		if (this.currentPage > 1)
		{
			this.displayPage(this.currentPage-1);
		}
	}
	
	displayNextPage()
	{
		if (this.currentPage < this.totalPages)
		{
			this.displayPage(this.currentPage+1);
		}
	}
	
	
	renderTableRows(rowData,pageNumber,doNotClear)
	{	
		this.setPageNumber(pageNumber);
		if (Array.isArray(rowData) && rowData.length)
		{
			if (!doNotClear)
			{
				while (this.tableBody.firstChild) 
				{
					this.tableBody.removeChild(this.tableBody.lastChild);
				}
			}
			
			let visibleColumns = this.config.columns.filter((col) => !col.hidden || (!col.hidden == true));
			
			//Create table body	
			for (var i=0, rl = rowData.length; i < rl; i++)
			{
				let isAlternate = ((i%2)==0);
				let newRow = this.tableBody.insertRow();
				let dataObj = rowData[i];
				let rowClass = "mm-ehd-table-row";
				let bse = 0;
				if (isAlternate)
				{
					rowClass = (this.css.tableAlternateRow) ? `${rowClass} mm-ehd-table-alternate-row ${this.css.tableAlternateRow}` : `${rowClass} mm-ehd-table-alternate-row`;
				}
				newRow.className = rowClass;
				
				if (this.bulkOperationsEnabled)
				{
					let bulkSelectorCell = newRow.insertCell();
					let bulkSelector = this.ce(bulkSelectorCell,"input","mm-ehd-bulk-selector-item");
					bulkSelector.type = "checkbox";
					bulkSelector.name = `mm-ehd-bulk-selector-item[]`;
					bulkSelector.value = dataObj.id;
					bse=1;
					
					//add listener
					bulkSelector.addEventListener("click",(e) => {
						this.manageSelectedItems(e.currentTarget.value,!e.currentTarget.checked);
					});
				}
				
				var j=0;
				for (var j=0,cl = visibleColumns.length; j < cl; j++)
				{
					let dataCell = newRow.insertCell(j+bse);
					let columnDef = visibleColumns[j];
					let rawDataValue = dataObj[columnDef.id];
					if (columnDef.render && this.isFunction(columnDef.render))
					{
						let renderFunc = columnDef.render;
						this.renderCustomCell(dataCell,rawDataValue,renderFunc,dataObj);
					}
					else if (this.isJSDate(rawDataValue))
					{
						//value is supposed to be a date, check if its valid
						if (isNaN(rawDataValue.getTime()))
						{
							//date was not valid, display as string
							let dtn = document.createTextNode(rawDataValue);
							dataCell.appendChild(dtn);
						}
						else
						{
							//date was valid, format it 
							let dtn = document.createTextNode(this.formatJSDate(rawDataValue));
							dataCell.appendChild(dtn);
						}
					}
					else if (rawDataValue)
					{
						dataCell.innerHTML = rawDataValue;
					}
					else
					{
						dataCell.innerHTML = "&nbsp;";
					}
				}
			}
		}
		else
		{
			//there was no data, display msg
			this.renderNoDataCell(false);
		}
	}
	
	
	displayPage(pageNumber,doNotClear)
	{
		if (this.tableBody)
		{
			if (this.headerNeedsRedraw)
			{
				doNotClear = false;
				this.redrawTableHeader();
			}	
					
			this.fetchPageData(pageNumber).then( (rowData) => {
				this.renderTableRows(rowData,pageNumber,doNotClear)	
			}); //TODO: error handling
		}
	}
	
	
	ajaxSearch(searchText)
	{
		this.activeQuery = searchText.trim();
		this.displayPage(1);
	}
	
	
	customSearch(customQueryStruct)
	{
		this.activeQuery = JSON.stringify(customQueryStruct);
		this.displayPage(1);
	}
	
	
	changeSort(selectedCol)
	{
		if (selectedCol)
		{
			document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-ehd-table-header-sortable`).forEach( (sortableHeader) => {
				if (sortableHeader !== selectedCol)
				{
					sortableHeader.classList.remove("mm-ehd-table-header-sortable-sortasc");
					sortableHeader.classList.remove("mm-ehd-table-header-sortable-sortdesc");
				}
				sortableHeader.querySelector(".mm-ehd-table-header-sortable-sorticon").innerHTML = "";
			});
			
			let sortField = selectedCol.dataset.sortableId;
			let sortDirection = selectedCol.classList.contains("mm-ehd-table-header-sortable-sortasc") ? "DESC" : "ASC";
			selectedCol.classList.remove("mm-ehd-table-header-sortable-sortasc");
			selectedCol.classList.remove("mm-ehd-table-header-sortable-sortdesc");
			
			if (sortDirection == "DESC")
			{
				selectedCol.classList.add("mm-ehd-table-header-sortable-sortdesc");
				selectedCol.querySelector(".mm-ehd-table-header-sortable-sorticon").innerHTML = this.getImage("sort-desc");
			}
			else
			{
				selectedCol.classList.add("mm-ehd-table-header-sortable-sortasc");
				selectedCol.querySelector(".mm-ehd-table-header-sortable-sorticon").innerHTML = this.getImage("sort-asc");
			}
			this.activeSort = `${sortField} ${sortDirection}`;
			this.displayPage(1);
		}
	}
	
	
	repaginate(newPagesize)
	{
		//todo: implement
	}
	
	
	changePagesize(pagesizeChooser)
	{
		if (pagesizeChooser)
		{
			//sync other selectors
			document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-ehd-controls-pagesize-select`).forEach( (selector) => {
				if (selector.value != pagesizeChooser.value)
				{
					selector.value = pagesizeChooser.value;
				}
			});
			
			//repaginate, maybe avoid server fetch. Note: currently does nothing
			this.repaginate(pagesizeChooser.value);
			this.pageSize = pagesizeChooser.value;
			this.updateTotalResults(this.totalResults); //reupdates the number of pages
			this.displayPage(1);
		}
	}
	
	
	setPageNumber(pageNumber)
	{
		this.currentPage = pageNumber;
		document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-ehd-controls-currentpage`).forEach( (pageHolder) => {
			pageHolder.innerHTML = pageNumber;
		});
	}
	
	
	setLoadingState(clear)
	{
		if (clear)
		{
			//clear loading state
			this.tableBody.classList.remove("has-overlay");
			this.tableBody.style.cursor = 'default';
		}
		else
		{
			//begin loading state
			this.tableBody.classList.add("has-overlay");
			this.tableBody.style.cursor = 'progress';
		}
	}
	
	
	updateTotalResults(newTotal,updateQueryIndex)
	{
		if (newTotal != this.totalResults)
		{
			this.totalResults = newTotal;
			document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-ehd-controls-totalresults-value`).forEach( (totalHolder) => {
				totalHolder.innerHTML = this.totalResults;
			});
		}
		
		let newTotalPages = Math.ceil(this.totalResults / this.pageSize);
		if (newTotalPages != this.totalPages)
		{
			this.totalPages = newTotalPages;
			document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-ehd-controls-totalpages-value`).forEach( (totalPagesHolder) => {
				totalPagesHolder.innerHTML = this.totalPages;
			});
		}
		//TODO: figure out mechanism to save totals in the queryindex, why fetches are being done for the same ajax searches prior to timeout
	}
	
	
	async dumpCache()
	{
		if (this.gridId)
		{
			let gridQueryIds = await this.db.queryIndex.where("gridId").equals(this.gridId).primaryKeys();
			if (gridQueryIds)
			{
				await Promise.all([this.db.queryIndex.where("gridId").equals(this.gridId).delete(),
								   this.db.datacache.where("queryIndexId").anyOf(gridQueryIds).delete()]);
			}
		}
	}
	
	
	/** 
	* Dumps the cache and re-renders the current page
	*/
	dumpCacheAndRefresh(customQueryStruct)
	{
		if (this.gridId)
		{
			this.dumpCache().then( () => { 
				if (customQueryStruct)
				{
					this.customSearch(customQueryStruct);
				}
				else
				{
					this.displayPage(this.currentPage);
				}
			}); //TODO: add fail handling
		}
	}
	
	
	dateFromMysqlDate(mysqlDatestring)
	{ 
	   var t, result = null;
	   if( typeof mysqlDatestring === 'string' )
	   {
	      t = mysqlDatestring.split(/[- :]/);
	
	      //when t[3], t[4] and t[5] are missing they defaults to zero
	      result = new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0); 
		  return this.isJSDate(result) ? result : null;       
	   }
	   return result;   
	}
	
	
	isJSDate(obj)
	{
		return ((Object.prototype.toString.call(obj) === "[object Date]") && !isNaN(obj));
	}
	
	
	/**
	 * Converts a javascript Date object into a string formatted like "Jan 1, 2021 5:14pm"
	 */
	formatJSDate(jsDateObj)
	{
		const months = ["Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
		let tf = (n) => { return (n<=9) ? "0" + n : n; } ; //append 0 to values 9 or less
		let hour = jsDateObj.getHours();
		hour = ((hour / 12) > 1) ? hour-12 : hour;
		hour = tf((hour == 0) ? 12 : hour);
		let ampm = (jsDateObj.getHours() >= 12) ? "pm" : "am";
		let formattedDate = `${months[jsDateObj.getMonth()]} ${tf(jsDateObj.getDate())}, ${jsDateObj.getFullYear()} ${hour}:${tf(jsDateObj.getMinutes())} ${ampm}`;
		return formattedDate;
	}
	
	
	useCustomSearch()
	{
		this.ajaxSearchEnabled = false;
		this.customSearchEnabled = true;
	}
	
	
	alterColumn(columnId, newName, hidden)
	{
		if (newName || hidden)
		{
			let redraw = false;
			this.config.columns.filter((col) => col.id == columnId).forEach((tCol) => {	
				if (tCol.name != newName)
				{
					tCol.name = newName;
					redraw = true;
				}
				if ((hidden === true) || (hidden === false))
				{
					let colHidden = ('hidden' in tCol) && (tCol.hidden == false)
					if (colHidden != hidden)
					{
						redraw = true;
					}
					tCol.hidden = hidden;
				}
			});
			if (redraw)
			{
				this.headerNeedsRedraw = true;
			}
		}
	}
	
	
	setColumnHidden(columnId,newState)
	{
		let redraw = false;
		this.config.columns.filter((col) => col.id == columnId).forEach((tCol) => {
			let colHidden = ('hidden' in tCol) && (tCol.hidden == false); //if the property doesnt exist, the column is not hidden
			if (newState != colHidden)
			{
				redraw = true;
			}
			tCol.hidden = (newState == true);
		});
		if (redraw)
		{
			this.headerNeedsRedraw = true;
		}
	}
	
	
	bulkSelectAllItems()
	{
		document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-ehd-bulk-selector-item`).forEach( (bulkSelectCheckbox) => {
			if (!bulkSelectCheckbox.checked)
			{
				bulkSelectCheckbox.click();
			}
		});
	}
	
	
	bulkUnselectAllItems()
	{
		document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-ehd-bulk-selector-item`).forEach( (bulkSelectCheckbox) => {
			if (bulkSelectCheckbox.checked)
			{
				bulkSelectCheckbox.click();
			}
		});
	}
	
	
	clearSelectedItems()
	{
		this.currentSelection = {};
		document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-ehd-bulk-selector-all`).forEach( (bulkopAllSelector) => {
			bulkopAllSelector.checked = false;
		});
	}
	
	
	manageSelectedItems(itemId,remove)
	{
		if (remove)
		{
			delete this.currentSelection.itemId;
		}
		else
		{
			this.currentSelection[itemId] = true;
		}
	}
	
	
	bulkopConfirm(bulkopMetadata)
	{
		if (!bulkopMetadata.display_name)
		{
			return false;
		}
		
		let selectedIds = Object.keys(this.currentSelection);
		if (Array.isArray(selectedIds) && (selectedIds.length > 0))
		{
			let opName = bulkopMetadata.display_name.toLowerCase();
			let wm1 = "Warning: you are about to perform the operation";
			let wm2 = "on"
			let wm3 = this.tr("items"); //TODO: get entity name from the grid config
			let wm4 = this.tr("Click OK to begin, or Cancel to abort");
			let warningMsg = `${wm1} ${bulkopMetadata.display_name} ${wm2} ${selectedIds.length} ${wm3}\n\n${wm4}`;
			if (confirm(warningMsg))
			{
				this.bulkAdminFunction(bulkopMetadata,selectedIds);
				this.clearSelectedItems();
			}
			else
			{
				return false;
			}
		}
		else
		{
			//no items selected
			alert(this.tr("No items have been selected"));
		}
	}


	toggleCacheUsage()
	{
		this.useCache = !this.useCache;
		document.querySelectorAll('span.mm-ehd-controls-cachestatus').forEach( (cacheStatusSpan) => {
			cacheStatusSpan.innerHTML = (this.useCache) ? this.getImage("cacheIcon") : this.getImage("cacheIcon-disabled");  
		});
		this.dumpCacheAndRefresh();
	}
	
	
	setupColumnHeaderListeners()
	{
		let thisGrid = this;
		
		//attach column sort listeners
		document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-ehd-table-header-sortable`).forEach( (sortCol) => {
			sortCol.addEventListener('click',(e) => {  thisGrid.changeSort(e.currentTarget); });	
		});
		
		//listen to mass selection checkbox, check all visible items if clicked
		if (this.bulkOperationsEnabled)
		{
			document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-ehd-bulk-selector-all`).forEach( (bulkopAllSelector) => {
					bulkopAllSelector.addEventListener('change',(e) => {  
						if (e.currentTarget.checked) 
						{
							thisGrid.bulkSelectAllItems(); 
						}
						else
						{
							thisGrid.bulkUnselectAllItems(); 
						}
					});	
				});
		}
	}
	
	
	setupEventListeners()
	{
		let thisGrid = this;
		
		//attach prev page handler
		document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-ehd-controls-prevpage`).forEach( (backButton) => {
			backButton.addEventListener('click',() => { thisGrid.displayPrevPage(); });	
		});
		
		//attach next page handler 
		document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-ehd-controls-nextpage`).forEach( (fwdButton) => {
			fwdButton.addEventListener('click',() => { thisGrid.displayNextPage(); });	
		});
		
		//attach ajax search listener
		if (this.ajaxSearchEnabled)
		{
			let searchBoxes = document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-ajax-search-field`);
			searchBoxes.forEach( (searchBox) => {
				searchBox.addEventListener('keyup',(e) => { 
					if (searchBoxes.length > 1)
					{
						searchBoxes.forEach( (searchBox) => {
							if (searchBox !== e.target)
							{
								searchBox.value = e.target.value;
							}
						})
					}
					clearTimeout(thisGrid.ajaxSearchTimer);
				    var ms = 500; // milliseconds to wait for typing to stop
					thisGrid.ajaxSearchTimer = setTimeout(function() 
					{
						thisGrid.ajaxSearch(e.target.value); 
				    }, ms);
					
				});
			});
		}
		
		//attach column sort listener
		this.setupColumnHeaderListeners();
		
		//attach page size listener
		document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-ehd-controls-pagesize-select`).forEach( (sizeChooser) => {
			sizeChooser.addEventListener('change',(e) => {  thisGrid.changePagesize(e.currentTarget); });	
		});
		
		//attach bulk operations listeners, if applicable
		if (this.bulkOperationsEnabled)
		{
			//keep dropdown menus in sync
			document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-bulk-operation-chooser`).forEach( (bulkopDropdown) => {
				bulkopDropdown.addEventListener('change',(e) => {  
					document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-bulk-operation-chooser`).forEach( (currentDropdown) => {
						if (e.currentTarget !== currentDropdown)
						{
							currentDropdown.value = e.currentTarget.value;
						}
					});
				});	
			});
			
			//listen to apply button
			document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-bulk-operation-apply-button`).forEach( (bulkopApplyButton) => {
				bulkopApplyButton.addEventListener('click',(e) => {  
					let bulkopMenu = document.querySelectorAll(`[data-grid-id='${this.gridId}'] .mm-bulk-operation-chooser`)[0];
					if (bulkopMenu.value == "bulk-operations-title")
					{
						return;
					}
					let matchingOps = thisGrid.supportedBulkOperations.filter( op => op.id == bulkopMenu.value);
					if (Array.isArray(matchingOps) && (matchingOps.length > 0)) //should only be one
					{
						console.log("applying operation " + bulkopMenu.value);
						thisGrid.bulkopConfirm.call(thisGrid,matchingOps[0]);
					}
					else
					{
						console.log("Incorrect format for bulk operations array... aborting");
					}
				});	
			});
			
		}
	}
}