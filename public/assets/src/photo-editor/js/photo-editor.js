/**
 * Class for the photo editor.
 *
 * This class is the class for make all the transformations needed for the photos
 *
 * @param {object}  conf                - Configuration need for the photo editor
 * @param {string}  conf.container      - Id of the html element to contain the photo editor
 * @param {string}  conf.image          - The image to edit
 * @param {string}  conf.template       - Template name used for the photo editor
 * @param {method}  conf.closeCallBack  - callback by the time the photo edition is finished
 * @param {object}  conf.maxSize        - The maximun size of the photoEditor. If you don't use this the photo editor
 *    use the screen size
 * @param {integer} conf.maxSize.width  - Maximun width
 * @param {integer} conf.maxSize.height - Maximun height
 */
window.OnmPhotoEditor = function(conf) {
  this.conf = conf;
  this.statusImage = {
    brightness: 0,
    contrast: 0,
    rotation: 0,
    cropSizes: null,
    mirror: { v: 1, h: 1 }
  };
};

/*
 *  Displays availables for the editor
 */

/**
 * Principal display for the photo editor. From here you can reach all the other
 * displays
 */
window.OnmPhotoEditor.prototype.DISPLAY_INIT      = 'init';

/**
 * Display for rotation and crop the images
 */
window.OnmPhotoEditor.prototype.DISPLAY_TRANSFORM = 'transform';

/**
 * Display to apply the different presets availables
 */
window.OnmPhotoEditor.prototype.DISPLAY_FILTERS   = 'filter';

/**
 * Display to apply the different color and light filters availables
 */
window.OnmPhotoEditor.prototype.DISPLAY_LIGHT     = 'light';

/*
 * Menu actions. Here you can see all the options of the different menus.
 */

/**
 * Action to change the brightness of the photo
 */
window.OnmPhotoEditor.prototype.ACTIONS_BRIGHTNESS = 'brightness';

/**
 * Basic template.
 *
 * Template with the list of actions for the basic template
 *
 * @property {object.<string, mixed>} actionList - List of all actions we can do. The key is the displays
 *           availables for this template and the values are the list of actions or displays for the display
 */
window.OnmPhotoEditor.prototype.TEMPLATE_BASIC     = {
  init: [
    'transform',
    'light',
    'filter'
  ],
  light: [ 'brightness', 'contrast' ],
  transform: [
    [
      {
        action: 'orientation',
        icon:   'landscape',
        text:   'landscape',
        value:  'landscape',
        default: 1
      },
      {
        action: 'orientation',
        icon:   'portrait',
        text:   'portrait',
        value:  'portrait'
      },
    ],
    [
      {
        action: 'ratio',
        text:   'free',
        value:  'free'
      },
      {
        action: 'ratio',
        text:   '1:1',
        value:  '1:1'
      },
      {
        action: 'ratio',
        text:   '4:3',
        value:  '4:3'
      },
      {
        action:  'ratio',
        text:    '16:9',
        value:   '16:9',
        default: 1
      }
    ],
    {
      action: 'rotation',
      icon:   'undo',
      value:  '-90'
    },
    {
      action: 'rotation',
      icon:   'repeat',
      value:  '90'
    },
    {
      action: 'mirror',
      icon:   'mirror',
      value:  'vertical'
    },
    {
      action: 'mirror',
      icon:   'mirrorv',
      value:  'horizontal'
    }
  ],
  filter: 'initFilterMenu'
};

/**
 * @description
 *     status for the photo editor. Here we have all data needed to show the
 * current status of the photo editor
 *
 * @typedef  {object}  status
 * @property {string}  status.display                - display selected
 * @property {string}  status.action                 - action selected
 * @property {element} status.image                  - Initial image to load in
 *    Canvas
 * @property {string}  status.error                  - error loading the canvas
 * @property {boolean} status.loading                - If the photo editor is
 *    Loading something
 * @property {object.<string, string>} multiSelect - For actions with your own
 *    selection. The key is the name of the action and the value is the option
 *    value
 */

/**
 * Status of the photo editor
 *
 * @property {status} Status of the photo editor what determine what are showing
 */
window.OnmPhotoEditor.prototype.status = {
  display:     null,
  action:      null,
  image:       null,
  error:       null,
  loading:     false,
  multiSelect: {}
};

/**
 * Canvas status of the image
 *
 * @property {status} statusImage of the photo what determine how we see the image
 */
window.OnmPhotoEditor.prototype.statusImage = null;

/**
 * List of displays and respective actions for the photo editor
 *
 * @property {object.<string, mixed>} actionList - List of all actions we can do with the photo editor. The key is the
 *           displays availables and the values are the list of actions or displays of this displays
 */
window.OnmPhotoEditor.prototype.actionList  = null;

/*
 * Photod editor elements
 *
 * All parts of the photo editor display. This element are here to all us can know
 * the parts we are using to show the photo editor interface.
 */

/**
 * Element where we go to load the main menu(bottom menu) of the photo editor
 *
 * @property {element} divMenu - Html element for the menu
 */
window.OnmPhotoEditor.prototype.divMenu     = null;

/**
 * Element where we go to load the photo we pretend to edit
 *
 * @property {element} canvas - Html element for the photo
 */
window.OnmPhotoEditor.prototype.canvas               = null;

/**
 * Canvas context
 *
 * @property {element} ctx - Context of the photo to edit
 */
window.OnmPhotoEditor.prototype.ctx = null;

/**
 * Maximun size of the photo editor
 *
 * @property {object} maximunSize        - The maximun size for the photo editor
 * @property {object} maximunSize.width  - The maximun width
 * @property {object} maximunSize.height - The maximun height
 */
window.OnmPhotoEditor.prototype.maximunSize = { width: 0, height: 0 };

/**
 * Element where we go to load the canvas for edit the photo
 *
 * @property {element} divCanvas - Html element for the photo
 */
window.OnmPhotoEditor.prototype.divCanvas   = null;

/**
 * Element where we go to load top menu
 *
 * @property {element} divTopMenu - Html element for the top menu
 */
window.OnmPhotoEditor.prototype.divTopMenu  = null;

/**
 * Element where we go to put the backs buttons
 *
 * @property {element} divTopMenuBack - Html element for the top menu back buttons
 */
window.OnmPhotoEditor.prototype.divTopMenuBack = null;

/**
 * Element where we go to put the top title for the photo editor
 *
 * @property {element} divTopMenuTitle - Html element for the top menu
 */
window.OnmPhotoEditor.prototype.divTopMenuTitle = null;

/**
 * Elements custom for the actual display
 */
window.OnmPhotoEditor.prototype.displayElements = [];

/**
 *  Elements custom for the actual action
 */
window.OnmPhotoEditor.prototype.actionElements = [];

/**
 * This property have all icons for the different actions of the photo editor
 *
 * @property {object.<string, string>} ACTION_ICONS - List of all actions we can do with the photo editor. The key is
 *           the displays availables and the values are the list of actions or displays of this displays
 */
