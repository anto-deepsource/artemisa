if (!window.DHTMLSuite) var DHTMLSuite = new Object()
DHTMLSuite.paneSplitterPane = function (parentRef) {
  let divElement
  let divElCollapsed
  let divElCollapsedInner
  let contentDiv
  let headerDiv
  let titleSpan
  let paneModel
  let resizeDiv
  let tabDiv
  let divTransparentForResize
  var parentRef
  let divClose
  let divCollapse
  let divExpand
  let divRefresh
  let slideIsInProgress
  let reloadIntervalHandlers
  let contentScrollTopPositions
  this.contents = new Array()
  this.reloadIntervalHandlers = new Object()
  this.contentScrollTopPositions = new Object()
  this.parentRef = parentRef
  let activeContentIndex
  this.activeContentIndex = false
  this.slideIsInProgress = false
  let objectIndex
  this.objectIndex = DHTMLSuite.variableStorage.arrayDSObjects.length
  DHTMLSuite.variableStorage.arrayDSObjects[this.objectIndex] = this
}
DHTMLSuite.paneSplitterPane.prototype = {
  addDataSource: function (paneModelRef) {
    this.paneModel = paneModelRef
  },
  addContent: function (paneContentModelObject, jsCodeToExecuteWhenComplete) {
    const retValue = this.paneModel.addContent(paneContentModelObject)
    if (!retValue) return false
    this.__addOneContentDiv(
      paneContentModelObject,
      jsCodeToExecuteWhenComplete
    )
    this.__updateTabContent()
    this.__updateView()
    if (this.paneModel.getCountContent() == 1) {
      this.showContent(paneContentModelObject.id)
    }
    return retValue
  },
  showContent: function (idOfContentObject) {
    for (let no = 0; no < this.paneModel.contents.length; no++) {
      if (this.paneModel.contents[no].id == idOfContentObject) {
        this.__updatePaneView(no)
        return
      }
    }
  },
  loadContent: function (
    idOfContentObject,
    url,
    refreshAfterSeconds,
    internalCall,
    onCompleteJsCode
  ) {
    if (!url) return
    for (let no = 0; no < this.paneModel.contents.length; no++) {
      if (this.paneModel.contents[no].id == idOfContentObject) {
        if (internalCall && !this.paneModel.contents[no].refreshAfterSeconds) {
          return
        }
        const ajaxWaitMsg = this.parentRef.waitMessage
        this.paneModel.contents[no].__setContentUrl(url)
        if (refreshAfterSeconds && !internalCall) {
          this.paneModel.contents[no].__setRefreshAfterSeconds(
            refreshAfterSeconds
          )
        }
        if (refreshAfterSeconds) {
          this.__handleContentReload(idOfContentObject, refreshAfterSeconds)
        }
        try {
          var dynContent = new DHTMLSuite.dynamicContent()
        } catch (e) {
          alert('Include dhtmlSuite-dynamicContent.js')
        }
        dynContent.setWaitMessage(ajaxWaitMsg)
        dynContent.loadContent(
          this.paneModel.contents[no].htmlElementId,
          url,
          onCompleteJsCode
        )
        dynContent = false
        return
      }
    }
  },
  isUrlLoadedInPane: function (idOfContentObject, url) {
    const contentIndex = this.paneModel.__getIndexById(idOfContentObject)
    if (contentIndex !== false) {
      if (this.paneModel.contents[contentIndex].contentUrl == url) return true
    }
    return false
  },
  reloadContent: function (idOfContentObject) {
    const contentIndex = this.paneModel.__getIndexById(idOfContentObject)
    if (contentIndex !== false) {
      this.loadContent(
        idOfContentObject,
        this.paneModel.contents[contentIndex].contentUrl
      )
    }
  },
  setRefreshAfterSeconds: function (idOfContentObject, refreshAfterSeconds) {
    for (let no = 0; no < this.paneModel.contents.length; no++) {
      if (this.paneModel.contents[no].id == idOfContentObject) {
        if (!this.paneModel.contents[no].refreshAfterSeconds) {
          this.loadContent(
            idOfContentObject,
            this.paneModel.contents[no].contentUrl,
            refreshAfterSeconds
          )
        }
        this.paneModel.contents[no].__setRefreshAfterSeconds(
          refreshAfterSeconds
        )
        this.__handleContentReload(idOfContentObject)
      }
    }
  },
  setContentTitle: function (idOfContent, newTitle) {
    const contentModelIndex = this.paneModel.__getIndexById(idOfContent)
    if (contentModelIndex !== false) {
      const contentModelObj = this.paneModel.contents[contentModelIndex]
      contentModelObj.__setTitle(newTitle)
      this.__updateHeaderBar(this.activeContentIndex)
    }
  },
  setContentTabTitle: function (idOfContent, newTitle) {
    const contentModelIndex = this.paneModel.__getIndexById(idOfContent)
    if (contentModelIndex !== false) {
      const contentModelObj = this.paneModel.contents[contentModelIndex]
      contentModelObj.__setTabTitle(newTitle)
      this.__updateTabContent()
    }
  },
  hidePane: function () {
    this.paneModel.__setVisible(false)
    this.expand()
    this.divElement.style.display = 'none'
    this.__executeCallBack(
      'hide',
      this.paneModel.contents[this.activeContentIndex]
    )
  },
  showPane: function () {
    this.paneModel.__setVisible(true)
    this.divElement.style.display = 'block'
    this.__executeCallBack(
      'show',
      this.paneModel.contents[this.activeContentIndex]
    )
  },
  collapse: function () {
    this.__collapse()
    if (!this.parentRef.dataModel.collapseButtonsInTitleBars) {
      this.parentRef.__toggleCollapseExpandButton(
        this.paneModel.getPosition(),
        'collapse'
      )
    }
  },
  expand: function () {
    this.__expand()
    if (!this.parentRef.dataModel.collapseButtonsInTitleBars) {
      this.parentRef.__toggleCollapseExpandButton(
        this.paneModel.getPosition(),
        'expand'
      )
    }
  },
  getIdOfCurrentlyDisplayedContent: function () {
    return this.paneModel.contents[this.activeContentIndex].id
  },
  getHtmlElIdOfCurrentlyDisplayedContent: function () {
    return this.paneModel.contents[this.activeContentIndex].htmlElementId
  },
  __getSizeOfPaneInPixels: function () {
    const retArray = new Object()
    retArray.width = this.divElement.offsetWidth
    retArray.height = this.divElement.offsetHeight
    return retArray
  },
  __reloadDisplayedContent: function () {
    this.reloadContent(this.paneModel.contents[this.activeContentIndex].id)
  },
  __getReferenceTomainDivEl: function () {
    return this.divElement
  },
  __executeResizeCallBack: function () {
    this.__executeCallBack('resize')
  },
  __executeCallBack: function (whichCallBackAction, contentObj) {
    let callbackString = false
    switch (whichCallBackAction) {
      case 'show':
        if (!this.paneModel.callbackOnShow) return
        callbackString = this.paneModel.callbackOnShow
        break
      case 'collapse':
        if (!this.paneModel.callbackOnCollapse) return
        callbackString = this.paneModel.callbackOnCollapse
        break
      case 'expand':
        if (!this.paneModel.callbackOnExpand) return
        callbackString = this.paneModel.callbackOnExpand
        break
      case 'hide':
        if (!this.paneModel.callbackOnHide) return
        callbackString = this.paneModel.callbackOnHide
        break
      case 'slideIn':
        if (!this.paneModel.callbackOnSlideIn) return
        callbackString = this.paneModel.callbackOnSlideIn
        break
      case 'slideOut':
        if (!this.paneModel.callbackOnSlideOut) return
        callbackString = this.paneModel.callbackOnSlideOut
        break
      case 'closeContent':
        if (!this.paneModel.callbackOnCloseContent) return
        callbackString = this.paneModel.callbackOnCloseContent
        break
      case 'beforeCloseContent':
        if (!this.paneModel.callbackOnBeforeCloseContent) return true
        callbackString = this.paneModel.callbackOnBeforeCloseContent
        break
      case 'tabSwitch':
        if (!this.paneModel.callbackOnTabSwitch) return
        callbackString = this.paneModel.callbackOnTabSwitch
        break
      case 'resize':
        if (!this.paneModel.callbackOnResize) return
        callbackString = this.paneModel.callbackOnResize
        break
    }
    if (!callbackString) return
    if (!contentObj) contentObj = false
    callbackString = this.__getCallBackString(
      callbackString,
      whichCallBackAction,
      contentObj
    )
    return this.__executeCallBackString(callbackString, contentObj)
  },
  __getCallBackString: function (
    callbackString,
    whichCallBackAction,
    contentObj
  ) {
    if (callbackString.indexOf('(') >= 0) return callbackString
    if (contentObj) {
      callbackString =
        callbackString +
        '(this.paneModel,"' +
        whichCallBackAction +
        '",contentObj)'
    } else {
      callbackString =
        callbackString + '(this.paneModel,"' + whichCallBackAction + '")'
    }
    callbackString = callbackString
    return callbackString
  },
  __executeCallBackString: function (callbackString, contentObj) {
    try {
      return eval(callbackString)
    } catch (e) {
      alert(
        'Could not execute specified call back function:\n' +
          callbackString +
          '\n\nError:\n' +
          e.name +
          '\n' +
          e.message +
          '\n' +
          "\nMake sure that there aren't any errors in your function.\nAlso remember that contentObj would not be present when you click close on the last tab\n(In case a close tab event triggered this callback function)"
      )
    }
  },
  __handleContentReload: function (id) {
    const ind = this.objectIndex
    const contentIndex = this.paneModel.__getIndexById(id)
    if (contentIndex !== false) {
      const contentRef = this.paneModel.contents[contentIndex]
      if (contentRef.refreshAfterSeconds) {
        if (this.reloadIntervalHandlers[id]) {
          clearInterval(this.reloadIntervalHandlers[id])
        }
        this.reloadIntervalHandlers[id] = setInterval(
          'DHTMLSuite.variableStorage.arrayDSObjects[' +
            ind +
            '].loadContent("' +
            id +
            '","' +
            contentRef.contentUrl +
            '",' +
            contentRef.refreshAfterSeconds +
            ',true)',
          contentRef.refreshAfterSeconds * 1000
        )
      } else {
        if (this.reloadIntervalHandlers[id]) {
          clearInterval(this.reloadIntervalHandlers[id])
        }
      }
    }
  },
  __createPane: function () {
    this.divElement = document.createElement('DIV')
    this.divElement.style.position = 'absolute'
    this.divElement.className = 'DHTMLSuite_pane'
    this.divElement.id = 'DHTMLSuite_pane_' + this.paneModel.getPosition()
    document.body.appendChild(this.divElement)
    this.__createHeaderBar()
    this.__createContentPane()
    this.__createTabBar()
    this.__createCollapsedPane()
    this.__createTransparentDivForResize()
    this.__updateView()
    this.__addContentDivs()
    this.__setSize()
  },
  __createTransparentDivForResize: function () {
    this.divTransparentForResize = document.createElement('DIV')
    const ref = this.divTransparentForResize
    ref.style.opacity = '0'
    ref.style.display = 'none'
    ref.style.filter = 'alpha(opacity=0)'
    ref.style.position = 'absolute'
    ref.style.left = '0px'
    ref.style.top = this.headerDiv.offsetHeight + 'px'
    ref.style.height = '90%'
    ref.style.width = '100%'
    ref.style.backgroundColor = '#FFF'
    ref.style.zIndex = '1000'
    this.divElement.appendChild(ref)
  },
  __createCollapsedPane: function () {
    const ind = this.objectIndex
    var pos = this.paneModel.getPosition()
    let buttonSuffix = 'Vertical'
    if (pos == 'west' || pos == 'east') buttonSuffix = 'Horizontal'
    if (pos == 'center') buttonSuffix = ''
    this.divElCollapsed = document.createElement('DIV')
    const obj = this.divElCollapsed
    obj.className = 'DHTMLSuite_pane_collapsed_' + pos
    obj.style.visibility = 'hidden'
    obj.style.position = 'absolute'
    this.divElCollapsedInner = document.createElement('DIV')
    this.divElCollapsedInner.className = 'DHTMLSuite_pane_collapsedInner'
    this.divElCollapsedInner.onmouseover = this.__mouseoverHeaderButton
    this.divElCollapsedInner.onmouseout = this.__mouseoutHeaderButton
    this.divElCollapsedInner.onclick = function (e) {
      DHTMLSuite.variableStorage.arrayDSObjects[ind].__slidePane(e)
    }
    DHTMLSuite.commonObj.__addEventEl(this.divElCollapsedInner)
    obj.appendChild(this.divElCollapsedInner)
    const buttonDiv = document.createElement('DIV')
    buttonDiv.className = 'buttonDiv'
    this.divElCollapsedInner.appendChild(buttonDiv)
    document.body.appendChild(obj)
    if (this.parentRef.dataModel.collapseButtonsInTitleBars) {
      if (
        this.paneModel.getPosition() == 'east' ||
        this.paneModel.getPosition() == 'west'
      ) {
        this.divElCollapsedInner.style.width =
          this.parentRef.paneSizeCollapsed - 6 + 'px'
        this.divElCollapsed.style.width =
          this.parentRef.paneSizeCollapsed + 'px'
        if (this.paneModel.getPosition() == 'east') {
          this.divElCollapsedInner.style.marginLeft = '3px'
        }
      } else {
        this.divElCollapsedInner.style.height =
          this.parentRef.paneSizeCollapsed - 6 + 'px'
        this.divElCollapsed.style.height =
          this.parentRef.paneSizeCollapsed + 'px'
        buttonDiv.style.cssText = 'float:right;clear:both'
      }
      var pos = this.paneModel.getPosition()
      this.divExpand = document.createElement('DIV')
      if (pos == 'south' || pos == 'east') {
        this.divExpand.className = 'collapseButton' + buttonSuffix
      } else this.divExpand.className = 'expandButton' + buttonSuffix
      this.divExpand.onclick = function () {
        return DHTMLSuite.variableStorage.arrayDSObjects[ind].__expand()
      }
      this.divExpand.onmouseover = this.__mouseoverHeaderButton
      this.divExpand.onmouseout = this.__mouseoutHeaderButton
      DHTMLSuite.commonObj.__addEventEl(this.divExpand)
      buttonDiv.appendChild(this.divExpand)
    }
  },
  __autoSlideInPane: function (e) {
    if (document.all) e = event
    const state = this.paneModel.__getState()
    if (state == 'collapsed' && this.divElement.style.visibility != 'hidden') {
      if (!DHTMLSuite.commonObj.isObjectClicked(this.divElement, e)) {
        this.__slidePane(e, true)
      }
    }
  },
  __slidePane: function (e, forceSlide) {
    if (this.slideIsInProgress) return
    this.parentRef.paneZIndexCounter++
    if (document.all) e = event
    let src = DHTMLSuite.commonObj.getSrcElement(e)
    if (src.className == 'buttonDiv') src = src.parentNode
    if (src.className.indexOf('collapsed') < 0 && !forceSlide) return
    this.slideIsInProgress = true
    const state = this.paneModel.__getState()
    let hideWhenComplete = true
    if (this.divElement.style.visibility == 'hidden') {
      this.__executeCallBack(
        'slideOut',
        this.paneModel.contents[this.activeContentIndex]
      )
      this.__setSlideInitPosition()
      this.divElement.style.visibility = 'visible'
      this.divElement.style.zIndex = 16000 + this.parentRef.paneZIndexCounter
      this.divElCollapsed.style.zIndex =
        16000 + this.parentRef.paneZIndexCounter
      var slideTo = this.__getSlideToCoordinates(true)
      hideWhenComplete = false
      var slideSpeed = this.__getSlideSpeed(true)
    } else {
      this.__executeCallBack(
        'slideIn',
        this.paneModel.contents[this.activeContentIndex]
      )
      var slideTo = this.__getSlideToCoordinates(false)
      var slideSpeed = this.__getSlideSpeed(false)
    }
    this.__processSlideByPixels(
      slideTo,
      slideSpeed * this.parentRef.slideSpeed,
      this.__getCurrentCoordinateInPixels(),
      hideWhenComplete
    )
  },
  __setSlideInitPosition: function () {
    const bw = DHTMLSuite.clientInfoObj.getBrowserWidth()
    const bh = DHTMLSuite.clientInfoObj.getBrowserHeight()
    const pos = this.paneModel.getPosition()
    switch (pos) {
      case 'west':
        this.divElement.style.left = 0 - this.paneModel.size + 'px'
        break
      case 'east':
        this.divElement.style.left = bw + 'px'
        break
      case 'north':
        this.divElement.style.top = 0 - this.paneModel.size + 'px'
        break
      case 'south':
        this.divElement.style.top = bh + 'px'
        break
    }
  },
  __getCurrentCoordinateInPixels: function () {
    const pos = this.paneModel.getPosition()
    const left = this.divElement.style.left.replace('px', '') / 1
    const top = this.divElement.style.top.replace('px', '') / 1
    switch (pos) {
      case 'west':
        return left
      case 'east':
        return left
      case 'south':
        return top
      case 'north':
        return top
    }
  },
  __getSlideSpeed: function (slideOut) {
    const pos = this.paneModel.getPosition()
    switch (pos) {
      case 'west':
      case 'north':
        if (slideOut) return 1
        else return -1
      case 'south':
      case 'east':
        if (slideOut) return -1
        else return 1
    }
  },
  __processSlideByPixels: function (
    slideTo,
    slidePixels,
    currentPos,
    hideWhenComplete
  ) {
    const pos = this.paneModel.getPosition()
    currentPos = currentPos + slidePixels
    let repeatSlide = true
    if (slidePixels > 0 && currentPos > slideTo) {
      currentPos = slideTo
      repeatSlide = false
    }
    if (slidePixels < 0 && currentPos < slideTo) {
      currentPos = slideTo
      repeatSlide = false
    }
    switch (pos) {
      case 'west':
      case 'east':
        this.divElement.style.left = currentPos + 'px'
        break
      case 'north':
      case 'south':
        this.divElement.style.top = currentPos + 'px'
    }
    if (repeatSlide) {
      const ind = this.objectIndex
      setTimeout(
        'DHTMLSuite.variableStorage.arrayDSObjects[' +
          ind +
          '].__processSlideByPixels(' +
          slideTo +
          ',' +
          slidePixels +
          ',' +
          currentPos +
          ',' +
          hideWhenComplete +
          ')',
        10
      )
    } else {
      if (hideWhenComplete) {
        this.divElement.style.visibility = 'hidden'
        this.divElement.style.zIndex = 11000
        this.divElCollapsed.style.zIndex = 12000
      }
      this.slideIsInProgress = false
    }
  },
  __getSlideToCoordinates: function (slideOut) {
    const bw = DHTMLSuite.clientInfoObj.getBrowserWidth()
    const bh = DHTMLSuite.clientInfoObj.getBrowserHeight()
    const pos = this.paneModel.getPosition()
    switch (pos) {
      case 'west':
        if (slideOut) {
          return (
            this.parentRef.paneSizeCollapsed +
            this.parentRef.verticalSplitterSize
          )
        } else return 0 - this.paneModel.size
      case 'east':
        if (slideOut) {
          return (
            bw -
            this.parentRef.paneSizeCollapsed -
            this.paneModel.size -
            this.parentRef.verticalSplitterSize -
            1
          )
        } else return bw
      case 'north':
        if (slideOut) {
          return (
            this.parentRef.paneSizeCollapsed +
            this.parentRef.horizontalSplitterSize
          )
        } else return 0 - this.paneModel.size
      case 'south':
        if (slideOut) {
          return (
            bh -
            this.parentRef.paneSizeCollapsed -
            this.paneModel.size -
            this.parentRef.horizontalSplitterSize -
            1
          )
        } else return bh
    }
  },
  __updateCollapsedSize: function () {
    const pos = this.paneModel.getPosition()
    let size
    if (pos == 'west' || pos == 'east') size = this.divElCollapsed.offsetWidth
    if (pos == 'north' || pos == 'south') {
      size = this.divElCollapsed.offsetHeight
    }
    if (size) this.parentRef.__setPaneSizeCollapsed(size)
  },
  __createHeaderBar: function () {
    const ind = this.objectIndex
    const pos = this.paneModel.getPosition()
    let buttonSuffix = 'Vertical'
    if (pos == 'west' || pos == 'east') buttonSuffix = 'Horizontal'
    if (pos == 'center') buttonSuffix = ''
    this.headerDiv = document.createElement('DIV')
    this.headerDiv.className = 'DHTMLSuite_paneHeader'
    this.headerDiv.style.position = 'relative'
    this.divElement.appendChild(this.headerDiv)
    this.titleSpan = document.createElement('SPAN')
    this.titleSpan.className = 'paneTitle'
    this.headerDiv.appendChild(this.titleSpan)
    const buttonDiv = document.createElement('DIV')
    buttonDiv.style.position = 'absolute'
    buttonDiv.style.right = '0px'
    buttonDiv.style.top = '0px'
    buttonDiv.className = 'DHTMLSuite_paneHeader_buttonDiv'
    this.headerDiv.appendChild(buttonDiv)
    this.divClose = document.createElement('DIV')
    this.divClose.className = 'closeButton'
    this.divClose.onmouseover = this.__mouseoverHeaderButton
    this.divClose.onmouseout = this.__mouseoutHeaderButton
    this.divClose.innerHTML = '<span></span>'
    this.divClose.onclick = function () {
      return DHTMLSuite.variableStorage.arrayDSObjects[ind].__close()
    }
    DHTMLSuite.commonObj.__addEventEl(this.divClose)
    buttonDiv.appendChild(this.divClose)
    if (
      pos != 'center' &&
      this.parentRef.dataModel.collapseButtonsInTitleBars
    ) {
      this.divCollapse = document.createElement('DIV')
      if (pos == 'south' || pos == 'east') {
        this.divCollapse.className = 'expandButton' + buttonSuffix
      } else this.divCollapse.className = 'collapseButton' + buttonSuffix
      this.divCollapse.innerHTML = '<span></span>'
      this.divCollapse.onclick = function () {
        return DHTMLSuite.variableStorage.arrayDSObjects[ind].__collapse()
      }
      this.divCollapse.onmouseover = this.__mouseoverHeaderButton
      this.divCollapse.onmouseout = this.__mouseoutHeaderButton
      DHTMLSuite.commonObj.__addEventEl(this.divCollapse)
      buttonDiv.appendChild(this.divCollapse)
    }
    this.divRefresh = document.createElement('DIV')
    this.divRefresh.className = 'refreshButton'
    this.divRefresh.onmouseover = this.__mouseoverHeaderButton
    this.divRefresh.onmouseout = this.__mouseoutHeaderButton
    this.divRefresh.onclick = function () {
      return DHTMLSuite.variableStorage.arrayDSObjects[
        ind
      ].__reloadDisplayedContent()
    }
    DHTMLSuite.commonObj.__addEventEl(this.divRefresh)
    buttonDiv.appendChild(this.divRefresh)
    this.headerDiv.onselectstart = function () {
      return false
    }
  },
  __mouseoverHeaderButton: function () {
    if (this.className.indexOf('Over') == -1) {
      this.className = this.className + 'Over'
    }
  },
  __mouseoutHeaderButton: function () {
    this.className = this.className.replace('Over', '')
  },
  __close: function (e) {
    const id = this.paneModel.contents[this.activeContentIndex].id
    const ok = this.__getOnBeforeCloseResult(this.activeContentIndex)
    if (!ok) return
    if (id) {
      this.__executeCallBack(
        'closeContent',
        this.paneModel.contents[this.activeContentIndex]
      )
      DHTMLSuite.discardElement(
        this.paneModel.contents[this.activeContentIndex].htmlElementId
      )
    }
    this.activeContentIndex = this.paneModel.__deleteContent(
      this.activeContentIndex
    )
    if (this.activeContentIndex || this.activeContentIndex == 0) {
      this.__executeCallBack(
        'tabSwitch',
        this.paneModel.contents[this.activeContentIndex]
      )
    }
    this.__updatePaneView(this.activeContentIndex)
  },
  __closeAllClosableTabs: function () {
    for (let no = 0; no < this.paneModel.contents.length; no++) {
      const closable = this.paneModel.contents[no].__getClosable()
      if (closable) {
        const id = this.paneModel.contents[no].id
        DHTMLSuite.discardElement(this.paneModel.contents[no].htmlElementId)
        this.activeContentIndex = this.paneModel.__deleteContent(no)
        this.__updatePaneView(this.activeContentIndex)
        no--
      }
    }
  },
  __getOnBeforeCloseResult: function (contentIndex) {
    return this.__executeCallBack(
      'beforeCloseContent',
      this.paneModel.contents[contentIndex]
    )
  },
  __deleteContentByIndex: function (contentIndex) {
    if (this.paneModel.getCountContent() == 0) return
    if (!this.__getOnBeforeCloseResult(contentIndex)) return
    const htmlId = this.paneModel.contents[contentIndex].htmlElementId
    if (htmlId) {
      try {
        DHTMLSuite.discardElement(htmlId)
      } catch (e) {}
    }
    const tmpIndex = this.paneModel.__deleteContent(contentIndex)
    if (contentIndex == this.activeContentIndex) {
      this.activeContentIndex = tmpIndex
    }
    if (this.activeContentIndex > contentIndex) this.activeContentIndex--
    if (tmpIndex === false) this.activeContentIndex = false
    this.__updatePaneView(this.activeContentIndex)
  },
  __deleteContentById: function (id) {
    const index = this.paneModel.__getIndexById(id)
    if (index !== false) this.__deleteContentByIndex(index)
  },
  __collapse: function () {
    this.__updateCollapsedSize()
    this.paneModel.__setState('collapsed')
    this.divElCollapsed.style.visibility = 'visible'
    this.divElement.style.visibility = 'hidden'
    this.__updateView()
    this.parentRef.__hideResizeHandle(this.paneModel.getPosition())
    this.parentRef.__positionPanes()
    this.__executeCallBack(
      'collapse',
      this.paneModel.contents[this.activeContentIndex]
    )
  },
  __expand: function () {
    this.paneModel.__setState('expanded')
    this.divElCollapsed.style.visibility = 'hidden'
    this.divElement.style.visibility = 'visible'
    this.__updateView()
    this.parentRef.__showResizeHandle(this.paneModel.getPosition())
    this.parentRef.__positionPanes()
    this.__executeCallBack(
      'expand',
      this.paneModel.contents[this.activeContentIndex]
    )
  },
  __updateHeaderBar: function (index) {
    if (index === false) {
      this.divClose.style.display = 'none'
      this.divRefresh.style.display = 'none'
      try {
        if (
          this.paneModel.getPosition() != 'center' &&
          this.paneModel.collapsable
        ) {
          this.divCollapse.style.display = 'block'
        } else this.divCollapse.style.display = 'none'
      } catch (e) {}
      this.titleSpan.innerHTML = ''
      return
    }
    this.divClose.style.display = 'block'
    this.divRefresh.style.display = 'block'
    if (this.divCollapse) this.divCollapse.style.display = 'block'
    this.titleSpan.innerHTML = this.paneModel.contents[index].title
    const contentObj = this.paneModel.contents[index]
    if (!contentObj.closable) this.divClose.style.display = 'none'
    if (!contentObj.displayRefreshButton || !contentObj.contentUrl) {
      this.divRefresh.style.display = 'none'
    }
    if (!this.paneModel.collapsable) {
      if (this.divCollapse) this.divCollapse.style.display = 'none'
    }
  },
  __showButtons: function () {
    const div = this.headerDiv.getElementsByTagName('DIV')[0]
    div.style.visibility = 'visible'
  },
  __hideButtons: function () {
    const div = this.headerDiv.getElementsByTagName('DIV')[0]
    div.style.visibility = 'hidden'
  },
  __updateView: function () {
    if (
      this.paneModel.getCountContent() > 0 &&
      this.activeContentIndex === false
    ) {
      this.activeContentIndex = 0
    }
    this.tabDiv.style.display = 'block'
    this.headerDiv.style.display = 'block'
    const pos = this.paneModel.getPosition()
    if (pos == 'south' || pos == 'north') {
      this.divElCollapsed.style.height = this.parentRef.paneSizeCollapsed
    }
    if (this.paneModel.getCountContent() < 2) {
      this.tabDiv.style.display = 'none'
    }
    if (this.activeContentIndex !== false) {
      if (!this.paneModel.contents[this.activeContentIndex].title) {
        this.headerDiv.style.display = 'none'
      }
    }
    if (this.paneModel.state == 'expanded') this.__showButtons()
    else this.__hideButtons()
    this.__setSize()
  },
  __createContentPane: function () {
    this.contentDiv = document.createElement('DIV')
    this.contentDiv.className = 'DHTMLSuite_paneContent'
    this.contentDiv.id =
      'DHTMLSuite_paneContent' + this.paneModel.getPosition()
    if (!this.paneModel.scrollbars) this.contentDiv.style.overflow = 'hidden'
    this.divElement.appendChild(this.contentDiv)
  },
  __createTabBar: function () {
    this.tabDiv = document.createElement('DIV')
    this.tabDiv.className = 'DHTMLSuite_paneTabs'
    this.divElement.appendChild(this.tabDiv)
    this.__updateTabContent()
  },
  __updateTabContent: function () {
    this.tabDiv.innerHTML = ''
    const tableObj = document.createElement('TABLE')
    tableObj.style.padding = '0px'
    tableObj.style.margin = '0px'
    tableObj.cellPadding = 0
    tableObj.cellSpacing = 0
    this.tabDiv.appendChild(tableObj)
    const tbody = document.createElement('TBODY')
    tableObj.appendChild(tbody)
    const row = tbody.insertRow(0)
    const contents = this.paneModel.getContents()
    const ind = this.objectIndex
    for (let no = 0; no < contents.length; no++) {
      const cell = row.insertCell(-1)
      const divTag = document.createElement('DIV')
      divTag.className = 'paneSplitterInactiveTab'
      cell.appendChild(divTag)
      const aTag = document.createElement('A')
      aTag.title = contents[no].tabTitle
      contents[no].tabTitle = contents[no].tabTitle + ''
      aTag.innerHTML = contents[no].tabTitle.replace(' ', '&nbsp;') + ''
      aTag.id = 'paneTabLink' + no
      aTag.href = '#'
      aTag.onclick = function (e) {
        return DHTMLSuite.variableStorage.arrayDSObjects[ind].__tabClick(e)
      }
      divTag.appendChild(aTag)
      DHTMLSuite.commonObj.__addEventEl(aTag)
      divTag.appendChild(document.createElement('SPAN'))
    }
    this.__updateTabView(0)
  },
  __updateTabView: function (activeTab) {
    const tabDivs = this.tabDiv.getElementsByTagName('DIV')
    for (let no = 0; no < tabDivs.length; no++) {
      if (no == activeTab) {
        tabDivs[no].className = 'paneSplitterActiveTab'
      } else tabDivs[no].className = 'paneSplitterInactiveTab'
    }
  },
  __tabClick: function (e) {
    if (document.all) e = event
    let inputObj = DHTMLSuite.commonObj.getSrcElement(e)
    if (inputObj.tagName != 'A') inputObj = inputObj.parentNode
    const numIdClickedTab = inputObj.id.replace(/[^0-9]/gi, '')
    if (numIdClickedTab != this.activeContentIndex) {
      this.__updatePaneContentScrollTopPosition(
        this.activeContentIndex,
        numIdClickedTab
      )
    }
    this.__updatePaneView(numIdClickedTab)
    this.__executeCallBack(
      'tabSwitch',
      this.paneModel.contents[this.activeContentIndex]
    )
    return false
  },
  __updatePaneContentScrollTopPosition: function (
    idOfContentToHide,
    idOfContentToShow
  ) {
    let newScrollTop = 0
    if (this.contentScrollTopPositions[idOfContentToShow]) {
      newScrollTop = this.contentScrollTopPositions[idOfContentToShow]
    }
    const contentParentContainer = document.getElementById(
      this.paneModel.contents[idOfContentToHide].htmlElementId
    ).parentNode
    this.contentScrollTopPositions[idOfContentToHide] =
      contentParentContainer.scrollTop
    setTimeout(
      'document.getElementById("' +
        contentParentContainer.id +
        '").scrollTop=' +
        newScrollTop,
      20
    )
  },
  __addContentDivs: function (onCompleteJsCode) {
    const contents = this.paneModel.getContents()
    for (let no = 0; no < contents.length; no++) {
      this.__addOneContentDiv(this.paneModel.contents[no], onCompleteJsCode)
    }
    this.__updatePaneView(this.activeContentIndex)
  },
  __addOneContentDiv: function (contentObj, onCompleteJsCode) {
    const htmlElementId = contentObj.htmlElementId
    const contentUrl = contentObj.contentUrl
    const refreshAfterSeconds = contentObj.refreshAfterSeconds
    if (htmlElementId) {
      try {
        this.contentDiv.appendChild(document.getElementById(htmlElementId))
        document.getElementById(htmlElementId).className =
          'DHTMLSuite_paneContentInner'
        document.getElementById(htmlElementId).style.display = 'none'
      } catch (e) {}
    }
    if (contentUrl) {
      if (
        !contentObj.htmlElementId ||
        htmlElementId.indexOf('dynamicCreatedDiv__') == -1
      ) {
        if (!document.getElementById(htmlElementId)) {
          this.__createAContentDivDynamically(contentObj)
          this.loadContent(
            contentObj.id,
            contentUrl,
            refreshAfterSeconds,
            false,
            onCompleteJsCode
          )
        }
      }
    }
  },
  __createAContentDivDynamically: function (contentObj) {
    const d = new Date()
    let divId =
      'dynamicCreatedDiv__' +
      d.getSeconds() +
      (Math.random() + '').replace('.', '')
    if (!document.getElementById(contentObj.id)) divId = contentObj.id
    contentObj.__setIdOfContentElement(divId)
    const div = document.createElement('DIV')
    div.id = divId
    div.className = 'DHTMLSuite_paneContentInner'
    if (contentObj.contentUrl) div.innerHTML = this.parentRef.waitMessage
    this.contentDiv.appendChild(div)
    div.style.display = 'none'
  },
  __updatePaneView: function (index) {
    if (!index && index !== 0) index = this.activeContentIndex
    this.__updateTabContent()
    this.__updateView()
    this.__updateHeaderBar(index)
    this.__showHideContentDiv(index)
    this.__updateTabView(index)
    this.activeContentIndex = index
  },
  __showHideContentDiv: function (index) {
    if (index !== false) {
      var htmlElementId =
        this.paneModel.contents[this.activeContentIndex].htmlElementId
      try {
        document.getElementById(htmlElementId).style.display = 'none'
      } catch (e) {}
      var htmlElementId = this.paneModel.contents[index].htmlElementId
      if (htmlElementId) {
        try {
          document.getElementById(htmlElementId).style.display = 'block'
        } catch (e) {}
      }
    }
  },
  __setSize: function (recursive) {
    const pos = this.paneModel.getPosition().toLowerCase()
    if (pos == 'west' || pos == 'east') {
      this.divElement.style.width = this.paneModel.size + 'px'
    }
    if (pos == 'north' || pos == 'south') {
      this.divElement.style.height = this.paneModel.size + 'px'
      this.divElement.style.width = '100%'
    }
    try {
      this.contentDiv.style.height =
        this.divElement.clientHeight -
        this.tabDiv.offsetHeight -
        this.headerDiv.offsetHeight +
        'px'
    } catch (e) {}
    if (!recursive) {
      window.obj = this
      setTimeout('window.obj.__setSize(true)', 100)
    }
  },
  __setTopPosition: function (newTop) {
    this.divElement.style.top = newTop + 'px'
  },
  __setLeftPosition: function (newLeft) {
    this.divElement.style.left = newLeft + 'px'
  },
  __setWidth: function (newWidth) {
    if (
      this.paneModel.getPosition() == 'west' ||
      this.paneModel.getPosition() == 'east'
    ) {
      this.paneModel.setSize(newWidth)
    }
    newWidth = newWidth + ''
    if (newWidth.indexOf('%') == -1) newWidth = Math.max(1, newWidth) + 'px'
    this.divElement.style.width = newWidth
  },
  __setHeight: function (newHeight) {
    if (
      this.paneModel.getPosition() == 'north' ||
      this.paneModel.getPosition() == 'south'
    ) {
      this.paneModel.setSize(newHeight)
    }
    this.divElement.style.height = Math.max(1, newHeight) + 'px'
    this.__setSize()
  },
  __showTransparentDivForResize: function () {
    this.divTransparentForResize.style.display = 'block'
    const ref = this.divTransparentForResize
    ref.style.height = this.contentDiv.clientHeight + 'px'
    ref.style.width = this.contentDiv.clientWidth + 'px'
  },
  __hideTransparentDivForResize: function () {
    this.divTransparentForResize.style.display = 'none'
  }
}
DHTMLSuite.paneSplitter = function () {
  let dataModel
  let panes
  let panesAssociative
  let paneContent
  let layoutCSS
  let horizontalSplitterSize
  let horizontalSplitterBorderSize
  let verticalSplitterSize
  let paneSplitterHandles
  let paneSplitterHandleOnResize
  let resizeInProgress
  let resizeCounter
  let currentResize
  let currentResize_min
  let currentResize_max
  let paneSizeCollapsed
  let paneBorderLeftPlusRight
  let slideSpeed
  let waitMessage
  let collapseExpandButtons
  let paneZIndexCounter
  this.collapseExpandButtons = new Object()
  this.resizeCounter = -1
  this.horizontalSplitterSize = 6
  this.verticalSplitterSize = 6
  this.paneBorderLeftPlusRight = 2
  this.slideSpeed = 10
  this.horizontalSplitterBorderSize = 1
  this.resizeInProgress = false
  this.paneSplitterHandleOnResize = false
  this.paneSizeCollapsed = 18
  this.paneSplitterHandles = new Object()
  this.dataModel = false
  this.layoutCSS = 'pane-splitter.css'
  this.waitMessage = 'Loading content-please wait'
  this.panes = new Array()
  this.panesAssociative = new Object()
  this.paneZIndexCounter = 1
  try {
    if (!standardObjectsCreated) DHTMLSuite.createStandardObjects()
  } catch (e) {
    alert('Include the dhtmlSuite-common.js file')
  }
  let objectIndex
  this.objectIndex = DHTMLSuite.variableStorage.arrayDSObjects.length
  DHTMLSuite.variableStorage.arrayDSObjects[this.objectIndex] = this
}
DHTMLSuite.paneSplitter.prototype = {
  addModel: function (newModel) {
    this.dataModel = newModel
  },
  setLayoutCss: function (layoutCSS) {
    this.layoutCSS = layoutCSS
  },
  setAjaxWaitMessage: function (newWaitMessage) {
    this.waitMessage = newWaitMessage
  },
  setSizeOfPane: function (panePosition, newSize) {
    if (!this.panesAssociative[panePosition.toLowerCase()]) return
    if (panePosition == 'east' || panePosition == 'west') {
      this.panesAssociative[panePosition.toLowerCase()].__setWidth(newSize)
    }
    if (panePosition == 'north' || panePosition == 'south') {
      this.panesAssociative[panePosition.toLowerCase()].__setHeight(newSize)
    }
    this.__positionPanes()
  },
  setSlideSpeed: function (slideSpeed) {
    this.slideSpeed = slideSpeed
  },
  init: function () {
    DHTMLSuite.commonObj.loadCSS(this.layoutCSS)
    if (this.dataModel.collapseButtonsInTitleBars) this.paneSizeCollapsed = 25
    this.__createPanes()
    this.__positionPanes()
    this.__createResizeHandles()
    this.__addEvents()
    this.__initCollapsePanes()
    setTimeout(
      'DHTMLSuite.variableStorage.arrayDSObjects[' +
        this.objectIndex +
        '].__positionPanes();',
      100
    )
    setTimeout(
      'DHTMLSuite.variableStorage.arrayDSObjects[' +
        this.objectIndex +
        '].__positionPanes();',
      500
    )
    setTimeout(
      'DHTMLSuite.variableStorage.arrayDSObjects[' +
        this.objectIndex +
        '].__positionPanes();',
      1500
    )
  },
  isUrlLoadedInPane: function (id, url) {
    const ref = this.__getPaneReferenceFromContentId(id)
    if (ref) {
      return ref.isUrlLoadedInPane(id, url)
    } else return false
  },
  loadContent: function (id, url, refreshAfterSeconds, onCompleteJsCode) {
    const ref = this.__getPaneReferenceFromContentId(id)
    if (ref) {
      ref.loadContent(id, url, refreshAfterSeconds, false, onCompleteJsCode)
    }
  },
  reloadContent: function (id) {
    const ref = this.__getPaneReferenceFromContentId(id)
    if (ref) {
      ref.reloadContent(id)
    }
  },
  setRefreshAfterSeconds: function (idOfContentObject, refreshAfterSeconds) {
    const ref = this.__getPaneReferenceFromContentId(idOfContentObject)
    if (ref) {
      ref.setRefreshAfterSeconds(idOfContentObject, refreshAfterSeconds)
    }
  },
  setContentTabTitle: function (idOfContentObject, newTitle) {
    const ref = this.__getPaneReferenceFromContentId(idOfContentObject)
    if (ref) ref.setContentTabTitle(idOfContentObject, newTitle)
  },
  setContentTitle: function (idOfContentObject, newTitle) {
    const ref = this.__getPaneReferenceFromContentId(idOfContentObject)
    if (ref) ref.setContentTitle(idOfContentObject, newTitle)
  },
  showContent: function (id) {
    const ref = this.__getPaneReferenceFromContentId(id)
    if (ref) ref.showContent(id)
  },
  closeAllClosableTabs: function (panePosition) {
    if (this.panesAssociative[panePosition.toLowerCase()]) {
      return this.panesAssociative[
        panePosition.toLowerCase()
      ].__closeAllClosableTabs()
    } else return false
  },
  addContent: function (panePosition, contentModel, onCompleteJsCode) {
    if (this.panesAssociative[panePosition.toLowerCase()]) {
      return this.panesAssociative[panePosition.toLowerCase()].addContent(
        contentModel,
        onCompleteJsCode
      )
    } else return false
  },
  getState: function (panePosition) {
    if (this.panesAssociative[panePosition.toLowerCase()]) {
      return this.panesAssociative[
        panePosition.toLowerCase()
      ].paneModel.__getState()
    }
  },
  deleteContentById: function (id) {
    const ref = this.__getPaneReferenceFromContentId(id)
    if (ref) ref.__deleteContentById(id)
  },
  deleteContentByIndex: function (panePosition, contentIndex) {
    if (this.panesAssociative[panePosition]) {
      this.panesAssociative[panePosition].__deleteContentByIndex(contentIndex)
    }
  },
  hidePane: function (panePosition) {
    if (this.panesAssociative[panePosition] && panePosition != 'center') {
      this.panesAssociative[panePosition].hidePane()
      if (this.paneSplitterHandles[panePosition]) {
        this.paneSplitterHandles[panePosition].style.display = 'none'
      }
      this.__positionPanes()
    } else return false
  },
  showPane: function (panePosition) {
    if (this.panesAssociative[panePosition] && panePosition != 'center') {
      this.panesAssociative[panePosition].showPane()
      if (this.paneSplitterHandles[panePosition]) {
        this.paneSplitterHandles[panePosition].style.display = 'block'
      }
      this.__positionPanes()
    } else return false
  },
  getReferenceToMainDivElOfPane: function (panePosition) {
    if (this.panesAssociative[panePosition]) {
      return this.panesAssociative[panePosition].__getReferenceTomainDivEl()
    }
    return false
  },
  getIdOfCurrentlyDisplayedContent: function (panePosition) {
    if (this.panesAssociative[panePosition]) {
      return this.panesAssociative[
        panePosition
      ].getIdOfCurrentlyDisplayedContent()
    }
    return false
  },
  getHtmlElIdOfCurrentlyDisplayedContent: function (panePosition) {
    if (this.panesAssociative[panePosition]) {
      return this.panesAssociative[
        panePosition
      ].getHtmlElIdOfCurrentlyDisplayedContent()
    }
    return false
  },
  getSizeOfPaneInPixels: function (panePosition) {
    if (this.panesAssociative[panePosition]) {
      return this.panesAssociative[panePosition].__getSizeOfPaneInPixels()
    }
    return false
  },
  expandPane: function (panePosition) {
    if (panePosition == 'center') return
    if (this.panesAssociative[panePosition]) {
      this.panesAssociative[panePosition].__expand()
    }
    if (!this.dataModel.collapseButtonsInTitleBars) {
      this.__toggleCollapseExpandButton(panePosition, 'expand')
    }
  },
  collapsePane: function (panePosition) {
    if (panePosition == 'center') return
    if (this.panesAssociative[panePosition]) {
      this.panesAssociative[panePosition].__collapse()
    }
    if (!this.dataModel.collapseButtonsInTitleBars) {
      this.__toggleCollapseExpandButton(panePosition, 'collapse')
    }
  },
  __setPaneSizeCollapsed: function (newSize) {},
  __createPanes: function () {
    const dataObjects = this.dataModel.getItems()
    for (let no = 0; no < dataObjects.length; no++) {
      const index = this.panes.length
      this.panes[index] = new DHTMLSuite.paneSplitterPane(this)
      this.panes[index].addDataSource(dataObjects[no])
      this.panes[index].__createPane()
      this.panesAssociative[dataObjects[no].position.toLowerCase()] =
        this.panes[index]
    }
  },
  __collapseAPane: function (e, panePosition) {
    const ind = this.objectIndex
    if (document.all) e = event
    const src = DHTMLSuite.commonObj.getSrcElement(e)
    src.className = src.className.replace(' DHTMLSuite_collapseExpandOver', '')
    this.__toggleCollapseExpandButton(panePosition, 'collapse')
    if (this.panesAssociative[panePosition]) {
      this.panesAssociative[panePosition].__collapse()
    }
    src.onclick = function (e) {
      return DHTMLSuite.variableStorage.arrayDSObjects[ind].__expandAPane(
        e,
        panePosition
      )
    }
  },
  __toggleCollapseExpandButton: function (panePosition, state) {
    const src = this.collapseExpandButtons[panePosition]
    const ind = this.objectIndex
    if (state == 'expand') {
      switch (panePosition) {
        case 'east':
          src.className = src.className.replace('Left', 'Right')
          src.parentNode.className = src.parentNode.className.replace(
            ' DHTMLSuite_paneSplitter_vertical_noresize',
            ''
          )
          break
        case 'west':
          src.className = src.className.replace('Right', 'Left')
          src.parentNode.className = src.parentNode.className.replace(
            ' DHTMLSuite_paneSplitter_vertical_noresize',
            ''
          )
          break
        case 'south':
          src.className = src.className.replace('Up', 'Down')
          src.parentNode.className = src.parentNode.className.replace(
            ' DHTMLSuite_paneSplitter_horizontal_noresize',
            ''
          )
          break
        case 'north':
          src.className = src.className.replace('Down', 'Up')
          src.parentNode.className = src.parentNode.className.replace(
            ' DHTMLSuite_paneSplitter_horizontal_noresize',
            ''
          )
          break
      }
      src.onclick = function (e) {
        return DHTMLSuite.variableStorage.arrayDSObjects[ind].__collapseAPane(
          e,
          panePosition
        )
      }
    }
    if (state == 'collapse') {
      switch (panePosition) {
        case 'west':
          src.className = src.className.replace('Left', 'Right')
          src.parentNode.className =
            src.parentNode.className +
            ' DHTMLSuite_paneSplitter_vertical_noresize'
          break
        case 'east':
          src.className = src.className.replace('Right', 'Left')
          src.parentNode.className =
            src.parentNode.className +
            ' DHTMLSuite_paneSplitter_vertical_noresize'
          break
        case 'north':
          src.className = src.className.replace('Up', 'Down')
          src.parentNode.className =
            src.parentNode.className +
            ' DHTMLSuite_paneSplitter_horizontal_noresize'
          break
        case 'south':
          src.className = src.className.replace('Down', 'Up')
          src.parentNode.className =
            src.parentNode.className +
            ' DHTMLSuite_paneSplitter_horizontal_noresize'
          break
      }
      src.onclick = function (e) {
        return DHTMLSuite.variableStorage.arrayDSObjects[ind].__expandAPane(
          e,
          panePosition
        )
      }
    }
  },
  __expandAPane: function (e, panePosition) {
    const ind = this.objectIndex
    if (document.all) e = event
    const src = DHTMLSuite.commonObj.getSrcElement(e)
    src.className = src.className.replace(' DHTMLSuite_collapseExpandOver', '')
    this.__toggleCollapseExpandButton(panePosition, 'expand')
    if (this.panesAssociative[panePosition]) {
      this.panesAssociative[panePosition].__expand()
    }
    src.onclick = function (e) {
      return DHTMLSuite.variableStorage.arrayDSObjects[ind].__collapseAPane(
        e,
        panePosition
      )
    }
  },
  __createResizeHandles: function () {
    const ind = this.objectIndex
    if (
      this.panesAssociative.north &&
      (this.panesAssociative.north.paneModel.resizable ||
        (this.panesAssociative.north.paneModel.collapsable &&
          !this.dataModel.collapseButtonsInTitleBars))
    ) {
      this.paneSplitterHandles.north = document.createElement('DIV')
      var obj = this.paneSplitterHandles.north
      obj.className = 'DHTMLSuite_paneSplitter_horizontal'
      obj.innerHTML = '<span></span>'
      obj.style.position = 'absolute'
      obj.style.height = this.horizontalSplitterSize + 'px'
      obj.style.width = '100%'
      obj.style.zIndex = 10000
      obj.setAttribute('resizeHandle', '1')
      DHTMLSuite.commonObj.addEvent(obj, 'mousedown', function (e) {
        DHTMLSuite.variableStorage.arrayDSObjects[ind].__initResizePane(
          e,
          'north'
        )
      })
      document.body.appendChild(obj)
      if (!this.dataModel.collapseButtonsInTitleBars) {
        var subElement = document.createElement('DIV')
        subElement.className = 'DHTMLSuite_resizeButtonUp'
        subElement.onclick = function (e) {
          return DHTMLSuite.variableStorage.arrayDSObjects[ind].__collapseAPane(
            e,
            'north'
          )
        }
        subElement.onmouseover = this.__mouseoverCollapseButton
        subElement.onmouseout = this.__mouseoutCollapseButton
        subElement.innerHTML = '<span></span>'
        DHTMLSuite.commonObj.__addEventEl(subElement)
        obj.appendChild(subElement)
        this.collapseExpandButtons.north = subElement
        if (this.panesAssociative.north.paneModel.state == 'collapsed') {
          this.__toggleCollapseExpandButton('north', 'collapse')
        }
        if (!this.panesAssociative.north.paneModel.collapsable) {
          subElement.style.display = 'none'
          obj.className =
            obj.className + ' DHTMLSuite_paneSplitter_horizontal_expInTitle'
        }
      } else {
        obj.className =
          obj.className + ' DHTMLSuite_paneSplitter_horizontal_expInTitle'
      }
      if (!this.panesAssociative.north.paneModel.resizable) {
        obj.className =
          obj.className + ' DHTMLSuite_paneSplitter_horizontal_noresize'
      }
    }
    if (this.panesAssociative.west) {
      this.paneSplitterHandles.west = document.createElement('DIV')
      var obj = this.paneSplitterHandles.west
      obj.innerHTML = '<span></span>'
      obj.className = 'DHTMLSuite_paneSplitter_vertical'
      obj.style.position = 'absolute'
      obj.style.width = this.verticalSplitterSize + 'px'
      obj.style.zIndex = 11000
      obj.setAttribute('resizeHandle', '1')
      DHTMLSuite.commonObj.addEvent(obj, 'mousedown', function (e) {
        DHTMLSuite.variableStorage.arrayDSObjects[ind].__initResizePane(
          e,
          'west'
        )
      })
      document.body.appendChild(obj)
      if (!this.dataModel.collapseButtonsInTitleBars) {
        var subElement = document.createElement('DIV')
        subElement.className = 'DHTMLSuite_resizeButtonLeft'
        subElement.onclick = function (e) {
          return DHTMLSuite.variableStorage.arrayDSObjects[ind].__collapseAPane(
            e,
            'west'
          )
        }
        DHTMLSuite.commonObj.__addEventEl(subElement)
        subElement.onmouseover = this.__mouseoverCollapseButton
        subElement.onmouseout = this.__mouseoutCollapseButton
        subElement.innerHTML = '<span></span>'
        obj.appendChild(subElement)
        this.collapseExpandButtons.west = subElement
        if (this.panesAssociative.west.paneModel.state == 'collapsed') {
          this.__toggleCollapseExpandButton('west', 'collapse')
        }
        if (!this.panesAssociative.west.paneModel.collapsable) {
          subElement.style.display = 'none'
          obj.className =
            obj.className + ' DHTMLSuite_paneSplitter_vertical_expInTitle'
        }
      } else {
        obj.className =
          obj.className + ' DHTMLSuite_paneSplitter_vertical_expInTitle'
      }
      if (!this.panesAssociative.west.paneModel.resizable) {
        obj.className =
          obj.className + ' DHTMLSuite_paneSplitter_vertical_noresize'
      }
    }
    if (this.panesAssociative.east) {
      this.paneSplitterHandles.east = document.createElement('DIV')
      var obj = this.paneSplitterHandles.east
      obj.innerHTML = '<span></span>'
      obj.className = 'DHTMLSuite_paneSplitter_vertical'
      obj.style.position = 'absolute'
      obj.style.width = this.verticalSplitterSize + 'px'
      obj.style.zIndex = 11000
      obj.setAttribute('resizeHandle', '1')
      DHTMLSuite.commonObj.addEvent(obj, 'mousedown', function (e) {
        DHTMLSuite.variableStorage.arrayDSObjects[ind].__initResizePane(
          e,
          'east'
        )
      })
      document.body.appendChild(obj)
      if (!this.dataModel.collapseButtonsInTitleBars) {
        var subElement = document.createElement('DIV')
        subElement.className = 'DHTMLSuite_resizeButtonRight'
        subElement.onclick = function (e) {
          return DHTMLSuite.variableStorage.arrayDSObjects[ind].__collapseAPane(
            e,
            'east'
          )
        }
        subElement.onmouseover = this.__mouseoverCollapseButton
        subElement.onmouseout = this.__mouseoutCollapseButton
        subElement.innerHTML = '<span></span>'
        DHTMLSuite.commonObj.__addEventEl(subElement)
        obj.appendChild(subElement)
        this.collapseExpandButtons.east = subElement
        if (this.panesAssociative.east.paneModel.state == 'collapsed') {
          this.__toggleCollapseExpandButton('east', 'collapse')
        }
        if (!this.panesAssociative.east.paneModel.collapsable) {
          subElement.style.display = 'none'
          obj.className =
            obj.className + ' DHTMLSuite_paneSplitter_vertical_expInTitle'
        }
      } else {
        obj.className =
          obj.className + ' DHTMLSuite_paneSplitter_vertical_expInTitle'
      }
      if (!this.panesAssociative.east.paneModel.resizable) {
        obj.className =
          obj.className + ' DHTMLSuite_paneSplitter_vertical_noresize'
      }
    }
    if (
      this.panesAssociative.south &&
      (this.panesAssociative.south.paneModel.resizable ||
        (this.panesAssociative.south.paneModel.collapsable &&
          !this.dataModel.collapseButtonsInTitleBars))
    ) {
      this.paneSplitterHandles.south = document.createElement('DIV')
      var obj = this.paneSplitterHandles.south
      obj.innerHTML = '<span></span>'
      obj.className = 'DHTMLSuite_paneSplitter_horizontal'
      obj.style.position = 'absolute'
      obj.style.height = this.horizontalSplitterSize + 'px'
      obj.style.width = '100%'
      obj.setAttribute('resizeHandle', '1')
      obj.style.zIndex = 10000
      DHTMLSuite.commonObj.addEvent(obj, 'mousedown', function (e) {
        DHTMLSuite.variableStorage.arrayDSObjects[ind].__initResizePane(
          e,
          'south'
        )
      })
      document.body.appendChild(obj)
      if (!this.dataModel.collapseButtonsInTitleBars) {
        var subElement = document.createElement('DIV')
        subElement.style.position = 'absolute'
        subElement.className = 'DHTMLSuite_resizeButtonDown'
        subElement.onclick = function (e) {
          return DHTMLSuite.variableStorage.arrayDSObjects[ind].__collapseAPane(
            e,
            'south'
          )
        }
        subElement.onmouseover = this.__mouseoverCollapseButton
        subElement.onmouseout = this.__mouseoutCollapseButton
        subElement.innerHTML = '<span></span>'
        DHTMLSuite.commonObj.__addEventEl(subElement)
        obj.appendChild(subElement)
        this.collapseExpandButtons.south = subElement
        if (this.panesAssociative.south.paneModel.state == 'collapsed') {
          this.__toggleCollapseExpandButton('south', 'collapse')
        }
        if (!this.panesAssociative.south.paneModel.collapsable) {
          subElement.style.display = 'none'
          obj.className =
            obj.className + ' DHTMLSuite_paneSplitter_horizontal_expInTitle'
        }
      } else {
        obj.className =
          obj.className + ' DHTMLSuite_paneSplitter_horizontal_expInTitle'
      }
      if (!this.panesAssociative.south.paneModel.resizable) {
        obj.className =
          obj.className + ' DHTMLSuite_paneSplitter_vertical_noresize'
      }
    }
    this.paneSplitterHandleOnResize = document.createElement('DIV')
    var obj = this.paneSplitterHandleOnResize
    obj.innerHTML = '<span></span>'
    obj.className = 'DHTMLSuite_paneSplitter_onResize'
    obj.style.position = 'absolute'
    obj.style.zIndex = 955000
    obj.style.display = 'none'
    document.body.appendChild(obj)
  },
  __mouseoverCollapseButton: function () {
    this.className = this.className + ' DHTMLSuite_collapseExpandOver'
  },
  __mouseoutCollapseButton: function () {
    this.className = this.className.replace(
      ' DHTMLSuite_collapseExpandOver',
      ''
    )
  },
  __getPaneReferenceFromContentId: function (id) {
    for (let no = 0; no < this.panes.length; no++) {
      const contents = this.panes[no].paneModel.getContents()
      for (let no2 = 0; no2 < contents.length; no2++) {
        if (contents[no2].id == id) return this.panes[no]
      }
    }
    return false
  },
  __initCollapsePanes: function () {
    for (let no = 0; no < this.panes.length; no++) {
      if (this.panes[no].paneModel.state == 'collapsed') {
        this.panes[no].__collapse()
      }
    }
  },
  __getMinimumPos: function (pos) {
    const browserWidth = DHTMLSuite.clientInfoObj.getBrowserWidth()
    const browserHeight = DHTMLSuite.clientInfoObj.getBrowserHeight()
    if (pos == 'west' || pos == 'north') {
      return this.panesAssociative[pos].paneModel.minSize
    } else {
      if (pos == 'east') {
        return browserWidth - this.panesAssociative[pos].paneModel.maxSize
      }
      if (pos == 'south') {
        return browserHeight - this.panesAssociative[pos].paneModel.maxSize
      }
    }
  },
  __getMaximumPos: function (pos) {
    const browserWidth = DHTMLSuite.clientInfoObj.getBrowserWidth()
    const browserHeight = DHTMLSuite.clientInfoObj.getBrowserHeight()
    if (pos == 'west' || pos == 'north') {
      return this.panesAssociative[pos].paneModel.maxSize
    } else {
      if (pos == 'east') {
        return browserWidth - this.panesAssociative[pos].paneModel.minSize
      }
      if (pos == 'south') {
        return browserHeight - this.panesAssociative[pos].paneModel.minSize
      }
    }
  },
  __initResizePane: function (e, pos) {
    if (document.all) e = event
    const obj = DHTMLSuite.commonObj.getSrcElement(e)
    let attr = obj.getAttribute('resizeHandle')
    if (!attr) attr = obj.resizeHandle
    if (!attr) return
    if (obj.className.indexOf('noresize') >= 0) return
    this.currentResize = pos
    this.currentResize_min = this.__getMinimumPos(pos)
    this.currentResize_max = this.__getMaximumPos(pos)
    this.paneSplitterHandleOnResize.style.left =
      this.paneSplitterHandles[pos].style.left
    this.paneSplitterHandleOnResize.style.top =
      this.paneSplitterHandles[pos].style.top
    this.paneSplitterHandleOnResize.style.width =
      this.paneSplitterHandles[pos].offsetWidth + 'px'
    this.paneSplitterHandleOnResize.style.height =
      this.paneSplitterHandles[pos].offsetHeight + 'px'
    this.paneSplitterHandleOnResize.style.display = 'block'
    this.resizeCounter = 0
    DHTMLSuite.commonObj.__setTextSelOk(false)
    this.__timerResizePane(pos)
  },
  __timerResizePane: function (pos) {
    if (this.resizeCounter >= 0 && this.resizeCounter < 5) {
      this.resizeCounter++
      setTimeout(
        'DHTMLSuite.variableStorage.arrayDSObjects[' +
          this.objectIndex +
          '].__timerResizePane()',
        2
      )
      return
    }
    if (this.resizeCounter == 5) {
      this.__showTransparentDivForResize('show')
    }
  },
  __showTransparentDivForResize: function () {
    if (DHTMLSuite.clientInfoObj.isOpera) return
    if (this.panesAssociative.west) {
      this.panesAssociative.west.__showTransparentDivForResize()
    }
    if (this.panesAssociative.south) {
      this.panesAssociative.south.__showTransparentDivForResize()
    }
    if (this.panesAssociative.east) {
      this.panesAssociative.east.__showTransparentDivForResize()
    }
    if (this.panesAssociative.north) {
      this.panesAssociative.north.__showTransparentDivForResize()
    }
    if (this.panesAssociative.center) {
      this.panesAssociative.center.__showTransparentDivForResize()
    }
  },
  __hideTransparentDivForResize: function () {
    if (this.panesAssociative.west) {
      this.panesAssociative.west.__hideTransparentDivForResize()
    }
    if (this.panesAssociative.south) {
      this.panesAssociative.south.__hideTransparentDivForResize()
    }
    if (this.panesAssociative.east) {
      this.panesAssociative.east.__hideTransparentDivForResize()
    }
    if (this.panesAssociative.north) {
      this.panesAssociative.north.__hideTransparentDivForResize()
    }
    if (this.panesAssociative.center) {
      this.panesAssociative.center.__hideTransparentDivForResize()
    }
  },
  __resizePane: function (e) {
    if (document.all) e = event
    if (DHTMLSuite.clientInfoObj.isMSIE && e.button != 1) this.__endResize()
    if (this.resizeCounter == 5) {
      if (this.currentResize == 'west' || this.currentResize == 'east') {
        let leftPos = e.clientX
        if (leftPos < this.currentResize_min) leftPos = this.currentResize_min
        if (leftPos > this.currentResize_max) leftPos = this.currentResize_max
        this.paneSplitterHandleOnResize.style.left = leftPos + 'px'
      } else {
        let topPos = e.clientY
        if (topPos < this.currentResize_min) topPos = this.currentResize_min
        if (topPos > this.currentResize_max) topPos = this.currentResize_max
        this.paneSplitterHandleOnResize.style.top = topPos + 'px'
      }
    }
  },
  __endResize: function () {
    if (this.resizeCounter == 5) {
      this.__hideTransparentDivForResize()
      const browserWidth = DHTMLSuite.clientInfoObj.getBrowserWidth()
      const browserHeight = DHTMLSuite.clientInfoObj.getBrowserHeight()
      const obj = this.panesAssociative[this.currentResize]
      switch (this.currentResize) {
        case 'west':
          obj.__setWidth(
            this.paneSplitterHandleOnResize.style.left.replace('px', '') / 1 - 2
          )
          break
        case 'north':
          obj.__setHeight(
            this.paneSplitterHandleOnResize.style.top.replace('px', '') / 1
          )
          break
        case 'east':
          obj.__setWidth(
            browserWidth -
              this.paneSplitterHandleOnResize.style.left.replace('px', '') / 1 -
              8
          )
          break
        case 'south':
          obj.__setHeight(
            browserHeight -
              this.paneSplitterHandleOnResize.style.top.replace('px', '') / 1 -
              7
          )
          break
      }
      this.__positionPanes()
      obj.__executeResizeCallBack()
      this.paneSplitterHandleOnResize.style.display = 'none'
      this.resizeCounter = -1
      DHTMLSuite.commonObj.__setTextSelOk(true)
    }
  },
  __hideResizeHandle: function (pos) {
    if (!this.paneSplitterHandles[pos]) return
    switch (pos) {
      case 'east':
      case 'west':
        this.paneSplitterHandles[pos].className =
          this.paneSplitterHandles[pos].className +
          ' DHTMLSuite_paneSplitter_vertical_noresize'
        break
      case 'north':
      case 'south':
        this.paneSplitterHandles[pos].className =
          this.paneSplitterHandles[pos].className +
          ' DHTMLSuite_paneSplitter_horizontal_noresize'
    }
  },
  __showResizeHandle: function (pos) {
    if (!this.paneSplitterHandles[pos]) return
    switch (pos) {
      case 'east':
      case 'west':
        this.paneSplitterHandles[pos].className = this.paneSplitterHandles[
          pos
        ].className.replace(' DHTMLSuite_paneSplitter_vertical_noresize', '')
        break
      case 'north':
      case 'south':
        this.paneSplitterHandles[pos].className = this.paneSplitterHandles[
          pos
        ].className.replace(' DHTMLSuite_paneSplitter_horizontal_noresize', '')
    }
  },
  __positionResizeHandles: function () {
    if (this.paneSplitterHandles.north) {
      if (this.panesAssociative.north.paneModel.state == 'expanded') {
        this.paneSplitterHandles.north.style.top =
          this.panesAssociative.north.divElement.style.height.replace(
            'px',
            ''
          ) + 'px'
      } else {
        this.paneSplitterHandles.north.style.top =
          this.paneSizeCollapsed + 'px'
      }
    }
    let heightHandler =
      this.panesAssociative.center.divElement.offsetHeight + 1
    var topPos = 0
    if (this.panesAssociative.center) {
      topPos +=
        this.panesAssociative.center.divElement.style.top.replace('px', '') / 1
    }
    if (this.paneSplitterHandles.west) {
      if (this.paneSplitterHandles.east) {
        heightHandler += this.horizontalSplitterBorderSize / 2
      }
      if (this.panesAssociative.west.paneModel.state == 'expanded') {
        this.paneSplitterHandles.west.style.left =
          this.panesAssociative.west.divElement.offsetWidth + 'px'
      } else {
        this.paneSplitterHandles.west.style.left =
          this.paneSizeCollapsed + 'px'
      }
      this.paneSplitterHandles.west.style.height = heightHandler + 'px'
      this.paneSplitterHandles.west.style.top = topPos + 'px'
    }
    if (this.paneSplitterHandles.east) {
      const leftPos =
        this.panesAssociative.center.divElement.style.left.replace('px', '') /
          1 +
        this.panesAssociative.center.divElement.offsetWidth
      this.paneSplitterHandles.east.style.left = leftPos + 'px'
      this.paneSplitterHandles.east.style.height = heightHandler + 'px'
      this.paneSplitterHandles.east.style.top = topPos + 'px'
    }
    if (this.paneSplitterHandles.south) {
      var topPos =
        this.panesAssociative.south.divElement.style.top.replace('px', '') / 1
      topPos =
        topPos -
        this.horizontalSplitterSize -
        this.horizontalSplitterBorderSize
      this.paneSplitterHandles.south.style.top = topPos + 'px'
    }
    this.resizeInProgress = false
  },
  __positionPanes: function () {
    if (this.resizeInProgress) return
    const ind = this.objectIndex
    this.resizeInProgress = true
    const browserWidth = DHTMLSuite.clientInfoObj.getBrowserWidth()
    const browserHeight = DHTMLSuite.clientInfoObj.getBrowserHeight()
    let posTopMiddlePanes = 0
    if (
      this.panesAssociative.north &&
      this.panesAssociative.north.paneModel.visible
    ) {
      if (this.panesAssociative.north.paneModel.state == 'expanded') {
        posTopMiddlePanes = this.panesAssociative.north.divElement.offsetHeight
        this.panesAssociative.north.__setHeight(
          this.panesAssociative.north.divElement.offsetHeight
        )
      } else {
        posTopMiddlePanes += this.paneSizeCollapsed
      }
      if (this.paneSplitterHandles.north) {
        posTopMiddlePanes +=
          this.horizontalSplitterSize + this.horizontalSplitterBorderSize
      }
    }
    if (this.panesAssociative.center) {
      this.panesAssociative.center.__setTopPosition(posTopMiddlePanes)
    }
    if (this.panesAssociative.west) {
      this.panesAssociative.west.__setTopPosition(posTopMiddlePanes)
    }
    if (this.panesAssociative.east) {
      this.panesAssociative.east.__setTopPosition(posTopMiddlePanes)
    }
    if (this.panesAssociative.west) {
      this.panesAssociative.west.divElCollapsed.style.top =
        posTopMiddlePanes + 'px'
    }
    if (this.panesAssociative.east) {
      this.panesAssociative.east.divElCollapsed.style.top =
        posTopMiddlePanes + 'px'
    }
    let posLeftCenterPane = 0
    if (this.panesAssociative.west) {
      if (this.panesAssociative.west.paneModel.state == 'expanded') {
        posLeftCenterPane = this.panesAssociative.west.divElement.offsetWidth
        this.panesAssociative.west.__setLeftPosition(0)
      } else {
        posLeftCenterPane += this.paneSizeCollapsed
      }
      posLeftCenterPane += this.verticalSplitterSize
    }
    this.panesAssociative.center.__setLeftPosition(posLeftCenterPane)
    let sizeCenterPane = browserWidth
    if (
      this.panesAssociative.west &&
      this.panesAssociative.west.paneModel.visible
    ) {
      if (this.panesAssociative.west.paneModel.state == 'expanded') {
        sizeCenterPane -= this.panesAssociative.west.divElement.offsetWidth
      } else sizeCenterPane -= this.paneSizeCollapsed
    }
    if (
      this.panesAssociative.east &&
      this.panesAssociative.east.paneModel.visible
    ) {
      if (this.panesAssociative.east.paneModel.state == 'expanded') {
        sizeCenterPane -= this.panesAssociative.east.divElement.offsetWidth
      } else {
        sizeCenterPane -= this.paneSizeCollapsed
        if (DHTMLSuite.clientInfoObj.isOldMSIE) sizeCenterPane -= 4
      }
    }
    sizeCenterPane -= this.paneBorderLeftPlusRight
    if (this.paneSplitterHandles.west) {
      sizeCenterPane -= this.verticalSplitterSize
    }
    if (this.paneSplitterHandles.east) {
      sizeCenterPane -= this.verticalSplitterSize
    }
    this.panesAssociative.center.__setWidth(sizeCenterPane)
    let posEastPane =
      posLeftCenterPane + this.panesAssociative.center.divElement.offsetWidth
    if (this.paneSplitterHandles.east) {
      posEastPane += this.verticalSplitterSize
    }
    if (this.panesAssociative.east) {
      if (this.panesAssociative.east.paneModel.state == 'expanded') {
        this.panesAssociative.east.__setLeftPosition(posEastPane)
      }
      this.panesAssociative.east.divElCollapsed.style.left = ''
      this.panesAssociative.east.divElCollapsed.style.right = '0px'
    }
    let heightMiddleFrames = browserHeight
    if (
      this.panesAssociative.north &&
      this.panesAssociative.north.paneModel.visible
    ) {
      if (this.panesAssociative.north.paneModel.state == 'expanded') {
        heightMiddleFrames -=
          this.panesAssociative.north.divElement.offsetHeight
      } else heightMiddleFrames -= this.paneSizeCollapsed
      if (this.paneSplitterHandles.north) {
        heightMiddleFrames -=
          this.horizontalSplitterSize + this.horizontalSplitterBorderSize
      }
    }
    if (
      this.panesAssociative.south &&
      this.panesAssociative.south.paneModel.visible
    ) {
      if (this.panesAssociative.south.paneModel.state == 'expanded') {
        heightMiddleFrames -=
          this.panesAssociative.south.divElement.offsetHeight
      } else heightMiddleFrames -= this.paneSizeCollapsed
      if (this.paneSplitterHandles.south) {
        heightMiddleFrames -=
          this.horizontalSplitterSize + this.horizontalSplitterBorderSize
      }
    }
    if (this.panesAssociative.center) {
      this.panesAssociative.center.__setHeight(heightMiddleFrames)
    }
    if (this.panesAssociative.west) {
      this.panesAssociative.west.__setHeight(heightMiddleFrames)
    }
    if (this.panesAssociative.east) {
      this.panesAssociative.east.__setHeight(heightMiddleFrames)
    }
    let posSouth = 0
    if (this.panesAssociative.north) {
      if (this.panesAssociative.north.paneModel.state == 'expanded') {
        posSouth = this.panesAssociative.north.divElement.offsetHeight
      } else posSouth = this.paneSizeCollapsed
    }
    posSouth += heightMiddleFrames
    if (this.paneSplitterHandles.south) {
      posSouth +=
        this.horizontalSplitterSize + this.horizontalSplitterBorderSize
    }
    if (this.paneSplitterHandles.north) {
      posSouth +=
        this.horizontalSplitterSize + this.horizontalSplitterBorderSize
    }
    if (this.panesAssociative.south) {
      this.panesAssociative.south.__setTopPosition(posSouth)
      this.panesAssociative.south.divElCollapsed.style.top = posSouth + 'px'
      this.panesAssociative.south.__setWidth('100%')
    }
    try {
      if (this.panesAssociative.west) {
        this.panesAssociative.west.divElCollapsed.style.height =
          heightMiddleFrames + 'px'
        this.panesAssociative.west.divElCollapsedInner.style.height =
          heightMiddleFrames - 1 + 'px'
      }
    } catch (e) {}
    if (this.panesAssociative.east) {
      try {
        this.panesAssociative.east.divElCollapsed.style.height =
          heightMiddleFrames + 'px'
        this.panesAssociative.east.divElCollapsedInner.style.height =
          heightMiddleFrames - 1 + 'px'
      } catch (e) {}
    }
    if (this.panesAssociative.south) {
      this.panesAssociative.south.divElCollapsed.style.width =
        browserWidth + 'px'
      if (
        this.panesAssociative.south.paneModel.state == 'collapsed' &&
        this.panesAssociative.south.divElCollapsed.offsetHeight
      ) {
        const rest =
          browserHeight -
          this.panesAssociative.south.divElCollapsed.style.top.replace(
            'px',
            ''
          ) /
            1 -
          this.panesAssociative.south.divElCollapsed.offsetHeight
        if (rest > 0) {
          this.panesAssociative.south.divElCollapsed.style.height =
            this.panesAssociative.south.divElCollapsed.offsetHeight +
            rest +
            'px'
        }
      }
    }
    if (this.panesAssociative.north) {
      this.panesAssociative.north.divElCollapsed.style.width =
        browserWidth + 'px'
    }
    this.__positionResizeHandles()
    setTimeout(
      'DHTMLSuite.variableStorage.arrayDSObjects[' +
        ind +
        '].__positionResizeHandles()',
      50
    )
  },
  __autoSlideInPanes: function (e) {
    if (document.all) e = event
    if (this.panesAssociative.south) {
      this.panesAssociative.south.__autoSlideInPane(e)
    }
    if (this.panesAssociative.west) {
      this.panesAssociative.west.__autoSlideInPane(e)
    }
    if (this.panesAssociative.north) {
      this.panesAssociative.north.__autoSlideInPane(e)
    }
    if (this.panesAssociative.east) {
      this.panesAssociative.east.__autoSlideInPane(e)
    }
  },
  __addEvents: function () {
    const ind = this.objectIndex
    DHTMLSuite.commonObj.addEvent(window, 'resize', function () {
      DHTMLSuite.variableStorage.arrayDSObjects[ind].__positionPanes()
    })
    DHTMLSuite.commonObj.addEvent(
      document.documentElement,
      'mouseup',
      function () {
        DHTMLSuite.variableStorage.arrayDSObjects[ind].__endResize()
      }
    )
    DHTMLSuite.commonObj.addEvent(
      document.documentElement,
      'mousemove',
      function (e) {
        DHTMLSuite.variableStorage.arrayDSObjects[ind].__resizePane(e)
      }
    )
    DHTMLSuite.commonObj.addEvent(
      document.documentElement,
      'click',
      function (e) {
        DHTMLSuite.variableStorage.arrayDSObjects[ind].__autoSlideInPanes(e)
      }
    )
    document.documentElement.onselectstart = function () {
      return DHTMLSuite.commonObj.__isTextSelOk()
    }
    DHTMLSuite.commonObj.__addEventEl(window)
  }
}
