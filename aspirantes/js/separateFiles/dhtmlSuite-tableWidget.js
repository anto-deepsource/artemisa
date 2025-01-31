if (!window.DHTMLSuite) var DHTMLSuite = new Object()
DHTMLSuite.tableWidgetPageHandler = function () {
  let tableRef
  let targetRef
  let txtPrevious
  let txtNext
  let txtFirst
  let txtLast
  let txtResultPrefix
  let txtResultTo
  let txtResultOf
  let totalNumberOfRows
  let rowsPerPage
  let layoutCSS
  let activePageNumber
  let mainDivEl
  let resultDivElement
  let pageListDivEl
  let objectIndex
  let linkPagePrefix
  let linkPageSuffix
  let maximumNumberOfPageLinks
  let callbackOnAfterNavigate
  this.txtPrevious = 'Previous'
  this.txtNext = 'Next'
  this.txtResultPrefix = 'Result: '
  this.txtResultTo = 'to'
  this.txtResultOf = 'of'
  this.txtFirst = 'First'
  this.txtLast = 'Last'
  this.tableRef = false
  this.targetRef = false
  this.totalNumberOfRows = false
  this.activePageNumber = 0
  this.layoutCSS = 'table-widget-page-handler.css'
  this.linkPagePrefix = ''
  this.linkPageSuffix = ''
  this.maximumNumberOfPageLinks = false
  this.callbackOnAfterNavigate = false
  this.objectIndex = DHTMLSuite.variableStorage.arrayDSObjects.length
  DHTMLSuite.variableStorage.arrayDSObjects[this.objectIndex] = this
}
DHTMLSuite.tableWidgetPageHandler.prototype = {
  setTableRef: function (tableRef) {
    this.tableRef = tableRef
    this.tableRef.setPageHandler(this)
  },
  setTargetId: function (idOfHTMLElement) {
    if (!document.getElementById(idOfHTMLElement)) {
      alert(
        'ERROR IN tableWidgetPageHandler.setTargetId:\nElement with id ' +
          idOfHTMLElement +
          ' does not exists'
      )
      return
    }
    this.targetRef = document.getElementById(idOfHTMLElement)
  },
  setTxtPrevious: function (newText) {
    this.txtPrevious = newText
  },
  setLinkPagePrefix: function (linkPagePrefix) {
    this.linkPagePrefix = linkPagePrefix
  },
  setLinkPageSuffix: function (linkPageSuffix) {
    this.linkPageSuffix = linkPageSuffix
  },
  setTxtNext: function (newText) {
    this.txtNext = newText
  },
  setTxtResultOf: function (txtResultOf) {
    this.txtResultOf = txtResultOf
  },
  setTxtResultTo: function (txtResultTo) {
    this.txtResultTo = txtResultTo
  },
  setTxtResultPrefix: function (txtResultPrefix) {
    this.txtResultPrefix = txtResultPrefix
  },
  setTxtFirstPage: function (txtFirst) {
    this.txtFirst = txtFirst
  },
  setTxtLastPage: function (txtLast) {
    this.txtLast = txtLast
  },
  setTotalNumberOfRows: function (totalNumberOfRows) {
    this.totalNumberOfRows = totalNumberOfRows
  },
  setCallbackOnAfterNavigate: function (callbackOnAfterNavigate) {
    this.callbackOnAfterNavigate = callbackOnAfterNavigate
  },
  setLayoutCss: function (layoutCSS) {
    this.layoutCSS = layoutCSS
  },
  setMaximumNumberOfPageLinks: function (maximumNumberOfPageLinks) {
    this.maximumNumberOfPageLinks = maximumNumberOfPageLinks
  },
  init: function () {
    this.rowsPerPage = this.tableRef.getServersideSortNumberOfRows()
    DHTMLSuite.commonObj.loadCSS(this.layoutCSS)
    this.__createMainDivEls()
    this.setHTMLOfResultList()
    this.__createPageLinks()
    this.goToPage(1)
  },
  __createMainDivEls: function () {
    if (!this.targetRef) {
      alert(
        'Error creating table widget page handler. Remember to specify targetRef'
      )
      return
    }
    this.mainDivEl = document.createElement('DIV')
    this.mainDivEl.className = 'DHTMLSuite_tableWidgetPageHandler_mainDiv'
    this.targetRef.appendChild(this.mainDivEl)
    this.resultDivElement = document.createElement('DIV')
    this.resultDivElement.className =
      'DHTMLSuite_tableWidgetPageHandler_result'
    this.mainDivEl.appendChild(this.resultDivElement)
    this.pageListDivEl = document.createElement('DIV')
    this.pageListDivEl.className = 'DHTMLSuite_tableWidgetPageHandler_pageList'
    this.mainDivEl.appendChild(this.pageListDivEl)
  },
  setHTMLOfResultList: function () {
    this.resultDivElement.innerHTML = ''
    const html =
      this.txtResultPrefix +
      ((this.activePageNumber - 1) * this.rowsPerPage + 1) +
      ' ' +
      this.txtResultTo +
      ' ' +
      Math.min(
        this.totalNumberOfRows,
        this.activePageNumber * this.rowsPerPage
      ) +
      ' ' +
      this.txtResultOf +
      ' ' +
      this.totalNumberOfRows
    this.resultDivElement.innerHTML = html
  },
  __createPageLinks: function () {
    const ind = this.objectIndex
    this.pageListDivEl.innerHTML = ''
    const numberOfPages = Math.ceil(this.totalNumberOfRows / this.rowsPerPage)
    if (
      this.maximumNumberOfPageLinks &&
      this.maximumNumberOfPageLinks < numberOfPages
    ) {
      var span = document.createElement('SPAN')
      span.innerHTML = this.linkPagePrefix
      this.pageListDivEl.appendChild(span)
      span.className = 'DHTMLSuite_pageHandler_firstLink'
      const fl = document.createElement('A')
      fl.innerHTML = this.txtFirst
      fl.href = '#'
      fl.id = 'pageLink_1'
      fl.onclick = function (e) {
        return DHTMLSuite.variableStorage.arrayDSObjects[ind].__navigate(e)
      }
      span.appendChild(fl)
      DHTMLSuite.commonObj.__addEventEl(fl)
    }
    var span = document.createElement('SPAN')
    span.innerHTML = this.linkPagePrefix
    this.pageListDivEl.appendChild(span)
    span.className = 'DHTMLSuite_pageHandler_previousLink'
    const previousLink = document.createElement('A')
    previousLink.innerHTML = this.txtPrevious
    previousLink.href = '#'
    previousLink.id = 'previous'
    previousLink.onclick = function (e) {
      return DHTMLSuite.variableStorage.arrayDSObjects[ind].__navigate(e)
    }
    span.appendChild(previousLink)
    DHTMLSuite.commonObj.__addEventEl(previousLink)
    if (this.activePageNumber == 1) {
      previousLink.className = 'previousLinkDisabled'
    } else previousLink.className = 'previousLink'
    let startNumberToShow = 1
    let endNumberToShow = numberOfPages
    if (
      this.maximumNumberOfPageLinks &&
      this.maximumNumberOfPageLinks < numberOfPages
    ) {
      startNumberToShow = Math.max(
        1,
        Math.round(this.activePageNumber - this.maximumNumberOfPageLinks / 2)
      )
      endNumberToShow = Math.min(
        numberOfPages,
        startNumberToShow + this.maximumNumberOfPageLinks - 1
      )
      if (endNumberToShow - startNumberToShow < this.maximumNumberOfPageLinks) {
        startNumberToShow = Math.max(
          1,
          endNumberToShow - this.maximumNumberOfPageLinks + 1
        )
      }
    }
    for (let no = startNumberToShow; no <= endNumberToShow; no++) {
      var span = document.createElement('SPAN')
      span.innerHTML = this.linkPagePrefix
      this.pageListDivEl.appendChild(span)
      const pageLink = document.createElement('A')
      if (no == this.activePageNumber) {
        pageLink.className = 'DHTMLSuite_tableWidgetPageHandler_activePage'
      } else {
        pageLink.className = 'DHTMLSuite_tableWidgetPageHandler_inactivePage'
      }
      pageLink.innerHTML = no
      pageLink.href = '#'
      pageLink.id = 'pageLink_' + no
      pageLink.onclick = function (e) {
        return DHTMLSuite.variableStorage.arrayDSObjects[ind].__navigate(e)
      }
      DHTMLSuite.commonObj.__addEventEl(pageLink)
      this.pageListDivEl.appendChild(pageLink)
      var span = document.createElement('SPAN')
      span.innerHTML = this.linkPageSuffix
      this.pageListDivEl.appendChild(span)
    }
    var span = document.createElement('SPAN')
    span.innerHTML = this.linkPagePrefix
    this.pageListDivEl.appendChild(span)
    span.className = 'DHTMLSuite_pageHandler_nextLink'
    const nextLink = document.createElement('A')
    nextLink.innerHTML = this.txtNext
    nextLink.id = 'next'
    nextLink.href = '#'
    nextLink.onclick = function (e) {
      return DHTMLSuite.variableStorage.arrayDSObjects[ind].__navigate(e)
    }
    DHTMLSuite.commonObj.__addEventEl(nextLink)
    span.appendChild(nextLink)
    if (this.activePageNumber == numberOfPages) {
      nextLink.className = 'nextLinkDisabled'
    } else nextLink.className = 'nextLink'
    if (
      this.maximumNumberOfPageLinks &&
      this.maximumNumberOfPageLinks < numberOfPages
    ) {
      var span = document.createElement('SPAN')
      span.innerHTML = this.linkPagePrefix
      this.pageListDivEl.appendChild(span)
      span.className = 'DHTMLSuite_pageHandler_lastLink'
      const ll = document.createElement('A')
      ll.innerHTML = this.txtLast
      ll.href = '#'
      ll.id = 'pageLink_' + numberOfPages
      ll.onclick = function (e) {
        return DHTMLSuite.variableStorage.arrayDSObjects[ind].__navigate(e)
      }
      span.appendChild(ll)
      DHTMLSuite.commonObj.__addEventEl(ll)
    }
  },
  __navigate: function (e) {
    if (document.all) e = event
    const src = DHTMLSuite.commonObj.getSrcElement(e)
    const initActivePageNumber = this.activePageNumber
    const numberOfPages = Math.ceil(this.totalNumberOfRows / this.rowsPerPage)
    if (src.id.indexOf('pageLink_') >= 0) {
      const pageNo = src.id.replace(/[^0-9]/gi, '') / 1
      this.activePageNumber = pageNo
    }
    if (src.id == 'next') {
      this.activePageNumber++
      if (this.activePageNumber > numberOfPages) {
        this.activePageNumber = numberOfPages
      }
    }
    if (src.id == 'previous') {
      this.activePageNumber--
      if (this.activePageNumber < 1) this.activePageNumber = 1
    }
    if (this.activePageNumber != initActivePageNumber) {
      this.tableRef.serversideSortCurrentStartIndex =
        (this.activePageNumber - 1) * this.rowsPerPage
      this.tableRef.__getItemsFromServer(this.callbackOnAfterNavigate)
      this.setHTMLOfResultList()
      this.__createPageLinks()
    }
    return false
  },
  __resetActivePageNumber: function () {
    this.activePageNumber = 1
    this.setHTMLOfResultList()
    this.__createPageLinks()
  },
  goToPage: function (pageNo) {
    const initActivePageNumber = this.activePageNumber
    this.activePageNumber = pageNo
    if (this.activePageNumber != initActivePageNumber) {
      this.tableRef.serversideSortCurrentStartIndex =
        (this.activePageNumber - 1) * this.rowsPerPage
      this.tableRef.__getItemsFromServer(this.callbackOnAfterNavigate)
      this.setHTMLOfResultList()
      this.__createPageLinks()
    }
  }
}
DHTMLSuite.tableWidget = function () {
  let tableWidget_okToSort
  let activeColumn
  let idOfTable
  let tableObj
  let widthOfTable
  let heightOfTable
  let columnSortArray
  let layoutCSS
  let noCssLayout
  let serversideSort
  let serversideSortAscending
  let tableCurrentlySortedBy
  let serversideSortFileName
  let serversideSortNumberOfRows
  let serversideSortCurrentStartIndex
  let serversideSortExtraSearchCriterias
  let pageHandler
  let rowClickCallBackFunction
  let objectIndex
  this.serversideSort = false
  this.serversideSortAscending = true
  this.tableCurrentlySortedBy = false
  this.serversideSortFileName = false
  this.serversideSortCurrentStartIndex = 0
  this.serversideSortExtraSearchCriterias = ''
  this.rowClickCallBackFunction = false
  this.setRowDblClickCallBackFunction = false
  try {
    if (!standardObjectsCreated) DHTMLSuite.createStandardObjects()
  } catch (e) {
    alert('Include the dhtmlSuite-common.js file')
  }
  this.objectIndex = DHTMLSuite.variableStorage.arrayDSObjects.length
  DHTMLSuite.variableStorage.arrayDSObjects[this.objectIndex] = this
}
DHTMLSuite.tableWidget.prototype = {
  init: function () {
    this.tableWidget_okToSort = true
    this.activeColumn = false
    if (!this.layoutCSS) this.layoutCSS = 'table-widget.css'
    DHTMLSuite.commonObj.loadCSS(this.layoutCSS)
    this.__initTableWidget()
  },
  setLayoutCss: function (newCssFile) {
    this.layoutCSS = newCSSFile
  },
  setRowClickCallBack: function (rowClickCallBackFunction) {
    this.rowClickCallBackFunction = rowClickCallBackFunction
  },
  setRowDblClickCallBack: function (setRowDblClickCallBackFunction) {
    this.setRowDblClickCallBackFunction = setRowDblClickCallBackFunction
  },
  setServerSideSort: function (serversideSort) {
    this.serversideSort = serversideSort
  },
  setServersideSearchCriterias: function (serversideSortExtraSearchCriterias) {
    this.serversideSortExtraSearchCriterias =
      serversideSortExtraSearchCriterias
  },
  getServersideSortNumberOfRows: function (serversideSort) {
    return this.serversideSortNumberOfRows
  },
  setServersideSortNumberOfRows: function (serversideSortNumberOfRows) {
    this.serversideSortNumberOfRows = serversideSortNumberOfRows
  },
  setServersideSortFileName: function (serversideSortFileName) {
    this.serversideSortFileName = serversideSortFileName
  },
  setNoCssLayout: function () {
    this.noCssLayout = true
  },
  sortTableByColumn: function (columnIndex, howToSort) {
    if (!howToSort) howToSort = 'ascending'
    const tableObj = document.getElementById(this.idOfTable)
    const firstRow = tableObj.rows[0]
    const tds = firstRow.cells
    if (tds[columnIndex] && this.columnSortArray[columnIndex]) {
      this.__sortTable(tds[columnIndex], howToSort)
    }
  },
  setTableId: function (idOfTable) {
    this.idOfTable = idOfTable
    try {
      this.tableObj = document.getElementById(idOfTable)
    } catch (e) {}
  },
  setTableWidth: function (width) {
    this.widthOfTable = width
  },
  setTableHeight: function (height) {
    this.heightOfTable = height
  },
  setColumnSort: function (columnSortArray) {
    this.columnSortArray = columnSortArray
  },
  addNewRow: function (cellContent) {
    const tObj = document.getElementById(this.idOfTable)
    const tb = tObj.getElementsByTagName('TBODY')[0]
    const row = tb.insertRow(-1)
    for (let no = 0; no < cellContent.length; no++) {
      const cell = row.insertCell(-1)
      cell.innerHTML = cellContent[no]
    }
    this.__parseDataRows(tObj)
  },
  addNewColumn: function (columnContent, headerText, sortMethod) {
    this.columnSortArray[this.columnSortArray.length] = sortMethod
    const tableObj = document.getElementById(this.idOfTable)
    const tbody = tableObj.getElementsByTagName('TBODY')[0]
    const thead = tableObj.getElementsByTagName('THEAD')[0]
    const bodyRows = tbody.rows
    const headerRows = thead.rows
    cellIndexSubtract = 1
    if (DHTMLSuite.clientInfoObj.isMSIE) cellIndexSubtract = 0
    const headerCell = headerRows[0].insertCell(
      headerRows[0].cells.length - cellIndexSubtract
    )
    if (!this.noCssLayout) {
      headerCell.className = 'DHTMLSuite_tableWidget_headerCell'
    }
    headerCell.onselectstart = function () {
      return false
    }
    DHTMLSuite.commonObj.__addEventEl(headerCell)
    headerCell.innerHTML = headerText
    if (sortMethod) {
      this.__parseHeaderCell(headerCell)
    } else {
      headerCell.style.cursor = 'default'
    }
    headerRows[0].cells[headerRows[0].cells.length - 1].style.borderRightWidth =
      '0px'
    headerRows[0].cells[headerRows[0].cells.length - 2].style.borderRightWidth =
      '1px'
    for (let no = 0; no < columnContent.length; no++) {
      const dataCell = bodyRows[no].insertCell(
        bodyRows[no].cells.length - cellIndexSubtract
      )
      dataCell.innerHTML = columnContent[no]
    }
    this.__parseDataRows(tableObj)
  },
  setPageHandler: function (ref) {
    this.pageHandler = ref
  },
  __handleCallBackFromEvent: function (e, action) {
    if (document.all) e = event
    let src = DHTMLSuite.commonObj.getSrcElement(e)
    if (
      (action == 'rowClick' || action == 'rowDblClick') &&
      src.tagName.toLowerCase() != 'tr'
    ) {
      while (src.tagName.toLowerCase() != 'tr') src = src.parentNode
    }
    this.__createCallBackJavascriptString(action, src)
  },
  __createCallBackJavascriptString: function (action, el) {
    let callbackString = ''
    switch (action) {
      case 'rowClick':
        if (!this.rowClickCallBackFunction) return
        callbackString = this.rowClickCallBackFunction + '(el)'
        break
      case 'rowDblClick':
        if (!this.setRowDblClickCallBackFunction) return
        callbackString = this.setRowDblClickCallBackFunction + '(el)'
        break
    }
    this.__executeCallBack(callbackString, el)
  },
  __executeCallBack: function (callbackString, el) {
    if (!callbackString) return
    try {
      eval(callbackString)
    } catch (e) {}
  },
  __parseHeaderCell: function (cellRef) {
    if (!this.noCssLayout) {
      cellRef.onmouseover = this.__highlightTableHeader
      cellRef.onmouseout = this.__removeHighlightEffectFromTableHeader
      cellRef.onmousedown = this.__mousedownOnTableHeader
      cellRef.onmouseup = this.__highlightTableHeader
    } else {
      cellRef.style.cursor = 'pointer'
    }
    const refToThis = this
    cellRef.onclick = function () {
      refToThis.__sortTable(this)
    }
    DHTMLSuite.commonObj.__addEventEl(cellRef)
    const img = document.createElement('IMG')
    img.src = DHTMLSuite.configObj.imagePath + 'table-widget/arrow_up.gif'
    cellRef.appendChild(img)
    img.style.visibility = 'hidden'
  },
  __parseDataRows: function (parentObj) {
    const ind = this.objectIndex
    for (let no = 1; no < parentObj.rows.length; no++) {
      if (!this.noCssLayout) {
        parentObj.rows[no].onmouseover = this.__highlightTableRow
        parentObj.rows[no].onmouseout =
          this.__removeHighlightEffectFromTableRow
      }
      parentObj.rows[no].onclick = function (e) {
        DHTMLSuite.variableStorage.arrayDSObjects[
          ind
        ].__handleCallBackFromEvent(e, 'rowClick')
      }
      parentObj.rows[no].ondblclick = function (e) {
        DHTMLSuite.variableStorage.arrayDSObjects[
          ind
        ].__handleCallBackFromEvent(e, 'rowDblClick')
      }
      DHTMLSuite.commonObj.__addEventEl(parentObj.rows[no])
      for (var no2 = 0; no2 < this.columnSortArray.length; no2++) {
        try {
          if (this.columnSortArray[no2] && this.columnSortArray[no2] == 'N') {
            parentObj.rows[no].cells[no2].style.textAlign = 'right'
          }
        } catch (e) {
          alert(
            'Error in __parseDataRows method-row: ' + no + ',column:' + no2
          )
        }
      }
    }
    for (var no2 = 0; no2 < this.columnSortArray.length; no2++) {
      if (this.columnSortArray[no2] && this.columnSortArray[no2] == 'N') {
        parentObj.rows[0].cells[no2].style.textAlign = 'right'
      }
    }
  },
  __initTableWidget: function () {
    if (!this.columnSortArray) this.columnSortArray = new Array()
    this.widthOfTable = this.widthOfTable + ''
    this.heightOfTable = this.heightOfTable + ''
    const obj = document.getElementById(this.idOfTable)
    obj.parentNode.className = 'DHTMLSuite_widget_tableDiv'
    if (
      navigator.userAgent.toLowerCase().indexOf('safari') == -1 &&
      !this.noCssLayout
    ) {
      if (!DHTMLSuite.clientInfoObj.isMSIE) {
        obj.parentNode.style.overflow = 'hidden'
      } else {
        obj.parentNode.style.overflowX = 'hidden'
        obj.parentNode.style.overflowY = 'scroll'
      }
    }
    if (!this.noCssLayout) {
      if (this.widthOfTable.indexOf('%') >= 0) {
        obj.style.width = '100%'
        obj.parentNode.style.width = this.widthOfTable
      } else {
        obj.style.width = this.widthOfTable + 'px'
        obj.parentNode.style.width = this.widthOfTable + 'px'
      }
      if (this.heightOfTable.indexOf('%') >= 0) {
        obj.parentNode.style.height = this.heightOfTable
      } else {
        obj.parentNode.style.height = this.heightOfTable + 'px'
      }
    }
    if (!DHTMLSuite.clientInfoObj.isMSIE) {
      this.__addEndCol(obj)
    } else {
      obj.style.cssText = 'width:expression(this.parentNode.clientWidth)'
    }
    obj.cellSpacing = 0
    obj.cellPadding = 0
    if (!this.noCssLayout) obj.className = 'DHTMLSuite_tableWidget'
    const tHead = obj.getElementsByTagName('THEAD')[0]
    const cells = tHead.getElementsByTagName('TD')
    var tBody = obj.getElementsByTagName('TBODY')[0]
    tBody.className = 'DHTMLSuite_scrollingContent'
    for (let no = 0; no < cells.length; no++) {
      if (!this.noCssLayout) {
        cells[no].className = 'DHTMLSuite_tableWidget_headerCell'
      }
      cells[no].onselectstart = function () {
        return false
      }
      DHTMLSuite.commonObj.__addEventEl(cells[no])
      if (no == cells.length - 1 && !this.noCssLayout) {
        cells[no].style.borderRightWidth = '0px'
      }
      if (this.columnSortArray[no]) {
        this.__parseHeaderCell(cells[no])
      } else {
        cells[no].style.cursor = 'default'
      }
    }
    if (!this.noCssLayout) {
      var tBody = obj.getElementsByTagName('TBODY')[0]
      if (document.all && navigator.userAgent.indexOf('Opera') < 0) {
        tBody.className = 'DHTMLSuite_scrollingContent'
        tBody.style.display = 'block'
      } else {
        if (!this.noCssLayout) tBody.className = 'DHTMLSuite_scrollingContent'
        tBody.style.height =
          obj.parentNode.clientHeight - tHead.offsetHeight + 'px'
        if (navigator.userAgent.indexOf('Opera') >= 0) {
          obj.parentNode.style.overflow = 'auto'
        }
      }
    }
    this.__parseDataRows(obj)
    const ind = this.objectIndex
  },
  __addEndCol: function (obj) {
    const rows = obj.getElementsByTagName('TR')
    for (let no = 0; no < rows.length; no++) {
      const cell = rows[no].insertCell(rows[no].cells.length)
      cell.innerHTML =
        '<img src="' +
        DHTMLSuite.configObj.imagePath +
        'table-widget/transparent.gif" width="10" style="visibility:hidden">'
      cell.style.width = '13px'
      cell.width = '13'
      cell.style.overflow = 'hidden'
    }
  },
  __highlightTableHeader: function () {
    this.className = 'DHTMLSuite_tableWigdet_headerCellOver'
    if (document.all) {
      const divObj = this.parentNode.parentNode.parentNode.parentNode
      this.parentNode.style.top = divObj.scrollTop + 'px'
    }
  },
  __removeHighlightEffectFromTableHeader: function () {
    this.className = 'DHTMLSuite_tableWidget_headerCell'
  },
  __mousedownOnTableHeader: function () {
    this.className = 'DHTMLSuite_tableWigdet_headerCellDown'
    if (document.all) {
      const divObj = this.parentNode.parentNode.parentNode.parentNode
      this.parentNode.style.top = divObj.scrollTop + 'px'
    }
  },
  __sortNumeric: function (a, b) {
    a = a.replace(/,/, '.')
    b = b.replace(/,/, '.')
    a = a.replace(/[^\d\.\/]/g, '')
    b = b.replace(/[^\d\.\/]/g, '')
    if (a.indexOf('/') >= 0) a = eval(a)
    if (b.indexOf('/') >= 0) b = eval(b)
    return a / 1 - b / 1
  },
  __sortString: function (a, b) {
    if (a.toUpperCase() < b.toUpperCase()) return -1
    if (a.toUpperCase() > b.toUpperCase()) return 1
    return 0
  },
  __parseDataContentFromServer: function (ajaxIndex) {
    const content = DHTMLSuite.variableStorage.ajaxObjects[ajaxIndex].response
    if (content.indexOf('|||') == -1 && content.indexOf('###') == -1) {
      alert('Error in data from server\n' + content)
      return
    }
    this.__clearDataRows()
    const rows = content.split('|||')
    for (let no = 0; no < rows.length; no++) {
      const items = rows[no].split('###')
      if (items.length > 1) this.__fillDataRow(items)
    }
    this.__parseDataRows(this.tableObj)
  },
  __clearDataRows: function () {
    if (!this.tableObj) this.tableObj = document.getElementById(this.idOfTable)
    while (this.tableObj.rows.length > 1) {
      DHTMLSuite.discardElement(
        this.tableObj.rows[this.tableObj.rows.length - 1]
      )
    }
  },
  __fillDataRow: function (data) {
    if (!this.tableObj) this.tableObj = document.getElementById(this.idOfTable)
    const tbody = this.tableObj.getElementsByTagName('TBODY')[0]
    const row = tbody.insertRow(-1)
    for (let no = 0; no < data.length; no++) {
      const cell = row.insertCell(no)
      cell.innerHTML = data[no]
    }
  },
  updateTableHeader: function (columnIndex, direction) {
    const tableObj = document.getElementById(this.idOfTable)
    const firstRow = tableObj.rows[0]
    const tds = firstRow.cells
    const tdObj = tds[columnIndex]
    tdObj.setAttribute('direction', direction)
    tdObj.direction = direction
    let sortBy = tdObj.getAttribute('sortBy')
    if (!sortBy) sortBy = tdObj.sortBy
    this.tableCurrentlySortedBy = sortBy
    this.__updateSortArrow(tdObj, direction)
  },
  reloadDataFromServer: function () {
    this.__getItemsFromServer()
    if (this.pageHandler) this.pageHandler.__resetActivePageNumber()
  },
  resetServersideSortCurrentStartIndex: function () {
    this.serversideSortCurrentStartIndex = 0
  },
  __updateSortArrow: function (obj, direction) {
    var images = obj.getElementsByTagName('IMG')
    if (direction == 'descending') {
      images[0].src = images[0].src.replace('arrow_up', 'arrow_down')
      images[0].style.visibility = 'visible'
    } else {
      images[0].src = images[0].src.replace('arrow_down', 'arrow_up')
      images[0].style.visibility = 'visible'
    }
    if (this.activeColumn && this.activeColumn != obj) {
      var images = this.activeColumn.getElementsByTagName('IMG')
      images[0].style.visibility = 'hidden'
      this.activeColumn.removeAttribute('direction')
    }
    this.activeColumn = obj
  },
  __getParsedCallbackString: function (functionName) {
    const objIndex = this.objectIndex
    functionName = !functionName
      ? 'true'
      : functionName +
        '(DHTMLSuite.variableStorage.arrayDSObjects[' +
        objIndex +
        '])'
    return functionName
  },
  __getItemsFromServer: function (callbackFunction) {
    callbackFunction = this.__getParsedCallbackString(callbackFunction)
    const objIndex = this.objectIndex
    const url =
      this.serversideSortFileName +
      '?sortBy=' +
      this.tableCurrentlySortedBy +
      '&numberOfRows=' +
      this.serversideSortNumberOfRows +
      '&sortAscending=' +
      this.serversideSortAscending +
      '&startIndex=' +
      this.serversideSortCurrentStartIndex +
      this.serversideSortExtraSearchCriterias
    const index = DHTMLSuite.variableStorage.ajaxObjects.length
    try {
      DHTMLSuite.variableStorage.ajaxObjects[index] = new sack()
    } catch (e) {
      alert(
        'Unable to create ajax object. Please make sure that the sack js file is included on your page(js/ajax.js)'
      )
      return
    }
    DHTMLSuite.variableStorage.ajaxObjects[index].requestFile = url
    DHTMLSuite.variableStorage.ajaxObjects[index].onCompletion = function () {
      DHTMLSuite.variableStorage.arrayDSObjects[
        objIndex
      ].__parseDataContentFromServer(index)
      eval(callbackFunction)
    }
    DHTMLSuite.variableStorage.ajaxObjects[index].runAJAX()
  },
  __sortTable: function (obj, howToSort) {
    if (this.serversideSort) {
      if (!this.serversideSortFileName) {
        alert(
          'No server side file defined. Use the setServersideSortFileName to specify where to send the ajax request'
        )
        return
      }
      let sortBy = obj.getAttribute('sortBy')
      if (!sortBy) sortBy = obj.sortBy
      if (!sortBy) {
        alert(
          'Sort is not defined. Remember to set a sortBy attribute on the header <td> tags'
        )
        return
      }
      if (sortBy == this.tableCurrentlySortedBy) {
        this.serversideSortAscending = !this.serversideSortAscending
      } else this.serversideSortAscending = true
      if (howToSort) {
        this.serversideSortAscending = howToSort == 'ascending'
      }
      this.tableCurrentlySortedBy = sortBy
      this.serversideSortCurrentStartIndex = 0
      this.__getItemsFromServer()
      if (this.pageHandler) this.pageHandler.__resetActivePageNumber()
      this.__updateSortArrow(
        obj,
        this.serversideSortAscending ? 'ascending' : 'descending'
      )
      return
    }
    if (!this.tableWidget_okToSort) return
    this.tableWidget_okToSort = false
    let indexThis = 0
    let tmpObj = obj
    while (tmpObj.previousSibling) {
      tmpObj = tmpObj.previousSibling
      if (tmpObj.tagName == 'TD') indexThis++
    }
    if (obj.getAttribute('direction') || obj.direction) {
      direction = obj.getAttribute('direction')
      if (navigator.userAgent.indexOf('Opera') >= 0) direction = obj.direction
      if (direction == 'ascending' || howToSort == 'descending') {
        direction = 'descending'
        obj.setAttribute('direction', 'descending')
        obj.direction = 'descending'
      } else {
        direction = 'ascending'
        obj.setAttribute('direction', 'ascending')
        obj.direction = 'ascending'
      }
    } else {
      let curDir = 'ascending'
      if (howToSort) curDir = howToSort
      direction = curDir
      obj.setAttribute('direction', curDir)
      obj.direction = curDir
    }
    this.__updateSortArrow(obj, direction)
    const tableObj = obj.parentNode.parentNode.parentNode
    const tBody = tableObj.getElementsByTagName('TBODY')[0]
    const widgetIndex = tableObj.id.replace(/[^\d]/g, '')
    const sortMethod = this.columnSortArray[indexThis]
    let cellArray = new Array()
    const cellObjArray = new Array()
    for (var no = 1; no < tableObj.rows.length; no++) {
      const content = tableObj.rows[no].cells[indexThis].innerHTML + ''
      cellArray.push(content)
      cellObjArray.push(tableObj.rows[no].cells[indexThis])
    }
    cellArray =
      sortMethod == 'N'
        ? cellArray.sort(this.__sortNumeric)
        : cellArray.sort(this.__sortString)
    if (direction == 'descending') {
      for (var no = cellArray.length; no >= 0; no--) {
        for (var no2 = 0; no2 < cellObjArray.length; no2++) {
          if (
            cellObjArray[no2].innerHTML == cellArray[no] &&
            !cellObjArray[no2].getAttribute('allreadySorted')
          ) {
            cellObjArray[no2].setAttribute('allreadySorted', '1')
            tBody.appendChild(cellObjArray[no2].parentNode)
          }
        }
      }
    } else {
      for (var no = 0; no < cellArray.length; no++) {
        for (var no2 = 0; no2 < cellObjArray.length; no2++) {
          if (
            cellObjArray[no2].innerHTML == cellArray[no] &&
            !cellObjArray[no2].getAttribute('allreadySorted')
          ) {
            cellObjArray[no2].setAttribute('allreadySorted', '1')
            tBody.appendChild(cellObjArray[no2].parentNode)
          }
        }
      }
    }
    for (var no2 = 0; no2 < cellObjArray.length; no2++) {
      cellObjArray[no2].removeAttribute('allreadySorted')
    }
    this.tableWidget_okToSort = true
  },
  __highlightTableRow: function () {
    if (DHTMLSuite.clientInfoObj.isOpera) return
    this.className = 'DHTMLSuite_tableWidget_dataRollOver'
    if (document.all) {
      const divObj = this.parentNode.parentNode.parentNode
      const tHead = divObj.getElementsByTagName('TR')[0]
      tHead.style.top = divObj.scrollTop + 'px'
    }
  },
  __removeHighlightEffectFromTableRow: function () {
    if (DHTMLSuite.clientInfoObj.isOpera) return
    this.className = null
    if (document.all) {
      const divObj = this.parentNode.parentNode.parentNode
      const tHead = divObj.getElementsByTagName('TR')[0]
      tHead.style.top = divObj.scrollTop + 'px'
    }
  }
}