window.OnmPhotoEditor.prototype.ACTION_ICONS = {
  transform:  'crop',
  light:      'adjust',
  filter:     'magic',
  brightness: 'sun-o',
  back:       'angle-left',
  landscape:  'file-o fa-rotate-90',
  portrait:   'file-o',
  undo:       'undo',
  repeat:     'repeat',
  mirror:     'exchange',
  mirrorv:    'exchange fa-rotate-90',
  contrast:   'adjust'
};

/**
 * @function init
 * @memberof OnmPhotoEditor
 *
 * @description
 *   Init the class photo editor for edit photos. Here we check if we have all data need to load the photo editor, load
 * the canvas image and draw the photo editor init display
 */
window.OnmPhotoEditor.prototype.init = function() {
  if (!this.conf.container || this.conf.container === '') {
    return null;
  }

  this.container = document.getElementById(this.conf.container);

  if (!this.container || this.container === null) {
    return null;
  }

  if (!this.conf.image || this.conf.image === '') {
    return null;
  }

  var status = {
    display:     this.DISPLAY_INIT,
    action:      null,
    multiSelect: {}
  };

  this.actionList = this.getActionList(status);

  this.drawPhotoEditor(status);
  this.maximunSize = this.getMaxSize();
  this.loadImage(this.conf.image);
  return null;
};

// Create HTML

/**
 * @function drawPhotoEditor
 * @memberof OnmPhotoEditor
 *
 * @description
 *   Create the html for to show the photo editor.
 *
 * @param {status} status - status of the photo editor what determine what are showing
 */
window.OnmPhotoEditor.prototype.drawPhotoEditor = function(status) {
  this.container.innerHTML = '';
  this.container.appendChild(this.getTopMenu(status));
  this.container.appendChild(this.getCanvas());
  this.container.appendChild(this.getMenu(status));
  this.status = status;
};

/**
 * @function updateStatusPhotoEditor
 * @memberof OnmPhotoEditor
 *
 * @description
 *   Method for update the status of the photo editor.
 *
 * @param {object} status - status of the photo editor what determine what are showing
 */
window.OnmPhotoEditor.prototype.updateStatusPhotoEditor = function(status) {
  if (JSON.stringify(status) === JSON.stringify(this.status)) {
    return false;
  }
  this.getTopMenu(status);
  this.getMenu(status);

  // If we have a init method we lunch this method
  if (status.display !== this.status.display) {
    if (this.displayElements.length > 0) {
      this.displayElements.forEach(function(element) {
        if (element.parentElement !== null) {
          element.parentElement.removeChild(element);
        }
      });
      this.displayElements = [];
    }

    var initDisplay = 'init' + this.capitalizeFirstLetter(status.display);

    if (typeof this[initDisplay] === 'function') {
      this[initDisplay]();
    }
  }

  this.status = status;
  return null;
};

/**
 * @function getTopMenu
 * @memberof OnmPhotoEditor
 *
 * @description
 *   Method for the generation of the top menu
 *
 * @param {object} status - status of the photo editor what determine what are showing
 *
 * @return {string} If no display has been loaded yet, it will return the html from the top menu.
 */
window.OnmPhotoEditor.prototype.getTopMenu = function(status) {
  var that = this;
  var topMenuStatus = this.getTopMenuElements(status);

  if (topMenuStatus === null) {
    return null;
  }

  var menuElements = [];

  menuElements = menuElements.concat(this.getTopMenuSection('topMenuBack', topMenuStatus.back));
  menuElements = menuElements.concat(this.getTopMenuSection('topMenuTitle', topMenuStatus.title));
  menuElements = menuElements.concat(this.getTopMenuSection('topMenuSave', [ 'save' ]));

  if (this.divTopMenu === null) {
    this.divTopMenu = document.createElement('ul');
    this.divTopMenu.setAttribute('class', 'topMenu');
  }
  this.divTopMenu.innerHTML = '';

  menuElements.forEach(function(element) {
    that.divTopMenu.appendChild(element);
  });

  return this.divTopMenu;
};

/**
 * @function getTopMenu
 * @memberof OnmPhotoEditor
 *
 * @description
 *   Method for the generation of the top menu html
 *
 * @param {string} section  - top menu section(cssclass [topMenuBack, topMenuTitle or topMenuSave])
 * @param {string} action   - action to show our client
 *
 * @return {array<element>} top menu elements for some option.
 */
window.OnmPhotoEditor.prototype.getTopMenuSection = function(section, options) {
  var elementList = [];
  var that        = this;

  if (Array.isArray(options)) {
    options.forEach(function(option) {
      elementList.push(that.getTopMenuElement(section, option));
    });
  }

  return elementList;
};

/**
 * @function getTopMenuElement
 * @memberof OnmPhotoEditor
 *
 * @description
 *   Method for the generation of one element of the top menu
 *
 * @param {string} section  - top menu section(cssclass [topMenuBack, topMenuTitle or topMenuSave])
 * @param {string} action   - action to show our client
 *
 * @return {element} Element of top menu.
 */
window.OnmPhotoEditor.prototype.getTopMenuElement = function(section, option) {
  if (typeof option === 'string') {
    option = { action: option, text: option };
  }

  return this.getPosition('li', section, option);
};

/**
 * @function getTopMenuElements
 * @memberof OnmPhotoEditor
 *
 * @description
 *   Method for get the status of the top menu
 *
 * @param {status} status         - status of the photo editor what determine what are showing
 *
 * @return {object} status for the top menu
 */
window.OnmPhotoEditor.prototype.getTopMenuElements = function(status) {
  if (status.display === this.status.display) {
    return null;
  }

  var backVal      = status.display === this.DISPLAY_INIT || status.display === null ?
    'cancel' :
    { action: 'cancel', icon: 'back' };

  var defaultTitle = '<h4>edit image</h4>';

  if (status.display !== this.DISPLAY_INIT) {
    return {
      back: [ backVal ],
      title: [
        defaultTitle,
        status.display
      ]
    };
  }
  return { back: [ backVal ], title: [ defaultTitle ] };
};

/**
 * @function getCanvas
 * @memberof OnmPhotoEditor
 *
 * @description
 *   Method for create the canvas element
 */
window.OnmPhotoEditor.prototype.getCanvas = function() {
  this.divCanvas = document.createElement('div');
  this.divCanvas.setAttribute('class', 'divCanvas');
  this.divCanvas.innerHTML = '<i class="fa fa-spinner" aria-hidden="true"></i>';

  var divCenter = document.createElement('div');

  divCenter.setAttribute('class', 'divCanvasCenter');
  divCenter.appendChild(this.divCanvas);

  return divCenter;
};

/**
 * @function getMenu
 * @memberof OnmPhotoEditor
 *
 * @description
 *   Method for get the menu html
 *
 * @param {status} status - status of the photo editor what determine what are showing
 *
 * @return {string} html of the menu
 */
