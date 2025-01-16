(self["webpackChunk"] = self["webpackChunk"] || []).push([["ibexa-connector-ai-action-configuration-list-js"],{

/***/ "./public/bundles/ibexaadminui/js/scripts/button.state.toggle.js":
/*!***********************************************************************!*\
  !*** ./public/bundles/ibexaadminui/js/scripts/button.state.toggle.js ***!
  \***********************************************************************/
/***/ (() => {

function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
(function (global, doc) {
  var toggleForms = doc.querySelectorAll('.ibexa-toggle-btn-state');
  toggleForms.forEach(function (toggleForm) {
    var checkboxes = _toConsumableArray(toggleForm.querySelectorAll('.ibexa-table__cell--has-checkbox .ibexa-input--checkbox'));
    var buttonRemove = doc.querySelector(toggleForm.dataset.toggleButtonId);
    if (!buttonRemove) {
      return;
    }
    var toggleButtonState = function toggleButtonState() {
      var isAnythingSelected = checkboxes.some(function (el) {
        return el.checked;
      });
      buttonRemove.disabled = !isAnythingSelected;
    };
    toggleButtonState();
    checkboxes.forEach(function (checkbox) {
      return checkbox.addEventListener('change', toggleButtonState, false);
    });
  });
})(window, window.document);

/***/ }),

/***/ "./public/bundles/ibexaadminui/js/scripts/filters.action.btns.js":
/*!***********************************************************************!*\
  !*** ./public/bundles/ibexaadminui/js/scripts/filters.action.btns.js ***!
  \***********************************************************************/
/***/ (() => {

function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
(function (global, doc, ibexa) {
  var containers = doc.querySelectorAll('.ibexa-adaptive-filters');
  containers.forEach(function (container) {
    var clearBtn = container.querySelector('.ibexa-adaptive-filters__clear-btn');
    var applyBtn = container.querySelector('.ibexa-adaptive-filters__submit-btn');
    var dropdownNodes = _toConsumableArray(container.querySelectorAll('.ibexa-dropdown'));
    var textInputNodes = _toConsumableArray(container.querySelectorAll('.ibexa-input--text'));
    var dateInputNodes = _toConsumableArray(container.querySelectorAll('.ibexa-input--date'));
    var originalValuesMap = new Map();
    var dropdownSelectionsEqual = function dropdownSelectionsEqual(selection1, selection2) {
      if (selection1.length !== selection2.length) {
        return false;
      }
      for (var i = 0; i < selection1.length; ++i) {
        if (selection1[i] !== selection2[i]) return false;
      }
      return true;
    };
    var checkFieldsValuesChanged = function checkFieldsValuesChanged() {
      return dropdownNodes.some(function (dropdownNode) {
        var dropdown = dropdownNode.ibexaInstance;
        var value = _toConsumableArray(dropdown.getSelectedItems()).map(function (item) {
          return item.value;
        });
        var originalValue = originalValuesMap.get(dropdown);
        return !dropdownSelectionsEqual(value, originalValue);
      }) || textInputNodes.some(function (textInputNode) {
        var value = textInputNode.value;
        var originalValue = originalValuesMap.get(textInputNode);
        return value !== originalValue;
      });
    };
    var checkAreFiltersCleared = function checkAreFiltersCleared() {
      return textInputNodes.every(function (textInputNode) {
        return textInputNode.disabled || textInputNode.value === '';
      }) && dropdownNodes.every(function (dropdownNode) {
        var isDisabled = dropdownNode.classList.contains('ibexa-dropdown--disabled');
        var selectNode = dropdownNode.querySelector('.ibexa-input--select');
        var dropdown = dropdownNode.ibexaInstance;
        return isDisabled || (dropdown.canSelectOnlyOne ? selectNode.selectedIndex === 0 : selectNode.selectedIndex === -1);
      });
    };
    var clearForm = function clearForm() {
      textInputNodes.forEach(function (textInputNode) {
        if (!textInputNode.disabled) {
          textInputNode.value = '';
        }
      });
      dateInputNodes.forEach(function (dateInputNode) {
        if (!dateInputNode.disabled) {
          var datePickerNode = dateInputNode.closest('.ibexa-picker');
          var datePickerInstance = ibexa.helpers.objectInstances.getInstance(datePickerNode);
          datePickerInstance.clear();
        }
      });
      dropdownNodes.forEach(function (dropdownNode) {
        var isDisabled = dropdownNode.classList.contains('ibexa-dropdown--disabled');
        if (!isDisabled) {
          var dropdown = dropdownNode.ibexaInstance;
          if (dropdown.canSelectOnlyOne) {
            dropdown.selectFirstOption();
          } else {
            dropdown.clearCurrentSelection();
          }
        }
      });
    };
    var handleFormClear = function handleFormClear() {
      clearForm();
      clearBtn.disabled = true;
      applyBtn.disabled = !checkFieldsValuesChanged();
      applyBtn.click();
    };
    var handleInputChange = function handleInputChange() {
      clearBtn.disabled = checkAreFiltersCleared();
      applyBtn.disabled = !checkFieldsValuesChanged();
    };
    dropdownNodes.forEach(function (dropdownNode) {
      var dropdown = dropdownNode.ibexaInstance;
      var originalValue = _toConsumableArray(dropdown.getSelectedItems()).map(function (item) {
        return item.value;
      });
      originalValuesMap.set(dropdown, originalValue);
    });
    textInputNodes.forEach(function (textInputNode) {
      var originalValue = textInputNode.value;
      originalValuesMap.set(textInputNode, originalValue);
    });
    if (applyBtn) {
      applyBtn.disabled = true;
    }
    if (clearBtn) {
      clearBtn.disabled = checkAreFiltersCleared();
      clearBtn.addEventListener('click', handleFormClear, false);
    }
    dropdownNodes.forEach(function (dropdownNode) {
      var select = dropdownNode.querySelector('.ibexa-input--select');
      select.addEventListener('change', handleInputChange, false);
    });
    textInputNodes.forEach(function (textInputNode) {
      return textInputNode.addEventListener('input', handleInputChange, false);
    });
  });
})(window, window.document, window.ibexa);

/***/ }),

/***/ "./public/bundles/ibexaadminui/js/scripts/sidebar/extra.actions.js":
/*!*************************************************************************!*\
  !*** ./public/bundles/ibexaadminui/js/scripts/sidebar/extra.actions.js ***!
  \*************************************************************************/
/***/ (() => {

function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
(function (global, doc, ibexa) {
  var CLASS_HIDDEN = 'ibexa-extra-actions--hidden';
  var CLASS_EXPANDED = 'ibexa-context-menu--expanded';
  var CLASS_PREVENT_SHOW = 'ibexa-extra-actions--prevent-show';
  var closeBtns = doc.querySelectorAll('.ibexa-extra-actions .ibexa-btn--close, .ibexa-extra-actions .ibexa-extra-actions__btn--cancel');
  var btns = _toConsumableArray(doc.querySelectorAll('.ibexa-btn--extra-actions'));
  var menu = doc.querySelector('.ibexa-context-menu');
  var backdrop = new ibexa.core.Backdrop();
  var haveHiddenPart = function haveHiddenPart(element) {
    return element.classList.contains(CLASS_HIDDEN) && !element.classList.contains(CLASS_PREVENT_SHOW);
  };
  var removeBackdrop = function removeBackdrop() {
    backdrop.hide();
    doc.body.classList.remove('ibexa-scroll-disabled');
  };
  var closeExtraActions = function closeExtraActions(actions) {
    actions.classList.add(CLASS_HIDDEN);
    if (menu) {
      menu.classList.remove(CLASS_EXPANDED);
    }
    doc.body.dispatchEvent(new CustomEvent('ibexa-extra-actions:after-close'));
    removeBackdrop();
  };
  var toggleExtraActionsWidget = function toggleExtraActionsWidget(widgetData) {
    var actions = doc.querySelector(".ibexa-extra-actions[data-actions=\"".concat(widgetData.actions, "\"]"));
    if (widgetData.validate && !parseInt(widgetData.isFormValid, 10)) {
      return;
    }
    var isHidden = haveHiddenPart(actions);
    var focusElement = actions.querySelector(widgetData.focusElement);
    var _detectClickOutside = function detectClickOutside(event) {
      if (event.target.classList.contains('ibexa-backdrop')) {
        closeExtraActions(actions);
        doc.body.removeEventListener('click', _detectClickOutside, false);
      }
    };
    actions.classList.toggle(CLASS_HIDDEN, !isHidden);
    if (menu) {
      menu.classList.toggle(CLASS_EXPANDED, isHidden);
    }
    if (!actions.classList.contains(CLASS_HIDDEN)) {
      backdrop.show();
      doc.body.addEventListener('click', _detectClickOutside, false);
      doc.body.classList.add('ibexa-scroll-disabled');
    } else {
      doc.body.removeEventListener('click', _detectClickOutside);
      removeBackdrop();
    }
    if (focusElement) {
      focusElement.focus();
    }
  };
  var initExtraActionsWidget = function initExtraActionsWidget(dataset) {
    var hashes = window.location.hash.split('#');
    if (hashes.includes(dataset.actions)) {
      toggleExtraActionsWidget(dataset);
    }
  };
  var hideMenu = function hideMenu(btn) {
    var menuBranch = btn.closest('.ibexa-multilevel-popup-menu__branch');
    if (!(menuBranch !== null && menuBranch !== void 0 && menuBranch.menuInstanceElement)) {
      return;
    }
    var menuInstance = ibexa.helpers.objectInstances.getInstance(menuBranch.menuInstanceElement);
    menuInstance.closeMenu();
  };
  btns.forEach(function (btn) {
    var dataset = btn.dataset;
    btn.addEventListener('click', function () {
      toggleExtraActionsWidget(dataset);
      hideMenu(btn);
    }, false);
    initExtraActionsWidget(dataset);
  });
  doc.body.addEventListener('ibexa-extra-actions:toggle-widget', function (event) {
    return toggleExtraActionsWidget(event.detail);
  }, false);
  closeBtns.forEach(function (closeBtn) {
    return closeBtn.addEventListener('click', function (event) {
      closeExtraActions(event.currentTarget.closest('.ibexa-extra-actions'));
    }, false);
  });
})(window, window.document, window.ibexa);

/***/ }),

/***/ "./vendor/ibexa/admin-ui/src/bundle/Resources/public/js/scripts/helpers/object.instances.js":
/*!**************************************************************************************************!*\
  !*** ./vendor/ibexa/admin-ui/src/bundle/Resources/public/js/scripts/helpers/object.instances.js ***!
  \**************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   clearInstance: () => (/* binding */ clearInstance),
/* harmony export */   getInstance: () => (/* binding */ getInstance),
/* harmony export */   setInstance: () => (/* binding */ setInstance)
/* harmony export */ });
var setInstance = function setInstance(domElement, instance) {
  if (domElement.ibexaInstance) {
    throw new Error('Instance for this DOM element already exists!');
  }
  domElement.ibexaInstance = instance;
};
var getInstance = function getInstance(domElement) {
  return domElement.ibexaInstance;
};
var clearInstance = function clearInstance(domElement) {
  delete domElement.ibexaInstance;
};


