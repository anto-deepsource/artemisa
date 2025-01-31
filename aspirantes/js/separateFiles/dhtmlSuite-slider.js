if (!window.DHTMLSuite) var DHTMLSuite = new Object()
DHTMLSuite.sliderObjects = new Array()
DHTMLSuite.indexOfCurrentlyActiveSlider = false
DHTMLSuite.slider_generalMouseEventsAdded = false
DHTMLSuite.slider = function () {
  let width
  let height
  let targetObj
  let sliderWidth
  let sliderDirection
  let functionToCallOnChange
  let layoutCss
  let sliderMaxValue
  let sliderMinValue
  let initialValue
  let sliderSize
  let directionOfPointer
  let slideInProcessTimer
  let indexThisSlider
  let numberOfSteps
  let stepLinesVisibility
  let slide_event_pos
  let slide_start_pos
  let sliderHandleImg
  let sliderName
  let sliderValueReversed
  this.sliderWidth = 9
  this.layoutCss = 'slider.css'
  this.sliderDirection = 'hor'
  this.width = 0
  this.height = 0
  this.sliderMaxValue = 100
  this.sliderMinValue = 0
  this.initialValue = 0
  this.targetObj = false
  this.directionOfPointer = 'up'
  this.slideInProcessTimer = -1
  this.sliderName = ''
  this.numberOfSteps = false
  this.stepLinesVisibility = true
  this.sliderValueReversed = false
  try {
    if (!standardObjectsCreated) DHTMLSuite.createStandardObjects()
  } catch (e) {
    alert('Include the dhtmlSuite-common.js file')
  }
  let objectIndex
  this.objectIndex = DHTMLSuite.variableStorage.arrayDSObjects.length
  DHTMLSuite.variableStorage.arrayDSObjects[this.objectIndex] = this
}
DHTMLSuite.slider.prototype = {
  init: function () {
    if (!this.targetObj) {
      alert('Error!-No target for slider specified')
      return
    }
    this.__setWidthAndHeightDynamically()
    DHTMLSuite.commonObj.loadCSS(this.layoutCss)
    this.__createSlider()
    if (!document.documentElement.onselectstart) {
      document.documentElement.onselectstart = function () {
        return DHTMLSuite.commonObj.__isTextSelOk()
      }
    }
  },
  setSliderTarget: function (targetRef) {
    targetRef = DHTMLSuite.commonObj.getEl(targetRef)
    this.targetObj = targetRef
  },
  setSliderDirection: function (newDirection) {
    newDirection = newDirection + ''
    newDirection = newDirection.toLowerCase()
    if (newDirection != 'hor' && newDirection != 'ver') {
      alert('Invalid slider direction-possible values: "hor" or "ver"')
      return
    }
    this.sliderDirection = newDirection
  },
  setSliderWidth: function (newWidth) {
    newWidth = newWidth + ''
    if (newWidth.indexOf('%') == -1) newWidth = newWidth + 'px'
    this.width = newWidth
  },
  setSliderHeight: function (newHeight) {
    newHeight = newHeight + ''
    if (newHeight.indexOf('%') == -1) newHeight = newHeight + 'px'
    this.height = height
  },
  setSliderReversed: function () {
    this.sliderValueReversed = true
  },
  setOnChangeEvent: function (nameOfFunction) {
    this.functionToCallOnChange = nameOfFunction
  },
  setSliderMaxValue: function (newMaxValue) {
    this.sliderMaxValue = newMaxValue
  },
  setSliderMinValue: function (newMinValue) {
    this.sliderMinValue = newMinValue
  },
  setSliderName: function (nameOfSlider) {
    this.sliderName = nameOfSlider
  },
  setLayoutCss: function (nameOfNewCssFile) {
    this.layoutCss = nameOfNewCssFile
  },
  setInitialValue: function (newInitialValue) {
    this.initialValue = newInitialValue
  },
  setSliderPointerDirection: function (directionOfPointer) {
    this.directionOfPointer = directionOfPointer
  },
  setSliderValue: function (newValue) {
    const position = Math.floor(
      (newValue / this.sliderMaxValue) * this.sliderSize
    )
    if (this.sliderDirection == 'hor') {
      this.sliderHandleImg.style.left = position + 'px'
    } else {
      this.sliderHandleImg.style.top = position + 'px'
    }
  },
  setNumberOfSliderSteps: function (numberOfSteps) {
    this.numberOfSteps = numberOfSteps
  },
  setStepLinesVisible: function (visible) {
    this.stepLinesVisibility = visible
  },
  __setWidthAndHeightDynamically: function () {
    if (!this.width || this.width == 0) {
      this.width = this.targetObj.clientWidth + ''
    }
    if (!this.height || this.height == 0) {
      this.height = this.targetObj.clientHeight + ''
    }
    if (!this.width || this.width == 0) {
      this.width = this.targetObj.offsetWidth + ''
    }
    if (!this.height || this.height == 0) {
      this.height = this.targetObj.offsetHeight + ''
    }
    if (this.width == 0) return
    if (this.height == 0) return
    if (this.width.indexOf('px') == -1 && this.width.indexOf('%') == -1) {
      this.width = this.width + 'px'
    }
    if (this.height.indexOf('px') == -1 && this.height.indexOf('%') == -1) {
      this.height = this.height + 'px'
    }
  },
  __createSlider: function (initWidth) {
    if (this.targetObj.clientWidth == 0 || initWidth == 0) {
      const timeoutTime = 100
      setTimeout(
        'DHTMLSuite.variableStorage.arrayDSObjects[' +
          this.objectIndex +
          '].__createSlider(' +
          this.targetObj.clientWidth +
          ')',
        timeoutTime
      )
      return
    }
    this.__setWidthAndHeightDynamically()
    this.indexThisSlider = DHTMLSuite.sliderObjects.length
    DHTMLSuite.sliderObjects[this.indexThisSlider] = this
    window.refToThisObject = this
    const div = document.createElement('DIV')
    div.style.width = this.width
    div.style.cursor = 'default'
    div.style.height = this.height
    div.style.position = 'relative'
    div.id = 'sliderNumber' + this.indexThisSlider
    div.onmousedown = this.__setPositionFromClick
    DHTMLSuite.commonObj.__addEventEl(div)
    this.targetObj.appendChild(div)
    const sliderObj = document.createElement('DIV')
    if (this.sliderDirection == 'hor') {
      sliderObj.className = 'DHTMLSuite_slider_horizontal'
      sliderObj.style.width = div.clientWidth + 'px'
      this.sliderSize = div.offsetWidth - this.sliderWidth
      var sliderHandle = document.createElement('IMG')
      var srcHandle = 'slider_handle_down.gif'
      sliderHandle.style.bottom = '2px'
      if (this.directionOfPointer == 'up') {
        srcHandle = 'slider_handle_up.gif'
        sliderHandle.style.bottom = '0px'
      }
      sliderHandle.src = DHTMLSuite.configObj.imagePath + 'slider/' + srcHandle
      div.appendChild(sliderHandle)
      let leftPos
      leftPos = this.sliderValueReversed
        ? Math.round(
            ((this.sliderMaxValue - this.initialValue) / this.sliderMaxValue) *
              this.sliderSize
          ) - 1
        : Math.round(
          (this.initialValue / this.sliderMaxValue) * this.sliderSize
        )
      sliderHandle.style.left = leftPos + 'px'
    } else {
      sliderObj.className = 'DHTMLSuite_slider_vertical'
      sliderObj.style.height = div.clientHeight + 'px'
      this.sliderSize = div.clientHeight - this.sliderWidth
      var sliderHandle = document.createElement('IMG')
      var srcHandle = 'slider_handle_right.gif'
      sliderHandle.style.left = '0px'
      if (this.directionOfPointer == 'left') {
        srcHandle = 'slider_handle_left.gif'
        sliderHandle.style.left = '0px'
      }
      sliderHandle.src = DHTMLSuite.configObj.imagePath + 'slider/' + srcHandle
      div.appendChild(sliderHandle)
      let topPos
      topPos = !this.sliderValueReversed
        ? Math.floor(
            ((this.sliderMaxValue - this.initialValue) / this.sliderMaxValue) *
              this.sliderSize
          )
        : Math.floor(
          (this.initialValue / this.sliderMaxValue) * this.sliderSize
        )
      sliderHandle.style.top = topPos + 'px'
    }
    sliderHandle.id = 'sliderForObject' + this.indexThisSlider
    sliderHandle.style.position = 'absolute'
    sliderHandle.style.zIndex = 5
    sliderHandle.onmousedown = this.__initializeSliderDrag
    sliderHandle.ondragstart = function () {
      return false
    }
    sliderHandle.onselectstart = function () {
      return false
    }
    DHTMLSuite.commonObj.__addEventEl(sliderHandle)
    this.sliderHandleImg = sliderHandle
    if (!DHTMLSuite.slider_generalMouseEventsAdded) {
      DHTMLSuite.commonObj.addEvent(
        document.documentElement,
        'mousemove',
        this.__moveSlider
      )
      DHTMLSuite.commonObj.addEvent(
        document.documentElement,
        'mouseup',
        this.__stopSlideProcess
      )
      DHTMLSuite.slider_generalMouseEventsAdded = true
    }
    sliderObj.innerHTML = '<span style="cursor:default"></span>'
    div.appendChild(sliderObj)
    if (this.numberOfSteps && this.stepLinesVisibility) {
      const stepSize = this.sliderSize / this.numberOfSteps
      for (let no = 0; no <= this.numberOfSteps; no++) {
        const lineDiv = document.createElement('DIV')
        lineDiv.style.position = 'absolute'
        lineDiv.innerHTML = '<span></span>'
        div.appendChild(lineDiv)
        if (this.sliderDirection == 'hor') {
          lineDiv.className = 'DHTMLSuite_smallLines_vertical'
          lineDiv.style.left =
            Math.floor(stepSize * no + this.sliderWidth / 2) + 'px'
        } else {
          lineDiv.className = 'DHTMLSuite_smallLines_horizontal'
          lineDiv.style.top =
            Math.floor(stepSize * no + this.sliderWidth / 2) + 'px'
          lineDiv.style.left = '14px'
        }
      }
    }
  },
  __initializeSliderDrag: function (e) {
    if (document.all) e = event
    DHTMLSuite.commonObj.__setTextSelOk(false)
    const numIndex = this.id.replace(/[^0-9]/gi, '')
    const sliderObj = DHTMLSuite.sliderObjects[numIndex]
    DHTMLSuite.indexOfCurrentlyActiveSlider = numIndex
    sliderObj.slideInProcessTimer = 0
    if (sliderObj.sliderDirection == 'hor') {
      sliderObj.slide_event_pos = e.clientX
      sliderObj.slide_start_pos = this.style.left.replace('px', '') / 1
    } else {
      sliderObj.slide_event_pos = e.clientY
      sliderObj.slide_start_pos = this.style.top.replace('px', '') / 1
    }
    sliderObj.__waitBeforeSliderDragStarts()
    return false
  },
  __setPositionFromClick: function (e) {
    if (document.all) e = event
    if (e.target) srcEvent = e.target
    else if (e.srcElement) srcEvent = e.srcElement
    if (srcEvent.nodeType == 3) srcEvent = srcEvent.parentNode
    if (srcEvent.tagName != 'DIV') return
    const numIndex = this.id.replace(/[^0-9]/gi, '')
    const sliderObj = DHTMLSuite.sliderObjects[numIndex]
    if (sliderObj.numberOfSteps) {
      modValue = sliderObj.sliderSize / sliderObj.numberOfSteps
    }
    if (sliderObj.sliderDirection == 'hor') {
      var handlePos =
        e.clientX -
        DHTMLSuite.commonObj.getLeftPos(this) -
        Math.ceil(sliderObj.sliderWidth / 2)
    } else {
      var handlePos =
        e.clientY -
        DHTMLSuite.commonObj.getTopPos(this) -
        Math.ceil(sliderObj.sliderWidth / 2)
    }
    if (sliderObj.numberOfSteps) {
      let mod = handlePos % modValue
      if (mod > modValue / 2) mod = modValue - mod
      else mod *= -1
      handlePos = handlePos + mod
    }
    if (handlePos < 0) handlePos = 0
    if (handlePos > sliderObj.sliderSize) handlePos = sliderObj.sliderSize
    if (sliderObj.sliderDirection == 'hor') {
      sliderObj.sliderHandleImg.style.left = handlePos + 'px'
      returnValue = !sliderObj.sliderValueReversed
        ? Math.round(
            (handlePos / sliderObj.sliderSize) *
              (sliderObj.sliderMaxValue - sliderObj.sliderMinValue)
          )
        : Math.round(
          ((sliderObj.sliderSize - handlePos) / sliderObj.sliderSize) *
              (sliderObj.sliderMaxValue - sliderObj.sliderMinValue)
        )
    } else {
      sliderObj.sliderHandleImg.style.top = handlePos + 'px'
      returnValue = sliderObj.sliderValueReversed
        ? Math.round(
            (handlePos / sliderObj.sliderSize) *
              (sliderObj.sliderMaxValue - sliderObj.sliderMinValue)
          )
        : Math.round(
          ((sliderObj.sliderSize - handlePos) / sliderObj.sliderSize) *
              (sliderObj.sliderMaxValue - sliderObj.sliderMinValue)
        )
    }
    returnValue = returnValue + sliderObj.sliderMinValue
    if (sliderObj.functionToCallOnChange) {
      eval(
        sliderObj.functionToCallOnChange +
          '(' +
          returnValue +
          ',"' +
          sliderObj.sliderName +
          '")'
      )
    }
    DHTMLSuite.indexOfCurrentlyActiveSlider = numIndex
    sliderObj.slideInProcessTimer = 10
    if (sliderObj.sliderDirection == 'hor') {
      sliderObj.slide_event_pos = e.clientX
      sliderObj.slide_start_pos = handlePos
    } else {
      sliderObj.slide_event_pos = e.clientY
      sliderObj.slide_start_pos = handlePos
    }
    DHTMLSuite.commonObj.__setTextSelOk(false)
  },
  __waitBeforeSliderDragStarts: function () {
    if (this.slideInProcessTimer < 10 && this.slideInProcessTimer >= 0) {
      this.slideInProcessTimer += 2
      window.refToThisSlider = this
      setTimeout('window.refToThisSlider.__waitBeforeSliderDragStarts()', 5)
    }
  },
  __moveSlider: function (e) {
    if (DHTMLSuite.indexOfCurrentlyActiveSlider === false) return
    const sliderObj =
      DHTMLSuite.sliderObjects[DHTMLSuite.indexOfCurrentlyActiveSlider]
    if (document.all) e = event
    if (sliderObj.slideInProcessTimer < 10) return
    let returnValue
    if (sliderObj.numberOfSteps) {
      modValue = sliderObj.sliderSize / sliderObj.numberOfSteps
    }
    if (sliderObj.sliderDirection == 'hor') {
      var handlePos =
        e.clientX - sliderObj.slide_event_pos + sliderObj.slide_start_pos
    } else {
      var handlePos =
        e.clientY - sliderObj.slide_event_pos + sliderObj.slide_start_pos
    }
    if (sliderObj.numberOfSteps) {
      let mod = handlePos % modValue
      if (mod > modValue / 2) mod = modValue - mod
      else mod *= -1
      handlePos = handlePos + mod
    }
    if (handlePos < 0) handlePos = 0
    if (handlePos > sliderObj.sliderSize) handlePos = sliderObj.sliderSize
    if (sliderObj.sliderDirection == 'hor') {
      sliderObj.sliderHandleImg.style.left = handlePos + 'px'
      returnValue = !sliderObj.sliderValueReversed
        ? Math.round(
            (handlePos / sliderObj.sliderSize) *
              (sliderObj.sliderMaxValue - sliderObj.sliderMinValue)
          )
        : Math.round(
          ((sliderObj.sliderSize - handlePos) / sliderObj.sliderSize) *
              (sliderObj.sliderMaxValue - sliderObj.sliderMinValue)
        )
    } else {
      sliderObj.sliderHandleImg.style.top = handlePos + 'px'
      returnValue = sliderObj.sliderValueReversed
        ? Math.round(
            (handlePos / sliderObj.sliderSize) *
              (sliderObj.sliderMaxValue - sliderObj.sliderMinValue)
          )
        : Math.round(
          ((sliderObj.sliderSize - handlePos) / sliderObj.sliderSize) *
              (sliderObj.sliderMaxValue - sliderObj.sliderMinValue)
        )
    }
    returnValue = returnValue + sliderObj.sliderMinValue
    if (sliderObj.functionToCallOnChange) {
      eval(
        sliderObj.functionToCallOnChange +
          '(' +
          returnValue +
          ',"' +
          sliderObj.sliderName +
          '")'
      )
    }
  },
  __stopSlideProcess: function (e) {
    DHTMLSuite.commonObj.__setTextSelOk(true)
    if (!DHTMLSuite.indexOfCurrentlyActiveSlider) return
    const sliderObj =
      DHTMLSuite.sliderObjects[DHTMLSuite.indexOfCurrentlyActiveSlider]
    sliderObj.slideInProcessTimer = -1
  }
}