window.OnmPhotoEditor.prototype.getMenu = function(status) {
  if (!this.actionList ||
    !(status.display in this.actionList) ||
    !Array.isArray(this.actionList[status.display]) ||
    this.actionList[status.display].length === 0
  ) {
    return null;
  }

  if (this.divMenu === null) {
    this.divMenu = document.createElement('div');
    this.divMenu.setAttribute('class', 'menu');
  }

  var mainMenu  = this.getMainMenu(status);

  var leftMenu  = null;
  var rightMenu = null;

  // if (this.DISPLAY_TRANSFORM === this.status.display) {
  //   this.getPosition('div', 'leftMenu', { action: 'reset', text: 'Reset' });
  //   this.getPosition('div', 'rightMenu', { action: 'back', text: 'OK' });
  // }

  this.divMenu.innerHTML = '';
  this.divMenu.appendChild(mainMenu);

  if (leftMenu !== null) {
    this.divMenu.appendChild(leftMenu);
  }

  if (rightMenu !== null) {
    this.divMenu.appendChild(rightMenu);
  }
  return this.divMenu;
};

window.OnmPhotoEditor.prototype.getMainMenu = function(status) {
  var menuElements = [];
  var options      = this.actionList[status.display];
  var elementAux   = null;

  for (var i = 0; i < options.length; i++) {
    if (Array.isArray(options[i])) {
      menuElements = menuElements.concat(this.getSubMenu(options, i, status));
      continue;
    }

    elementAux = this.getActionButton(options[i], status);
    if (elementAux !== null) {
      menuElements.push(elementAux);
    }
  }
  var ulEle = document.createElement('ul');

  menuElements.forEach(function(element) {
    ulEle.appendChild(element);
  });

  var divMainMenu = document.createElement('div');

  divMainMenu.setAttribute('class', 'mainMenu');
  divMainMenu.appendChild(ulEle);

  return ulEle;
};

window.OnmPhotoEditor.prototype.getSubMenu = function(mainOptions, pos, status) {
  var subOptions  = [];
  var options     = mainOptions[pos];
  var smallScreen = this.container.clientWidth < 600;
  var selected    = null;
  var liAux       = null;

  if (!smallScreen && pos !== 0 && !Array.isArray(mainOptions[pos - 1])) {
    liAux = document.createElement('li');
    liAux.setAttribute('class', 'quicklinks hidden-xs hidden-sm');
    liAux.innerHTML = '<span class="h-seperate"></span>';
    subOptions.push(liAux);
  }

  for (var i = 0; i < options.length; i++) {
    var elementAux = this.getActionButton(options[i], status);

    if (elementAux === null) {
      continue;
    }

    if (
      smallScreen &&
      selected === null &&
      elementAux.getAttribute('class') &&
      elementAux.getAttribute('class').indexOf(' active') > -1
    ) {
      selected = i;
      continue;
    }
    subOptions.push(elementAux);
  }

  if (smallScreen && selected !== null) {
    return this.getDropDown(subOptions, options[selected]);
  }

  if (pos + 1 !== mainOptions.length) {
    liAux = document.createElement('li');

    liAux.setAttribute('class', 'quicklinks hidden-xs hidden-sm');
    liAux.innerHTML = '<span class="h-seperate"></span>';
    subOptions.push(liAux);
  }
  return subOptions;
};

window.OnmPhotoEditor.prototype.getDropDown = function(subOptions, selected) {
  var submenuOption = { action: 'showSubmenu' };

  if (selected.icon) {
    submenuOption.icon = selected.icon;
  }

  if (selected.text) {
    submenuOption.text = selected.text;
  }
  var selectedToShow = this.getPosition('li', null, submenuOption);
  var ulElement = document.createElement('ul');

  subOptions.forEach(function(element) {
    ulElement.appendChild(element);
  });

  selectedToShow.appendChild(ulElement);
  return selectedToShow;
};

/**
 * @function getActionButton
 * @memberof OnmPhotoEditor
 *
 * @description
 *   Method for get a button
 *
 * @param {string} action - action to show our client
 * @param {status} status - status of the photo editor what determine what are showing
 *
 * @return {string} button element for the main menu
 */
window.OnmPhotoEditor.prototype.getActionButton = function(actionVal, status) {
  var option = actionVal;

  if (typeof actionVal === 'string') {
    option = {
      action: actionVal,
      text: actionVal,
      icon: actionVal
    };
  }

  var classAttr = option.action && actionVal === status.action ||
    option.action in status.multiSelect &&
    status.multiSelect[option.action] === option.value ?
    ' active' : null;

  return this.getPosition('li', classAttr, option);
};

window.OnmPhotoEditor.prototype.initTransform = function() {
  var topCircle    = document.createElement('div');
  var bottomCircle = document.createElement('div');
  var applyBtn     = document.createElement('a');
  var applyDivBtn  = document.createElement('div');
  var divCrop      = document.createElement('div');

  topCircle.setAttribute('class', 'photoEditorCircle topCircle');
  topCircle.addEventListener('mousedown', this.initResize.bind(this));

  bottomCircle.setAttribute('class', 'photoEditorCircle bottomCircle');
  bottomCircle.addEventListener('mousedown', this.initResize.bind(this));

  applyBtn.innerHTML = '<i class="fa fa-crop" aria-hidden="true"></i> apply';
  applyBtn.href = '#crop';
  applyBtn.addEventListener('click', this.callAction.bind(this));

  applyDivBtn.setAttribute('class', 'buttonApply');
  applyDivBtn.appendChild(applyBtn);

  divCrop.setAttribute('class', 'photoEditorCrop');
  divCrop.style.width  = this.canvas.width + 'px';
  divCrop.style.height = this.canvas.height + 'px';
  divCrop.appendChild(topCircle);
  divCrop.appendChild(bottomCircle);
  divCrop.appendChild(applyDivBtn);

  this.displayElements.push(divCrop);

  this.divCanvas.appendChild(divCrop);
  this.resizeCropDiv(this.status);
};

window.OnmPhotoEditor.prototype.createFormHtml = function(inputs) {
  var formDiv       = document.createElement('div');
  var formContainer = document.createElement('div');
  var inputAux      = null;
  var inputVals     = null;

  formContainer.setAttribute('class', 'formContainer');
  for (var inputIndex in inputs) {
    inputVals = inputs[inputIndex];
    inputAux = document.createElement('input');
    for (var key in inputVals.attributes) {
      inputAux[key] = inputVals.attributes[key];
    }
    for (var key in inputVals.events) {
      inputAux.addEventListener(key, inputVals.events[key]);
    }
    formContainer.appendChild(inputAux);
  }

  formDiv.setAttribute('class', 'canvasForm');
  formDiv.style.width  = this.canvas.width + 'px';
  formDiv.style.height = this.canvas.height + 'px';
  formDiv.appendChild(formContainer);

  this.divCanvas.appendChild(formDiv);
  this.actionElements.push(formDiv);
};

window.OnmPhotoEditor.prototype.showCanvas = function(canvas) {
  this.canvas.width  = canvas.canvas.width;
  this.canvas.height = canvas.canvas.height;
  this.ctx.drawImage(canvas.canvas, 0, 0, this.canvas.width, this.canvas.height);
  this.divCanvas.style.width = this.canvas.width + 'px';
  this.divCanvas.style.height = this.canvas.height + 'px';
};

// ACTIONS

// TOP MENU ACTIONS

/**
 *
 */
