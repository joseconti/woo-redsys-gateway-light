/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 20
(__unused_webpack_module, exports, __webpack_require__) {

var __webpack_unused_export__;
/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */
var f=__webpack_require__(609),k=Symbol.for("react.element"),l=Symbol.for("react.fragment"),m=Object.prototype.hasOwnProperty,n=f.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,p={key:!0,ref:!0,__self:!0,__source:!0};
function q(c,a,g){var b,d={},e=null,h=null;void 0!==g&&(e=""+g);void 0!==a.key&&(e=""+a.key);void 0!==a.ref&&(h=a.ref);for(b in a)m.call(a,b)&&!p.hasOwnProperty(b)&&(d[b]=a[b]);if(c&&c.defaultProps)for(b in a=c.defaultProps,a)void 0===d[b]&&(d[b]=a[b]);return{$$typeof:k,type:c,key:e,ref:h,props:d,_owner:n.current}}__webpack_unused_export__=l;exports.jsx=q;exports.jsxs=q;


/***/ },

/***/ 848
(module, __unused_webpack_exports, __webpack_require__) {



if (true) {
  module.exports = __webpack_require__(20);
} else // removed by dead control flow
{}


/***/ },

/***/ 609
(module) {

module.exports = window["React"];

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};

;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// external ["wc","wcBlocksRegistry"]
const external_wc_wcBlocksRegistry_namespaceObject = window["wc"]["wcBlocksRegistry"];
;// external ["wp","htmlEntities"]
const external_wp_htmlEntities_namespaceObject = window["wp"]["htmlEntities"];
;// external ["wc","wcSettings"]
const external_wc_wcSettings_namespaceObject = window["wc"]["wcSettings"];
// EXTERNAL MODULE: ./node_modules/react/jsx-runtime.js
var jsx_runtime = __webpack_require__(848);
;// ./resources/js/frontend/index.js