/***/ }),

/***/ "./vendor/ibexa/connector-ai/src/bundle/Resources/public/js/extra.actions.js":
/*!***********************************************************************************!*\
  !*** ./vendor/ibexa/connector-ai/src/bundle/Resources/public/js/extra.actions.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ibexa_admin_ui_src_bundle_Resources_public_js_scripts_helpers_object_instances__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/object.instances */ "./vendor/ibexa/admin-ui/src/bundle/Resources/public/js/scripts/helpers/object.instances.js");
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }

(function (global, doc) {
  var createActions = doc.querySelector('.ibexa-extra-actions.ibexa-extra-actions--action-configuration');
  if (!createActions) {
    return;
  }
  var actionTypeDropdown = createActions.querySelector('.ibexa-form-field--action-type .ibexa-dropdown');
  var actionHandlerDropdown = createActions.querySelector('.ibexa-form-field--action-handler .ibexa-dropdown');
  var actionTypeDropdownInstance = (0,_ibexa_admin_ui_src_bundle_Resources_public_js_scripts_helpers_object_instances__WEBPACK_IMPORTED_MODULE_0__.getInstance)(actionTypeDropdown);
  var actionHandlerDropdownInstance = (0,_ibexa_admin_ui_src_bundle_Resources_public_js_scripts_helpers_object_instances__WEBPACK_IMPORTED_MODULE_0__.getInstance)(actionHandlerDropdown);
  var filterActionHandlers = function filterActionHandlers() {
    var actionTypeValue = actionTypeDropdownInstance.sourceInput.value;
    var actionHandlerOptions = actionHandlerDropdownInstance.sourceInput.querySelectorAll("option[data-action-type=\"".concat(actionTypeValue, "\"]"));
    var actionHandlerDropdownItems = actionHandlerDropdownInstance.itemsListContainer.querySelectorAll('.ibexa-dropdown__item');
    var actionHandlerVisibleOptionsIds = _toConsumableArray(actionHandlerOptions).map(function (_ref) {
      var value = _ref.value;
      return value;
    });
    actionHandlerDropdownItems.forEach(function (itemNode) {
      itemNode.hidden = !actionHandlerVisibleOptionsIds.includes(itemNode.dataset.value);
    });
    var firstVisibleItem = actionHandlerDropdownInstance.itemsListContainer.querySelector('.ibexa-dropdown__item:not([hidden])');
    var firstVisibleItemId = firstVisibleItem.dataset.value;
    if (firstVisibleItemId !== actionHandlerDropdownInstance.sourceInput.value) {
      actionHandlerDropdownInstance.selectOption(firstVisibleItemId);
    }
  };
  filterActionHandlers();
  actionTypeDropdownInstance.sourceInput.addEventListener('change', function () {
    filterActionHandlers();
  }, false);
})(window, window.document);

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ var __webpack_exports__ = (__webpack_exec__("./public/bundles/ibexaadminui/js/scripts/sidebar/extra.actions.js"), __webpack_exec__("./public/bundles/ibexaadminui/js/scripts/filters.action.btns.js"), __webpack_exec__("./public/bundles/ibexaadminui/js/scripts/button.state.toggle.js"), __webpack_exec__("./vendor/ibexa/connector-ai/src/bundle/Resources/public/js/extra.actions.js"));
/******/ }
]);