window.OnmPhotoEditor.prototype.callCancel = function(e, newStatus) {
  if (this.status.display === this.DISPLAY_INIT) {
    this.conf.closeCallBack(null);
    return false;
  }

  newStatus.display = this.DISPLAY_INIT;
  newStatus.action  = null;
  return newStatus;
};

/**
 *
 */
window.OnmPhotoEditor.prototype.callSave = function() {
  this.conf.closeCallBack(this.canvas);
};

window.OnmPhotoEditor.prototype.callAction = function(e) {
  e.preventDefault();
  var hashVal   = e.currentTarget.hash.slice(1).split(',');
  var action    = hashVal[0];
  var newStatus = JSON.parse(JSON.stringify(this.status));

  if (action in this.actionList) {
    newStatus.display = action;
    this.updateStatusPhotoEditor(newStatus);
    return false;
  }

  newStatus.action = action;

  // If we change the action we clean the other action elements
  if (status.action !== this.status.action) {
    if (this.actionElements.length > 0) {
      this.actionElements.forEach(function(element) {
        if (element.parentElement !== null) {
          element.parentElement.removeChild(element);
        }
      });
      this.actionElements = [];
    }
  }

  if (hashVal.length > 1) {
    // The format for the link hash is '''#ratio,value'''. We need the link value
    newStatus.multiSelect[action] = hashVal[1];
  }
  var newReturnStatus = this['call' + this.capitalizeFirstLetter(action)](e, newStatus);

  if (newReturnStatus && typeof newReturnStatus === 'object') {
    newStatus = newReturnStatus;
  }
  this.updateStatusPhotoEditor(newStatus);
  return newStatus;
};

window.OnmPhotoEditor.prototype.callBrightness = function() {
  var that = this;
  var newStatusImage = this.copyStatusImage(this.statusImage);

  newStatusImage.brightness = 0;
  var canvas2Show    = this.getCanvas2Show(this.statusImage.canvasOriginal, newStatusImage);
  var brightnessForm = {
    attributes: {
      type: 'range',
      min: -100,
      max: 100,
      step: 1,
      value: that.statusImage.brightness
    },
    events: {
      input: function(e) {
        e.preventDefault();
        that.brightness(canvas2Show, that.ctx, e.currentTarget.value);
        return null;
      },
      change: function(e) {
        e.preventDefault();
        that.brightness(canvas2Show, that.ctx, e.currentTarget.value);
        that.statusImage.brightness = e.currentTarget.value;
        return null;
      }
    }
  };

  this.createFormHtml([ brightnessForm ]);
  return null;
};

window.OnmPhotoEditor.prototype.callContrast = function() {
  var that = this;
  var newStatusImage = this.copyStatusImage(this.statusImage);

  newStatusImage.contrast = 0;
  var canvas2Show    = this.getCanvas2Show(this.statusImage.canvasOriginal, newStatusImage);
  var contrastForm = {
    attributes: {
      type: 'range',
      min: -100,
      max: 100,
      step: 1,
      value: that.statusImage.contrast
    },
    events: {
      input: function(e) {
        e.preventDefault();
        that.contrast(canvas2Show, that.ctx, e.currentTarget.value);
        return null;
      },
      change: function(e) {
        e.preventDefault();
        that.contrast(canvas2Show, that.ctx, e.currentTarget.value);
        that.statusImage.contrast = e.currentTarget.value;
        return null;
      }
    }
  };

  this.createFormHtml([ contrastForm ]);
  return null;
};

window.OnmPhotoEditor.prototype.callRatio = function(e, newStatus) {
  if (!newStatus.multiSelect.ratio || newStatus.multiSelect.ratio === 'free') {
    return newStatus;
  }

  this.callOrientation(e, newStatus);
  return null;
};

window.OnmPhotoEditor.prototype.callOrientation = function(e, newStatus) {
  this.resizeCropDiv(newStatus);
};

window.OnmPhotoEditor.prototype.callRotation = function(e, newStatus) {
  var positive = parseInt(newStatus.multiSelect.rotation) > 0;

  this.statusImage.rotation += parseInt(newStatus.multiSelect.rotation);

  if (Math.abs(this.statusImage.rotation) >= 360) {
    this.statusImage.rotation = this.statusImage.rotation % 360;
  }

  var oldCanvasSize = { width: this.canvas.height, height: this.canvas.width };
  var resizeEle    = document.querySelector('.photoEditor .divCanvas .photoEditorCrop');
  var resizeVP     = resizeEle.getBoundingClientRect();
  var height       = resizeVP.height;
  var width        = resizeVP.width;
  var leftMargin   = positive ?
    this.canvas.getBoundingClientRect().bottom - resizeVP.bottom :
    this.canvas.getBoundingClientRect().top - resizeVP.top;
  var topMargin    = positive ?
    this.canvas.getBoundingClientRect().left - resizeVP.left :
    this.canvas.getBoundingClientRect().right - resizeVP.right;

  leftMargin = Math.abs(leftMargin);
  topMargin  = Math.abs(topMargin);

  var canvas2Show = this.getCanvas2Show(this.statusImage.canvasOriginal, this.statusImage);

  this.showCanvas(canvas2Show);

  var widthRatio  =  this.canvas.width / oldCanvasSize.width;
  var heightRatio = this.canvas.height / oldCanvasSize.height;

  resizeEle.style.marginTop  = topMargin * heightRatio + 'px';
  resizeEle.style.marginLeft = leftMargin * widthRatio + 'px';

  resizeEle.style.width = height * widthRatio + 'px';
  resizeEle.style.height = width * heightRatio + 'px';

  newStatus.multiSelect.orientation = newStatus.multiSelect.orientation === 'portrait' ? 'landscape' : 'portrait';
  return newStatus;
};

window.OnmPhotoEditor.prototype.callCrop = function(e, newStatus) {
  var divCrop        = document.querySelector('.photoEditor .divCanvas .photoEditorCrop');

  this.statusImage.cropSizes = this.rotateAndCropWithDiv(
    this.statusImage,
    divCrop.getBoundingClientRect(),
    this.canvas.getBoundingClientRect()
  );

  var canvas2Show = this.getCanvas2Show(this.statusImage.canvasOriginal, this.statusImage);

  this.showCanvas(canvas2Show);

  divCrop.style.width  = this.canvas.width + 'px';
  divCrop.style.height = this.canvas.height + 'px';
  divCrop.style.marginTop  = 0;
  divCrop.style.marginLeft = 0;

  return newStatus;
};

window.OnmPhotoEditor.prototype.callMirror = function(e, newStatus) {
  var divCrop        = document.querySelector('.photoEditor .divCanvas .photoEditorCrop');
  var horizontal     = newStatus.multiSelect.mirror === 'horizontal';

  if (horizontal === (this.statusImage.rotation % 180 === 0)) {
    this.statusImage.mirror.v *= -1;
  } else {
    this.statusImage.mirror.h *= -1;
  }
  newStatus.multiSelect.mirror = '';
  var canvas2Show              = this.getCanvas2Show(this.statusImage.canvasOriginal, this.statusImage);

  this.showCanvas(canvas2Show);

  if (!horizontal) {
    divCrop.style.marginLeft = this.canvas.getBoundingClientRect().right - divCrop.getBoundingClientRect().right + 'px';
    return newStatus;
  }

  divCrop.style.marginTop  = this.canvas.getBoundingClientRect().bottom - divCrop.getBoundingClientRect().bottom + 'px';
  return newStatus;
};