const settings = (0,external_wc_wcSettings_namespaceObject.getSetting)('redsys_data', {});
const settingsbizumredsys = (0,external_wc_wcSettings_namespaceObject.getSetting)('bizumredsys_data', {});
const settingsgpayredsys = (0,external_wc_wcSettings_namespaceObject.getSetting)('googlepayredirecredsys_data', {});
const settingsinespayredsys = (0,external_wc_wcSettings_namespaceObject.getSetting)('inespayredsys_data', {});
const defaultLabel = (0,external_wp_i18n_namespaceObject.__)('Redsys', 'woo-redsys-gateway-light');
const defaultLabelBizum = (0,external_wp_i18n_namespaceObject.__)('Bizum', 'woo-redsys-gateway-light');
const defaultLabelGpayRed = (0,external_wp_i18n_namespaceObject.__)('Google Pay', 'woo-redsys-gateway-light');
const defaultLabelInespay = (0,external_wp_i18n_namespaceObject.__)('Inespay Bank Transfer', 'woo-redsys-gateway-light');
const label = (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(settings.title) || defaultLabel;
const labelbizum = (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(settingsbizumredsys.title) || defaultLabelBizum;
const labelgpayred = (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(settingsgpayredsys.title) || defaultLabelGpayRed;
const labelinespay = (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(settingsinespayredsys.title) || defaultLabelInespay;
/**
 * Content component
 */
const Content = () => {
  return (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(settings.description || '');
};
const Contentbizum = () => {
  return (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(settingsbizumredsys.description || '');
};
const Contengpayred = () => {
  return (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(settingsgpayredsys.description || '');
};
const Contentinespay = () => {
  return (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(settingsinespayredsys.description || '');
};
/**
 * Label component
 *
 * @param {*} props Props from payment API.
 */
const Label = props => {
  const {
    PaymentMethodLabel
  } = props.components;
  const icon = settings.icon;
  return /*#__PURE__*/(0,jsx_runtime.jsxs)("div", {
    style: {
      display: 'flex',
      alignItems: 'center'
    },
    children: [/*#__PURE__*/(0,jsx_runtime.jsx)(PaymentMethodLabel, {
      text: label
    }), icon && /*#__PURE__*/(0,jsx_runtime.jsx)("img", {
      src: icon,
      alt: label,
      style: {
        marginLeft: '8px',
        maxHeight: '24px'
      }
    })]
  });
};
const Labelbizum = props => {
  const {
    PaymentMethodLabel
  } = props.components;
  const icon = settingsbizumredsys.icon;
  return /*#__PURE__*/(0,jsx_runtime.jsxs)("div", {
    style: {
      display: 'flex',
      alignItems: 'center'
    },
    children: [/*#__PURE__*/(0,jsx_runtime.jsx)(PaymentMethodLabel, {
      text: labelbizum
    }), icon && /*#__PURE__*/(0,jsx_runtime.jsx)("img", {
      src: icon,
      alt: labelbizum,
      style: {
        marginLeft: '8px',
        maxHeight: '24px'
      }
    })]
  });
};
const Labelgpayred = props => {
  const {
    PaymentMethodLabel
  } = props.components;
  const icon = settingsgpayredsys.icon;
  return /*#__PURE__*/(0,jsx_runtime.jsxs)("div", {
    style: {
      display: 'flex',
      alignItems: 'center'
    },
    children: [/*#__PURE__*/(0,jsx_runtime.jsx)(PaymentMethodLabel, {
      text: labelgpayred
    }), icon && /*#__PURE__*/(0,jsx_runtime.jsx)("img", {
      src: icon,
      alt: labelgpayred,
      style: {
        marginLeft: '8px',
        maxHeight: '24px'
      }
    })]
  });
};
const Labelinespay = props => {
  const {
    PaymentMethodLabel
  } = props.components;
  const icon = settingsinespayredsys.icon;
  return /*#__PURE__*/(0,jsx_runtime.jsxs)("div", {
    style: {
      display: 'flex',
      alignItems: 'center'
    },
    children: [/*#__PURE__*/(0,jsx_runtime.jsx)(PaymentMethodLabel, {
      text: labelinespay
    }), icon && /*#__PURE__*/(0,jsx_runtime.jsx)("img", {
      src: icon,
      alt: labelinespay,
      style: {
        marginLeft: '8px',
        maxHeight: '24px'
      }
    })]
  });
};

/**
 * Dummy payment method config object.
 */
const Redsys = {
  name: "redsys",
  label: /*#__PURE__*/(0,jsx_runtime.jsx)(Label, {}),
  content: /*#__PURE__*/(0,jsx_runtime.jsx)(Content, {}),
  edit: /*#__PURE__*/(0,jsx_runtime.jsx)(Content, {}),
  canMakePayment: () => true,
  ariaLabel: label,
  supports: {
    features: settings.supports
  }
};
const Bizum = {
  name: "bizumredsys",
  label: /*#__PURE__*/(0,jsx_runtime.jsx)(Labelbizum, {}),
  content: /*#__PURE__*/(0,jsx_runtime.jsx)(Contentbizum, {}),
  edit: /*#__PURE__*/(0,jsx_runtime.jsx)(Contentbizum, {}),
  canMakePayment: () => true,
  ariaLabel: labelbizum,
  supports: {
    features: settingsbizumredsys.supports
  }
};
const GPayRed = {
  name: "googlepayredirecredsys",
  label: /*#__PURE__*/(0,jsx_runtime.jsx)(Labelgpayred, {}),
  content: /*#__PURE__*/(0,jsx_runtime.jsx)(Contengpayred, {}),
  edit: /*#__PURE__*/(0,jsx_runtime.jsx)(Contengpayred, {}),
  canMakePayment: () => true,
  ariaLabel: labelgpayred,
  supports: {
    features: settingsgpayredsys.supports
  }
};
const Inespay = {
  name: "inespayredsys",
  label: /*#__PURE__*/(0,jsx_runtime.jsx)(Labelinespay, {}),
  content: /*#__PURE__*/(0,jsx_runtime.jsx)(Contentinespay, {}),
  edit: /*#__PURE__*/(0,jsx_runtime.jsx)(Contentinespay, {}),
  canMakePayment: () => true,
  ariaLabel: labelinespay,
  supports: {
    features: settingsinespayredsys.supports
  }
};
(0,external_wc_wcBlocksRegistry_namespaceObject.registerPaymentMethod)(Redsys);
(0,external_wc_wcBlocksRegistry_namespaceObject.registerPaymentMethod)(Bizum);
(0,external_wc_wcBlocksRegistry_namespaceObject.registerPaymentMethod)(GPayRed);
(0,external_wc_wcBlocksRegistry_namespaceObject.registerPaymentMethod)(Inespay);
/******/ })()
;