if (!window.DHTMLSuite) var DHTMLSuite = new Object()
DHTMLSuite.textEdit = function () {
  let layoutCSS
  let elements
  let elementsAssociative
  let serversideFile
  let objectIndex
  let inputObjects
  this.layoutCSS = 'text-edit.css'
  this.elements = new Array()
  this.elementsAssociative = new Object()
  this.inputObjects = new Object()
  try {
    if (!standardObjectsCreated) DHTMLSuite.createStandardObjects()
  } catch (e) {
    alert('Include the dhtmlSuite-common.js file')
  }
  this.objectIndex = DHTMLSuite.variableStorage.arrayDSObjects.length
  DHTMLSuite.variableStorage.arrayDSObjects[this.objectIndex] = this
}
DHTMLSuite.textEdit.prototype = {
  setLayoutCss: function (layoutCSS) {
    this.layoutCSS = layoutCSS
  },
  setServersideFile: function (serversideFile) {
    this.serversideFile = serversideFile
  },
  addElement: function (inputArray) {
    const index = this.elements.length
    try {
      this.elements[index] = new DHTMLSuite.textEditModel(inputArray)
    } catch (e) {
      alert('Error: Include dhtmlSuite-textEditModel.js in your html file')
    }
    this.elementsAssociative[inputArray.elementId] = this.elements[index]
  },
  init: function () {
    DHTMLSuite.commonObj.loadCSS(this.layoutCSS)
    const index = this.objectIndex
    for (let no = 0; no < this.elements.length; no++) {
      const obj = this.elements[no]
      const label = document.getElementById(obj.labelId)
      if (label) {
        label.setAttribute('elementId', obj.elementId)
        if (!label.getAttribute('elementId')) label.elementId = obj.elementId
        if (label.className) {
          label.setAttribute('origClassname', label.className)
          label.origClassname = label.className
        }
        label.onclick = function (e) {
          DHTMLSuite.variableStorage.arrayDSObjects[index].__clickOnLabel(e)
        }
        DHTMLSuite.commonObj.__addEventEl(label)
      }
      const el = document.getElementById(obj.elementId)
      DHTMLSuite.commonObj.__addEventEl(el)
      if (el) {
        el.onclick = function (e) {
          DHTMLSuite.variableStorage.arrayDSObjects[index].__clickOnElement(e)
        }
        if (obj.listModel) {
          this.inputObjects[obj.elementId] = document.createElement('SELECT')
          const selObj = this.inputObjects[obj.elementId]
          selObj.className = 'DHTMLSuite_textEdit_select'
          for (let no2 = 0; no2 < obj.listModel.options.length; no2++) {
            selObj.options[selObj.options.length] = new Option(
              obj.listModel.options[no2].text,
              obj.listModel.options[no2].value
            )
          }
          selObj.id = 'input___' + el.id
          selObj.onblur = function (e) {
            DHTMLSuite.variableStorage.arrayDSObjects[index].__exitEditMode(e)
          }
          DHTMLSuite.commonObj.__addEventEl(selObj)
          el.parentNode.insertBefore(selObj, el)
          selObj.style.display = 'none'
        } else {
          this.inputObjects[obj.elementId] = document.createElement('INPUT')
          const input = this.inputObjects[obj.elementId]
          input.onblur = function (e) {
            DHTMLSuite.variableStorage.arrayDSObjects[index].__exitEditMode(e)
          }
          DHTMLSuite.commonObj.__addEventEl(input)
          input.className = 'DHTMLSuite_textEdit_input'
          input.id = 'input___' + el.id
          input.value = el.innerHTML
          el.parentNode.insertBefore(input, el)
          input.style.display = 'none'
        }
      }
    }
  },
  __setLabelClassName: function (obj, state) {
    if (state == 'active') obj.className = 'DHTMLSuite_textEdit_label'
    else {
      let className = ''
      className = obj.getAttribute('origClassname')
      if (!className) className = obj.origClassname
      obj.className = className
    }
  },
  __clickOnLabel: function (e) {
    if (document.all) e = event
    const obj = DHTMLSuite.commonObj.getSrcElement(e)
    this.__setLabelClassName(obj, 'active')
    const elementId = obj.getAttribute('elementId')
    this.__clickOnElement(false, document.getElementById(elementId))
  },
  __clickOnElement: function (e, obj) {
    if (document.all) e = event
    if (!obj) var obj = DHTMLSuite.commonObj.getSrcElement(e)
    const id = obj.id
    const dataSource = this.elementsAssociative[id]
    if (dataSource.listModel) this.__setSelectBoxValue(id, obj.innerHTML)
    if (dataSource.labelId) {
      this.__setLabelClassName(
        document.getElementById(dataSource.labelId),
        'active'
      )
    }
    this.inputObjects[id].style.display = ''
    this.inputObjects[id].focus()
    if (!dataSource.listModel) this.inputObjects[id].select()
    obj.style.display = 'none'
  },
  __setSelectBoxValue: function (id, value) {
    const selObj = this.inputObjects[id]
    for (let no = 0; no < selObj.options.length; no++) {
      if (selObj.options[no].text == value) {
        selObj.selectedIndex = no
        return
      }
    }
  },
  __exitEditMode: function (e) {
    if (document.all) e = event
    const obj = DHTMLSuite.commonObj.getSrcElement(e)
    const elementId = obj.id.replace('input___', '')
    const dataSource = this.elementsAssociative[elementId]
    let newValue
    let valueToSendToAjax
    if (dataSource.listModel) {
      newValue = obj.options[obj.options.selectedIndex].text
      valueToSendToAjax = obj.options[obj.options.selectedIndex].value
    } else {
      newValue = obj.value
      valueToSendToAjax = newValue
    }
    if (e.keyCode && e.keyCode == 27) {
      newValue = document.getElementById(dataSource.elementId).innerHTML
    }
    if (
      newValue &&
      newValue != document.getElementById(dataSource.elementId).innerHTML
    ) {
      this.__sendRequest(dataSource.elementId, valueToSendToAjax)
    }
    document.getElementById(dataSource.elementId).innerHTML = newValue
    document.getElementById(dataSource.elementId).style.display = ''
    obj.style.display = 'none'
    if (dataSource.labelId) {
      this.__setLabelClassName(
        document.getElementById(dataSource.labelId),
        'inactive'
      )
    }
  },
  __sendRequest: function (elementId, value) {
    const index = DHTMLSuite.variableStorage.ajaxObjects.length
    const ind = this.objectIndex
    try {
      DHTMLSuite.variableStorage.ajaxObjects[index] = new sack()
    } catch (e) {
      alert(
        'Unable to create ajax object. Please make sure that the sack js file is included on your page'
      )
      return
    }
    let url
    url = this.elementsAssociative[elementId].serverFile
      ? this.elementsAssociative[elementId].serverFile
      : this.serversideFile
    if (url.indexOf('?') >= 0) url = url + '&'
    else url = url + '?'
    url =
      url +
      'saveTextEdit=1&textEditElementId=' +
      elementId +
      '&textEditValue=' +
      escape(value)
    DHTMLSuite.variableStorage.ajaxObjects[index].requestFile = url
    DHTMLSuite.variableStorage.ajaxObjects[index].onCompletion = function () {
      DHTMLSuite.variableStorage.arrayDSObjects[ind].__handleServerSideResponse(
        index,
        url
      )
    }
    DHTMLSuite.variableStorage.ajaxObjects[index].onError = function () {
      DHTMLSuite.variableStorage.arrayDSObjects[ind].__handleAjaxError(
        index,
        url
      )
    }
    DHTMLSuite.variableStorage.ajaxObjects[index].runAJAX()
  },
  __handleServerSideResponse: function (ajaxIndex, url) {
    if (DHTMLSuite.variableStorage.ajaxObjects[ajaxIndex].response != 'OK') {
      alert(
        'An error occured in the textEdit widget when calling the url\n' + url
      )
    }
    DHTMLSuite.variableStorage.ajaxObjects[ajaxIndex] = null
  },
  __handleAjaxError: function (ajaxIndex, url) {
    alert('Error when calling the url:\n' + url)
    DHTMLSuite.variableStorage.ajaxObjects[ajaxIndex] = null
  }
}