// CANVAS ACTIONS

window.OnmPhotoEditor.prototype.loadImage = function(image) {
  var that = this;

  if (typeof image === 'string') {
    var canvasImg = new Image();

    canvasImg.onload = function() {
      that.status.loading = false;
      that.loadImageInCanvas(that, this);
    };

    canvasImg.onerror = function() {
      that.status.loading = false;
      that.status.error   = 'Error loading the image';
    };
    canvasImg.src = image;
    that.status.loading = true;
    return null;
  }

  var elementType = image.nodeName.toLowerCase();

  if (elementType === 'img') {
    that.loadImageInCanvas(that, image);
    return null;
  }

  if (elementType === 'input' && image.type === 'file') {
    that.readImageFromFile(image);
  }
  return null;
};

window.OnmPhotoEditor.prototype.readImageFromFile = function() {
  var that  = this;
  var fr    = new FileReader();
  var img   = null;
  var input = document.createElement('input');

  fr.onload = function() {
    img = new Image();

    img.onload = function() {
      that.status.loading = false;
      that.loadImageInCanvas(that, this);
    };

    img.onerror = function() {
      that.status.loading = false;
      that.status.error   = 'Error loading the image';
    };
    img.src = fr.result;
  };
  var file = input.files[0];

  fr.readAsDataURL(file);
};

window.OnmPhotoEditor.prototype.loadImageInCanvas = function(self, img) {
  var imgSize        = this.getElementSize(img);
  var canvas         = document.createElement('canvas');
  var canvasOriginal = document.createElement('canvas');
  var ctxOriginal    = null;
  var ctx            = null;

  canvasOriginal.width  = imgSize.width;
  canvasOriginal.height = imgSize.height;
  ctxOriginal           = canvasOriginal.getContext('2d');
  ctxOriginal.drawImage(
    img,
    0,
    0,
    imgSize.width,
    imgSize.height
  );

  imgSize = self.getAdaptCanvasSize(canvasOriginal);

  canvas.width  = imgSize.width;
  canvas.height = imgSize.height;
  ctx           = canvas.getContext('2d');
  ctx.drawImage(canvasOriginal, 0, 0, imgSize.width, imgSize.height);

  self.statusImage.canvasOriginal = canvasOriginal;
  self.statusImage.ctxOriginal    = ctxOriginal;
  self.statusImage.rotation       = 0;
  self.statusImage.image          = img;

  self.canvas         = canvas;
  self.ctx            = ctx;
  self.status.loading = false;

  self.divCanvas.innerHTML = '';
  self.divCanvas.appendChild(self.canvas);
  self.divCanvas.style.width = imgSize.width + 'px';
  self.divCanvas.style.height = imgSize.height + 'px';
  return null;
};

window.OnmPhotoEditor.prototype.brightness = function(canvasOriginal, canvasDest, brightnessAdj) {
  var adjust     = brightnessAdj / 1.001;
  var pixels     = canvasOriginal.ctx.getImageData(0, 0, canvasOriginal.canvas.width, canvasOriginal.canvas.height);
  var pixelsData = pixels.data;

  for (var i = 0; i < pixelsData.length; i += 4) {
    pixelsData[i] += adjust;
    pixelsData[i + 1] += adjust;
    pixelsData[i + 2] += adjust;
  }
  canvasDest.putImageData(pixels, 0, 0);
};

window.OnmPhotoEditor.prototype.contrast = function(canvasOriginal, canvasDest, contrastAdj) {
  var adjust     = contrastAdj * 2.55;
  var factor = (255 + adjust) / (255.01 - adjust);
  var pixels     = canvasOriginal.ctx.getImageData(0, 0, canvasOriginal.canvas.width, canvasOriginal.canvas.height);
  var pixelsData = pixels.data;

  for (var i = 0; i < pixelsData.length; i += 4) {
    // R G B values. 0-255
    pixelsData[i]     = factor * (pixelsData[i] - 128) + 128;
    pixelsData[i + 1] = factor * (pixelsData[i + 1] - 128) + 128;
    pixelsData[i + 2] = factor * (pixelsData[i + 2] - 128) + 128;
  }
  canvasDest.putImageData(pixels, 0, 0);
};

window.OnmPhotoEditor.prototype.getCanvas2Show = function(canvasOriginal, statusImage) {
  var canvasAux = this.crop(canvasOriginal, statusImage);

  canvasAux = this.mirror(canvasAux.canvas, statusImage);
  canvasAux = this.rotate(canvasAux.canvas, statusImage);
  this.brightness(canvasAux, canvasAux.ctx, statusImage.brightness);

  var sizeRotate  = this.getAdaptCanvasSize(canvasAux.canvas);
  var canvas2Show = { canvas: document.createElement('canvas') };

  canvas2Show.canvas.width  = sizeRotate.width;
  canvas2Show.canvas.height = sizeRotate.height;
  canvas2Show.ctx = canvas2Show.canvas.getContext('2d');
  canvas2Show.ctx.drawImage(canvasAux.canvas, 0, 0, sizeRotate.width, sizeRotate.height);

  return canvas2Show;
};

window.OnmPhotoEditor.prototype.rotate = function(canvasOriginal, statusImage) {
  // Check the canvas orientation for change width for height of the original
  var sizeRotate     = statusImage.rotation % 180 === 0 ?
    { width: canvasOriginal.width, height: canvasOriginal.height } :
    { width: canvasOriginal.height, height: canvasOriginal.width };

  var canvasAux    = document.createElement('canvas');

  canvasAux.width  = sizeRotate.width;
  canvasAux.height = sizeRotate.height;
  var ctxAux       = canvasAux.getContext('2d');

  ctxAux.save();
  ctxAux.translate(sizeRotate.width / 2, sizeRotate.height / 2);
  ctxAux.rotate(statusImage.rotation * Math.PI / 180);
  ctxAux.drawImage(
    canvasOriginal,
    -canvasOriginal.width / 2,
    -canvasOriginal.height / 2,
    canvasOriginal.width,
    canvasOriginal.height
  );
  ctxAux.restore();

  return { canvas: canvasAux, ctx: ctxAux };
};

window.OnmPhotoEditor.prototype.mirror = function(canvasOriginal, statusImage) {
  var mirrorCanvas = document.createElement('canvas');

  mirrorCanvas.width  = canvasOriginal.width;
  mirrorCanvas.height = canvasOriginal.height;

  var ctxAux = mirrorCanvas.getContext('2d');

  ctxAux.save();
  ctxAux.translate(
    statusImage.mirror.h === -1 ? mirrorCanvas.width : 0,
    statusImage.mirror.v === -1 ? mirrorCanvas.height : 0
  );
  ctxAux.scale(statusImage.mirror.h, statusImage.mirror.v);

  ctxAux.drawImage(
    canvasOriginal,
    0,
    0,
    canvasOriginal.width,
    canvasOriginal.height
  );
  ctxAux.restore();

  return { canvas: mirrorCanvas, ctx: ctxAux };
};

