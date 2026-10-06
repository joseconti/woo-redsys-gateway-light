/******/ (() => { // webpackBootstrap
/******/ 	"use strict";

;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// external ["wc","wcBlocksRegistry"]
const external_wc_wcBlocksRegistry_namespaceObject = window["wc"]["wcBlocksRegistry"];
;// external ["wp","htmlEntities"]
const external_wp_htmlEntities_namespaceObject = window["wp"]["htmlEntities"];
;// external ["wc","wcSettings"]
const external_wc_wcSettings_namespaceObject = window["wc"]["wcSettings"];
;// ./resources/js/frontend/index.js
/**
 * JSX is compiled to createElement() from @wordpress/element on purpose.
 *
 * The build tools' default (the automatic JSX runtime) makes the script
 * depend on the `react-jsx-runtime` handle, which WordPress only registers
 * from 6.6 on: on an older WordPress the script would not load and the
 * gateways would vanish from the Blocks checkout. `wp-element` exists on
 * every WordPress that has blocks and uses the site's own React.
 *
 * @jsxRuntime classic
 * @jsx createElement
 */





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
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    style: {
      display: 'flex',
      alignItems: 'center'
    }
  }, (0,external_wp_element_namespaceObject.createElement)(PaymentMethodLabel, {
    text: label
  }), icon && (0,external_wp_element_namespaceObject.createElement)("img", {
    src: icon,
    alt: label,
    style: {
      marginLeft: '8px',
      maxHeight: '24px'
    }
  }));
};
const Labelbizum = props => {
  const {
    PaymentMethodLabel
  } = props.components;
  const icon = settingsbizumredsys.icon;
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    style: {
      display: 'flex',
      alignItems: 'center'
    }
  }, (0,external_wp_element_namespaceObject.createElement)(PaymentMethodLabel, {
    text: labelbizum
  }), icon && (0,external_wp_element_namespaceObject.createElement)("img", {
    src: icon,
    alt: labelbizum,
    style: {
      marginLeft: '8px',
      maxHeight: '24px'
    }
  }));
};
const Labelgpayred = props => {
  const {
    PaymentMethodLabel
  } = props.components;
  const icon = settingsgpayredsys.icon;
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    style: {
      display: 'flex',
      alignItems: 'center'
    }
  }, (0,external_wp_element_namespaceObject.createElement)(PaymentMethodLabel, {
    text: labelgpayred
  }), icon && (0,external_wp_element_namespaceObject.createElement)("img", {
    src: icon,
    alt: labelgpayred,
    style: {
      marginLeft: '8px',
      maxHeight: '24px'
    }
  }));
};
const Labelinespay = props => {
  const {
    PaymentMethodLabel
  } = props.components;
  const icon = settingsinespayredsys.icon;
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    style: {
      display: 'flex',
      alignItems: 'center'
    }
  }, (0,external_wp_element_namespaceObject.createElement)(PaymentMethodLabel, {
    text: labelinespay
  }), icon && (0,external_wp_element_namespaceObject.createElement)("img", {
    src: icon,
    alt: labelinespay,
    style: {
      marginLeft: '8px',
      maxHeight: '24px'
    }
  }));
};

/**
 * Dummy payment method config object.
 */
const Redsys = {
  name: "redsys",
  label: (0,external_wp_element_namespaceObject.createElement)(Label, null),
  content: (0,external_wp_element_namespaceObject.createElement)(Content, null),
  edit: (0,external_wp_element_namespaceObject.createElement)(Content, null),
  canMakePayment: () => true,
  ariaLabel: label,
  supports: {
    features: settings.supports
  }
};
const Bizum = {
  name: "bizumredsys",
  label: (0,external_wp_element_namespaceObject.createElement)(Labelbizum, null),
  content: (0,external_wp_element_namespaceObject.createElement)(Contentbizum, null),
  edit: (0,external_wp_element_namespaceObject.createElement)(Contentbizum, null),
  canMakePayment: () => true,
  ariaLabel: labelbizum,
  supports: {
    features: settingsbizumredsys.supports
  }
};
const GPayRed = {
  name: "googlepayredirecredsys",
  label: (0,external_wp_element_namespaceObject.createElement)(Labelgpayred, null),
  content: (0,external_wp_element_namespaceObject.createElement)(Contengpayred, null),
  edit: (0,external_wp_element_namespaceObject.createElement)(Contengpayred, null),
  canMakePayment: () => true,
  ariaLabel: labelgpayred,
  supports: {
    features: settingsgpayredsys.supports
  }
};
const Inespay = {
  name: "inespayredsys",
  label: (0,external_wp_element_namespaceObject.createElement)(Labelinespay, null),
  content: (0,external_wp_element_namespaceObject.createElement)(Contentinespay, null),
  edit: (0,external_wp_element_namespaceObject.createElement)(Contentinespay, null),
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