window.OnmPhotoEditor.prototype.crop = function(canvasOriginal, statusImage) {
  if (!statusImage.cropSizes || statusImage.cropSizes === null) {
    return { canvas: canvasOriginal, ctx: canvasOriginal.getContext('2d') };
  }

  var cropSizes = statusImage.cropSizes;
  var cropCanvas = document.createElement('canvas');

  cropCanvas.width  = cropSizes.width;
  cropCanvas.height = cropSizes.height;

  var ctxAux = cropCanvas.getContext('2d');

  ctxAux.drawImage(
    canvasOriginal,
    cropSizes.marginLeft,
    cropSizes.marginTop,
    cropSizes.width,
    cropSizes.height,
    0,
    0,
    cropSizes.width,
    cropSizes.height
  );

  return { canvas: cropCanvas, ctx: ctxAux };
};

// UTILS
/**
 * @function getActionList
 * @memberof OnmPhotoEditor
 *
 * @description
 *   Method to retrieve the list of actions enable for the photo editor
 *
 * @return {object.<string, mixed>} actionList - List of all actions we can do. The key is the displays
 *           availables for this template and the values are the list of actions or displays for the display
 */
window.OnmPhotoEditor.prototype.getActionList = function(status) {
  var actionList = null;

  actionList = this.conf.template &&
    this.conf.template !== '' &&
    this.hasOwnProperty(this.conf.template) ?
    this[this.conf.template] :
    actionList = this.TEMPLATE_BASIC;

  this.getMultiSelectValues(status, actionList);
  return actionList;
};

window.OnmPhotoEditor.prototype.getMultiSelectValues = function(status, actionList) {
  for (var action in actionList) {
    for (var i = actionList[action].length - 1; i !== -1; i--) {
      if (Array.isArray(actionList[action][i])) {
        this.loadMultiSelected(actionList[action][i], status);
      }
    }
  }
};

window.OnmPhotoEditor.prototype.loadMultiSelected = function(actionElement, status) {
  for (var j = actionElement.length - 1; j !== -1; j--) {
    if ('default' in actionElement[j]) {
      status.multiSelect[actionElement[j].action] = actionElement[j].value;
      break;
    }
  }
};

/**
 * @function capitalizeFirstLetter
 * @memberof OnmPhotoEditor
 *
 * @description
 *   Method for capitalize the first letter of a word
 *
 * @param {string} string - value the word to capitalize
 *
 * @return {string} the word capitalized
 */
window.OnmPhotoEditor.prototype.capitalizeFirstLetter = function(string) {
  if (!string) {
    return string;
  }
  return string.charAt(0).toUpperCase() + string.slice(1);
};

/**
 * @function getCanvasSize
 * @memberof OnmPhotoEditor
 *
 * @description
 *   Calculates the size of the image to fit into the div
 *
 * @param {element} element   - element to resize
 * @param {element} container - element to contain the parent
 *
 * @return {object} resize element
 */
window.OnmPhotoEditor.prototype.getAdaptCanvasSize = function(element) {
  var sizeElement = null;

  if (!element.tagName) {
    sizeElement = element;
  } else {
    sizeElement = this.getElementSize(element);
  }

  var sizeContainer = {
    height: this.maximunSize.height -
      this.divMenu.getBoundingClientRect().height -
      this.divTopMenu.getBoundingClientRect().height,
    width: this.divTopMenu.getBoundingClientRect().width
  };

  if (sizeContainer.height >= sizeElement.height && sizeContainer.width >= sizeElement.width) {
    return sizeElement;
  }

  var ratio = this.getElementRatio(element);

  if (sizeContainer.width < sizeElement.width) {
    sizeElement = {
      height: Math.round(sizeContainer.width / ratio),
      width: sizeContainer.width
    };
  }

  if (sizeContainer.height < sizeElement.height) {
    sizeElement = {
      height: sizeContainer.height,
      width: Math.round(sizeContainer.height * ratio)
    };
  }
  return sizeElement;
};

window.OnmPhotoEditor.prototype.getMaxSize = function() {
  var maxSize = this.conf.maximunSize ? this.conf.maximunSize : this.maximunSize;

  if (maxSize.width && maxSize.width > 0 && maxSize.height && maxSize.height > 0) {
    return maxSize;
  }
  var containerSize = this.getElementSize(this.container);

  if (this.container.offsetWidth !== '') {
    maxSize.width = containerSize.width;
  }

  if (this.container.offsetHeight !== '') {
    maxSize.height = containerSize.height;
  }
  return maxSize;
};

window.OnmPhotoEditor.prototype.getElementSize = function(element) {
  var tagName = element.tagName.toLowerCase();

  if (tagName !== 'div' && tagName !== 'canvas' && tagName !== 'img') {
    return null;
  }

  if (tagName === 'div') {
    return { height: element.clientHeight, width: element.clientWidth };
  }

  return { height: element.height, width: element.width };
};

window.OnmPhotoEditor.prototype.getElementRatio = function(element) {
  var sizeElement = this.getElementSize(element);

  return sizeElement.width / sizeElement.height;
};

window.OnmPhotoEditor.prototype.getEleResize = function(degrees) {
  // Var positive = parseInt(degrees) > 0;
  return degrees;
};

window.OnmPhotoEditor.prototype.getEleResizeRatio = function(oldSize, newSize) {
  return { widthRatio: newSize.width / oldSize.width, heightRatio: newSize.height / oldSize.height };
};

window.OnmPhotoEditor.prototype.getPosition = function(tagName, classVal, option) {
  // Element to return
  var position = document.createElement(tagName);

  // Content info of the element
  var content = '';

  if (!option.icon) {
    classVal = classVal !== null ? classVal + ' noicon' : 'noicon';
  }

  if (classVal !== null) {
    position.setAttribute('class', classVal);
  }

  if (option.icon) {
    content = '<i class="fa fa-' + this.ACTION_ICONS[option.icon] + '" aria-hidden="true"></i>';
  }

  if (option.text) {
    content += option.text;
  }

  var actionMethodName = 'call' + this.capitalizeFirstLetter(option.action);

  if (option.action in this.actionList || typeof this[actionMethodName] === 'function') {
    var a = document.createElement('a');

    a.innerHTML = content;
    a.href      = '#' + option.action + (option.value ? ',' + option.value : '');
    a.addEventListener('click', this.callAction.bind(this));
    position.appendChild(a);
    return position;
  }

  position.innerHTML = content;
  return position;
};

window.OnmPhotoEditor.prototype.callShowSubmenu = function(e) {
  var menu             = e.currentTarget;
  var removeSelSubmenu = null;

  menu.parentElement.setAttribute('class', 'subSelected');

  removeSelSubmenu = function() {
    var menuSelected = document.querySelector('.photoEditor .menu .subSelected');

    menuSelected.removeAttribute('class');
    document.body.removeEventListener('click', removeSelSubmenu, true);
  };
  document.body.addEventListener('click', removeSelSubmenu, true);
  return false;
};

window.OnmPhotoEditor.prototype.initResize = function(e) {
  var resize        = null;
  var stopResize    = null;
  var top           = e.currentTarget.getAttribute('class') === 'photoEditorCircle topCircle';
  var resizeElement = e.currentTarget.parentElement;
  var minSizeCrop   = this.getElementSize(document.querySelector('.photoEditor .divCanvas .buttonApply'));
  var ratioSelEle   = this.getSelectedRatio();
  var limits        = this.getLimits(this.canvas, resizeElement, minSizeCrop, top, ratioSelEle);
  var that          = this;

  // Increase minimal magin size for the apply button
  minSizeCrop.height = minSizeCrop.height + 5;
  minSizeCrop.width  = minSizeCrop.width + 5;

  resize = function(e) {
    that.resizeMovingCorner({ x: e.clientX, y: e.clientY }, that.canvas, resizeElement, limits, top, ratioSelEle);
  };

  stopResize = function() {
    window.removeEventListener('mousemove', resize, false);
    window.removeEventListener('mouseup', stopResize, false);
  };

  window.addEventListener('mousemove', resize, false);
  window.addEventListener('mouseup', stopResize, false);
};

window.OnmPhotoEditor.prototype.resizeMovingCorner = function(cornerPoint, container, resizeEle, limits, top, ratio) {
  var position = this.checkLimits(cornerPoint, limits, ratio);

  if (position === null) {
    var ratioAux = ratio !== null ? ratio : this.getSelectedRatio();

    position = this.getPositionWithRatio(cornerPoint, resizeEle, top, ratioAux);
    var positionAux = this.checkLimits(position, limits, ratio);

    if (positionAux !== null) {
      position = positionAux;
    }
  }

  var newSize  = this.getSizeValues(position, resizeEle, top);

  if (top) {
    var resizeVP               = resizeEle.getBoundingClientRect();
    var containerVP            = this.canvas.getBoundingClientRect();

    resizeEle.style.marginTop  = resizeVP.bottom - newSize.height - containerVP.top + 'px';
    resizeEle.style.marginLeft = resizeVP.right - newSize.width - containerVP.left + 'px';
  }

  resizeEle.style.width  = newSize.width + 'px';
  resizeEle.style.height = newSize.height + 'px';
};

window.OnmPhotoEditor.prototype.getSizeValues = function(cornerPos, resizeEle, isTopCorner) {
  var resizeVP = resizeEle.getBoundingClientRect();
  var size     = {};

  size.width  = isTopCorner ? resizeVP.right - cornerPos.x : cornerPos.x - resizeVP.left;
  size.height = isTopCorner ? resizeVP.bottom - cornerPos.y : cornerPos.y - resizeVP.top;
  return size;
};

window.OnmPhotoEditor.prototype.getSelectedRatio = function(status) {
  var statusAux = status === null || typeof status === 'undefined' ?
    this.status :
    status;

  if (
    !statusAux.multiSelect ||
    statusAux.multiSelect.ratio === 'free' ||
    statusAux.multiSelect.ratio === null
  ) {
    return null;
  }

  var ratioElements = statusAux.multiSelect.ratio.split(':');

  return statusAux.multiSelect.orientation !== 'portrait' ?
    ratioElements[0] / ratioElements[1] :
    ratioElements[1] / ratioElements[0];
};

window.OnmPhotoEditor.prototype.getLimits = function(containerEle, resizeEle, minSize, top, ratio) {
  var containerVP = containerEle.getBoundingClientRect();
  var resizeEleVP = resizeEle.getBoundingClientRect();

  var absoluteLimit = {};

  absoluteLimit.heightMin = top ? containerVP.top : resizeEleVP.top + minSize.height;
  absoluteLimit.heightMax = top ? resizeEleVP.bottom - minSize.height : containerVP.bottom;
  absoluteLimit.widthMin  = top ? containerVP.left : resizeEleVP.left + minSize.width;
  absoluteLimit.widthMax  = top ? resizeEleVP.right - minSize.width : containerVP.right;

  if (ratio === 0 || ratio === null) {
    return absoluteLimit;
  }

  // Limits with the height
  var heightLimits = {
    heightMin: absoluteLimit.heightMin,
    heightMax: absoluteLimit.heightMax
  };

  // Limits with the width
  var widthLimits  = {
    widthMin: absoluteLimit.widthMin,
    widthMax: absoluteLimit.widthMax
  };

  var posAux = null;

  /**
   *  Calculate the limit with the height and with the width. We will use the
   * bigger one
   */
  if (top) {
    posAux = { x: widthLimits.widthMin, y: heightLimits.heightMin };
    heightLimits.widthMin = this.getBottomPosWHeight(posAux, resizeEleVP, ratio).x;

    widthLimits.heightMin = this.getBottomPosWWidth(posAux, resizeEleVP, ratio).y;

    /**
     *  If the width go out of the absolute values or if with the height limit is bigger than height and isn't out of
     * he absolute limit then the heightLimit is our value
     */
    if (widthLimits.widthMin < absoluteLimit.widthMin ||
      widthLimits.heightMin < absoluteLimit.heightMin
    ) {
      widthLimits.widthMin  = heightLimits.widthMin;
      widthLimits.heightMin = heightLimits.heightMin;
    } else {
      heightLimits.widthMin  = widthLimits.widthMin;
      heightLimits.heightMin = widthLimits.heightMin;
    }

    posAux = { x: widthLimits.widthMax, y: heightLimits.heightMax };
    heightLimits.widthMax  = this.getTopPosWHeight(posAux, resizeEleVP, ratio).x;

    widthLimits.heightMax = this.getTopPosWWidth(posAux, resizeEleVP, ratio).y;

    return widthLimits.widthMax > absoluteLimit.widthMax ||
      widthLimits.heightMax > absoluteLimit.widthMax ?
      widthLimits :
      heightLimits;
  }

  posAux = { x: widthLimits.widthMin, y: heightLimits.heightMin };
  heightLimits.widthMin  = this.getTopPosWHeight(posAux, resizeEleVP, ratio).x;

  widthLimits.heightMin = this.getTopPosWWidth(posAux, resizeEleVP, ratio).y;

  if (widthLimits.widthMin < absoluteLimit.widthMin ||
    widthLimits.heightMin < absoluteLimit.heightMin
  ) {
    heightLimits.widthMin  = widthLimits.widthMin;
    heightLimits.heightMin = widthLimits.heightMin;
  } else {
    widthLimits.widthMin  = heightLimits.widthMin;
    widthLimits.heightMin = heightLimits.heightMin;
  }

  posAux = { x: widthLimits.widthMax, y: heightLimits.heightMax };

  heightLimits.widthMax = this.getBottomPosWHeight(posAux, resizeEleVP, ratio).x;

  widthLimits.heightMax = this.getBottomPosWWidth(posAux, resizeEleVP, ratio).y;

  return widthLimits.widthMax > absoluteLimit.widthMax ||
    widthLimits.heightMax > absoluteLimit.heightMax ?
    heightLimits :
    widthLimits;
};

window.OnmPhotoEditor.prototype.checkLimits = function(cornerPos, limits, ratio) {
  if (ratio === 'free' || ratio === null) {
    var cornerPosAux = {};

    cornerPosAux.x = cornerPos.x < limits.widthMin ? limits.widthMin : cornerPos.x;
    cornerPosAux.y = cornerPos.y < limits.heightMin ? limits.heightMin : cornerPos.y;

    if (cornerPosAux.x > limits.widthMax) {
      cornerPosAux.x = limits.widthMax;
    }

    if (cornerPosAux.y > limits.heightMax) {
      cornerPosAux.y = limits.heightMax;
    }
    return cornerPosAux;
  }

  if (cornerPos.x < limits.widthMin || cornerPos.y < limits.heightMin) {
    return { x: limits.widthMin, y: limits.heightMin };
  }

  if (cornerPos.x > limits.widthMax || cornerPos.y > limits.heightMax) {
    return { x: limits.widthMax, y: limits.heightMax };
  }

  return null;
};

window.OnmPhotoEditor.prototype.getPositionWithRatio = function(cornerPos, resizeEle, isTopCorner, ratio) {
  if (ratio === null || ratio === 0) {
    return cornerPos;
  }

  var resizeEleVP = resizeEle.getBoundingClientRect();

  var lateral = this.changeLateral(cornerPos, resizeEleVP, isTopCorner);

  if (isTopCorner) {
    return this['getTopPosW' + lateral](cornerPos, resizeEleVP, ratio);
  }
  return this['getBottomPosW' + lateral](cornerPos, resizeEleVP, ratio);
};

window.OnmPhotoEditor.prototype.changeLateral = function(cornerPos, resizeEleVP, top) {
  var relPos = top ? { x: resizeEleVP.left, y: resizeEleVP.top } : { x: resizeEleVP.right, y: resizeEleVP.bottom };

  var widthDist  = top ? relPos.x - cornerPos.x : cornerPos.x - relPos.x;
  var heightDist = top ? relPos.y - cornerPos.y : cornerPos.y - relPos.y;

  if (Math.abs(widthDist) > Math.abs(heightDist)) {
    return 'Height';
  }

  return 'Width';
};

window.OnmPhotoEditor.prototype.getTopPosWHeight = function(cornerPos, resizeEleVP, ratio) {
  var height =  resizeEleVP.bottom - cornerPos.y;

  return { y: cornerPos.y, x: resizeEleVP.right - Math.round(height * ratio) };
};

window.OnmPhotoEditor.prototype.getBottomPosWWidth = function(cornerPos, resizeEleVP, ratio) {
  var width  = cornerPos.x - resizeEleVP.left;
  var height = Math.round(width / ratio);

  return { y: resizeEleVP.top + height, x: cornerPos.x };
};

window.OnmPhotoEditor.prototype.getTopPosWWidth = function(cornerPos, resizeEleVP, ratio) {
  var width  = resizeEleVP.right - cornerPos.x;
  var height = Math.round(width / ratio);

  return { y: resizeEleVP.bottom - height, x: cornerPos.x };
};

window.OnmPhotoEditor.prototype.getBottomPosWHeight = function(cornerPos, resizeEleVP, ratio) {
  var height =  cornerPos.y - resizeEleVP.top;

  return { y: cornerPos.y, x: resizeEleVP.left + Math.round(height * ratio) };
};

window.OnmPhotoEditor.prototype.resizeCropDiv = function(status) {
  var resizeEle = document.querySelector('.photoEditor .divCanvas .photoEditorCrop');
  var resizeVP  = resizeEle.getBoundingClientRect();
  var minSize   = this.getElementSize(document.querySelector('.photoEditor .divCanvas .buttonApply'));
  var ratio     = this.getSelectedRatio(status);
  var limits    = this.getLimits(this.canvas, resizeEle, minSize, false, ratio);

  // Increase minimal magin size for the apply button
  minSize.height = minSize.height + 5;
  minSize.width  = minSize.width + 5;

  this.resizeMovingCorner({ x: resizeVP.right, y: resizeVP.bottom }, this.canvas, resizeEle, limits, false, ratio);
};

window.OnmPhotoEditor.prototype.rotateAndCropWithDiv = function(statusImage, cropDiv, cropContainer) {
  var cropPosition = {};
  var cropContRotateSize = {};

  if (statusImage.rotation % 180 === 0) {
    cropPosition.width  = cropDiv.width;
    cropPosition.height = cropDiv.height;
    cropContRotateSize.width  = cropContainer.width;
    cropContRotateSize.height = cropContainer.height;

    if (statusImage.rotation === 0 && statusImage.mirror.h === 1 ||
      statusImage.rotation !== 0 && statusImage.mirror.h !== 1
    ) {
      cropPosition.marginTop  = cropDiv.top - cropContainer.top;
      cropPosition.marginLeft = cropDiv.left - cropContainer.left;
    } else {
      cropPosition.marginTop  = cropContainer.bottom - cropDiv.bottom;
      cropPosition.marginLeft = cropContainer.right - cropDiv.right;
    }
  } else {
    cropPosition.width  = cropDiv.height;
    cropPosition.height = cropDiv.width;
    cropContRotateSize.width  = cropContainer.height;
    cropContRotateSize.height = cropContainer.width;

    var degreesConversion = statusImage.rotation;

    if (Math.abs(statusImage.rotation) > 180) {
      degreesConversion = -1 * statusImage.rotation - (statusImage.rotation > 0 ? -180 : 180);
    }

    if (
      degreesConversion === 90 && statusImage.mirror.h === 1 ||
      degreesConversion !== 90 && statusImage.mirror.h !== 1
    ) {
      cropPosition.marginTop  = cropContainer.right - cropDiv.right;
      cropPosition.marginLeft = cropDiv.top - cropContainer.top;
    } else {
      cropPosition.marginTop  = cropDiv.left - cropContainer.left;
      cropPosition.marginLeft = cropContainer.bottom - cropDiv.bottom;
    }
  }

  var referenceElement = statusImage.cropSizes && statusImage.cropSizes !== null ?
    statusImage.cropSizes :
    this.getElementSize(statusImage.canvasOriginal);
  var resizeRatio = this.getEleResizeRatio(cropContRotateSize, referenceElement);

  cropPosition.width      = cropPosition.width * resizeRatio.widthRatio;
  cropPosition.height     = cropPosition.height * resizeRatio.heightRatio;
  cropPosition.marginTop  = cropPosition.marginTop * resizeRatio.heightRatio;
  cropPosition.marginLeft = cropPosition.marginLeft * resizeRatio.widthRatio;

  if (statusImage.cropSizes && statusImage.cropSizes !== null) {
    cropPosition.marginTop  += statusImage.cropSizes.marginTop;
    cropPosition.marginLeft += statusImage.cropSizes.marginLeft;
  }

  return cropPosition;
};

window.OnmPhotoEditor.prototype.copyStatusImage = function(statusImage) {
  var newStatus = {};

  for (var property in statusImage) {
    if (property !== 'canvasOriginal' && property !== 'ctxOriginal') {
      newStatus[property] = statusImage[property];
    }
  }
  newStatus = JSON.parse(JSON.stringify(newStatus));

  return newStatus;
};
