/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./editor/components/BlockIcon/BlockIcon.jsx"
/*!***************************************************!*\
  !*** ./editor/components/BlockIcon/BlockIcon.jsx ***!
  \***************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ BlockIcon)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


function BlockIcon() {
  const DAY = new Date().getDate();
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 20 20",
    width: "20",
    height: "20",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("g", {
      fill: "none",
      stroke: "currentColor",
      "stroke-width": "1.5",
      "stroke-linecap": "round",
      "stroke-linejoin": "round",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("rect", {
        x: "3",
        y: "4.25",
        width: "14",
        height: "12.75",
        rx: "2.4"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
        d: "M3 8.25H17"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
        d: "M6.75 2.75V5.75"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
        d: "M13.25 2.75V5.75"
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("text", {
      x: "10",
      y: "14.7",
      "text-anchor": "middle",
      "font-family": "sans-serif",
      "font-size": "7.4",
      "font-weight": "700",
      fill: "currentColor",
      children: DAY
    })]
  });
}

/***/ },

/***/ "./editor/components/SectionToggle/SectionToggle.jsx"
/*!***********************************************************!*\
  !*** ./editor/components/SectionToggle/SectionToggle.jsx ***!
  \***********************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ SectionToggle)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _icons_CaretIcon_CaretIcon__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../icons/CaretIcon/CaretIcon */ "./editor/components/icons/CaretIcon/CaretIcon.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__);




function SectionToggle({
  title,
  value,
  onChange,
  className = null,
  asTitle = false,
  titleTag = 'h3'
}) {
  const [isCondensed, setIsCondensed] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(value);
  const handleToggle = () => {
    setIsCondensed(prev => {
      const newValue = !prev;
      onChange && onChange(newValue);
      return newValue;
    });
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
    className: `bc-events__section-toggle${isCondensed ? ' closed' : ' open'}`,
    children: [titleTag && titleTag === 'h2' ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h2", {
      className: "bce-sr-only",
      children: title
    }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h3", {
      className: "bce-sr-only",
      children: title
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("button", {
      type: "button",
      className: `bc-events__section-toggle-button bc-events-title-h1${isCondensed ? ' closed' : ' open'}${className ? ` ${className}` : ''}`,
      onClick: handleToggle,
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
        className: "bc-event-dates__toggle-text",
        children: title
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
        className: "bc-event-dates__toggle-icon",
        "aria-hidden": "true",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_icons_CaretIcon_CaretIcon__WEBPACK_IMPORTED_MODULE_2__["default"], {})
      })]
    })]
  });
}

/***/ },

/***/ "./editor/components/_sections/Loader/Loader.jsx"
/*!*******************************************************!*\
  !*** ./editor/components/_sections/Loader/Loader.jsx ***!
  \*******************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Loader)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


function Loader() {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
    className: "bce-loader",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
      className: "bce-sr-only",
      children: "Loading..."
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
      className: "bce-loader__icon",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("svg", {
        width: "16",
        height: "16",
        viewBox: "0 0 16 16",
        fill: "none",
        xmlns: "http://www.w3.org/2000/svg",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
          d: "M8.93626 0.37875L15.6063 7.04875C16.1213 7.56475 16.1213 8.41275 15.6063 8.92875L8.93626 15.5988C8.42026 16.1138 7.57226 16.1138 7.05626 15.5988L0.386251 8.92875C-0.12875 8.41275 -0.12875 7.56475 0.386251 7.04875L7.05626 0.37875C7.57626 -0.12625 8.41626 -0.12625 8.93626 0.37875Z",
          fill: "#F0F0F0"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
          "fill-rule": "evenodd",
          "clip-rule": "evenodd",
          d: "M15.6063 7.04875L8.93626 0.37875C8.41626 -0.12625 7.57626 -0.12625 7.05626 0.37875L0.386251 7.04875C-0.12875 7.56475 -0.12875 8.41275 0.386251 8.92875L7.05626 15.5988C7.57226 16.1138 8.42026 16.1138 8.93626 15.5988L15.6063 8.92875C16.1213 8.41275 16.1213 7.56475 15.6063 7.04875ZM15.2763 8.59875L8.60626 15.2688C8.44526 15.4308 8.22526 15.5228 7.99626 15.5228C7.76726 15.5228 7.54726 15.4308 7.38626 15.2688L0.716251 8.59875C0.554251 8.43775 0.462251 8.21775 0.462251 7.98875C0.462251 7.75975 0.554251 7.53975 0.716251 7.37875L7.38626 0.70875C7.54626 0.54375 7.76626 0.45075 7.99626 0.45075C8.22626 0.45075 8.44626 0.54375 8.60626 0.70875L15.2763 7.37875C15.4383 7.53975 15.5303 7.75975 15.5303 7.98875C15.5303 8.21775 15.4383 8.43775 15.2763 8.59875Z",
          fill: "#0049DB"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
          "fill-rule": "evenodd",
          "clip-rule": "evenodd",
          d: "M9.99622 4.25873C9.67722 4.01073 9.30322 3.84273 8.90622 3.76873C8.70522 3.72773 8.50122 3.70473 8.29622 3.69873H6.84622C6.33021 3.69873 5.90621 4.12273 5.90621 4.63873V5.43873C4.78521 5.77173 4.01221 6.80973 4.01221 7.97873C4.01221 9.14773 4.78521 10.1858 5.90621 10.5188V11.3188C5.90621 11.8348 6.33021 12.2588 6.84622 12.2588H8.24622C8.45022 12.2508 8.65422 12.2308 8.85622 12.1988C9.27122 12.1228 9.66222 11.9478 9.99622 11.6888C10.6313 11.1778 11.0003 10.4038 10.9963 9.58873C10.9823 9.00973 10.7783 8.45073 10.4163 7.99873C10.7923 7.53073 10.9973 6.94873 10.9963 6.34873C11.0003 5.53673 10.6313 4.76573 9.99622 4.25873ZM9.99622 8.12873C10.3573 8.52973 10.5563 9.04973 10.5563 9.58873C10.5733 10.6238 9.83422 11.5298 8.81622 11.7188C8.63822 11.7518 8.45722 11.7718 8.27622 11.7788H6.84622C6.58822 11.7788 6.37621 11.5668 6.37621 11.3088V10.6088C6.48322 10.6138 6.58921 10.6138 6.69622 10.6088H8.54622C8.71222 10.5848 8.86822 10.5158 8.99622 10.4088C9.25322 10.2158 9.40222 9.91073 9.39622 9.58873C9.39522 9.26873 9.24722 8.96673 8.99622 8.76873C8.87222 8.67673 8.72822 8.61573 8.57622 8.58873H6.84622C6.33021 8.58873 5.90621 9.01273 5.90621 9.52873V9.99873C5.05421 9.67873 4.48621 8.85873 4.48621 7.94873C4.48621 7.03873 5.05421 6.21873 5.90621 5.89873V6.43873C5.90621 6.95473 6.33021 7.37873 6.84622 7.37873H7.91622C8.12622 7.39673 8.33622 7.39673 8.54622 7.37873C8.71222 7.35073 8.86822 7.27773 8.99622 7.16873C9.26422 6.98173 9.42422 6.67573 9.42422 6.34873C9.42422 6.02173 9.26422 5.71573 8.99622 5.52873C8.87022 5.43773 8.72722 5.37273 8.57622 5.33873C8.45322 5.33173 8.32922 5.33173 8.20622 5.33873H6.69622C6.58921 5.33373 6.48322 5.33373 6.37621 5.33873V4.63873C6.37321 4.51573 6.42022 4.39673 6.50622 4.30873C6.59622 4.21873 6.71922 4.16873 6.84622 4.16873H8.22622C8.40622 4.15673 8.58622 4.15673 8.76622 4.16873C9.09422 4.23173 9.40322 4.37273 9.66622 4.57873C10.1963 4.99173 10.5063 5.62673 10.5063 6.29873C10.5303 6.85073 10.3483 7.39273 9.99622 7.81873L9.79622 7.99873L9.99622 8.12873ZM6.74622 10.1288C6.64022 10.1388 6.53222 10.1388 6.42621 10.1288V9.47873C6.41921 9.23473 6.60422 9.02473 6.84622 8.99873H8.15622C8.25622 8.98673 8.35622 8.98673 8.45622 8.99873C8.53722 9.00573 8.61322 9.03673 8.67622 9.08873C8.81422 9.19773 8.89522 9.36373 8.89622 9.53873C8.89922 9.71473 8.81722 9.88273 8.67622 9.98873C8.61222 10.0398 8.53622 10.0738 8.45622 10.0888C8.35622 10.0968 8.25622 10.0968 8.15622 10.0888H6.69622L6.74622 10.1288ZM6.37621 5.79873C6.48222 5.78873 6.59021 5.78873 6.69622 5.79873H8.45622C8.53622 5.81373 8.61222 5.84773 8.67622 5.89873C8.81622 6.00573 8.89822 6.17273 8.89622 6.34873C8.89922 6.52473 8.81722 6.69273 8.67622 6.79873C8.61222 6.84973 8.53622 6.88373 8.45622 6.89873C8.28022 6.91373 8.10222 6.91373 7.92622 6.89873H6.84622C6.58822 6.89873 6.37621 6.68673 6.37621 6.42873V5.79873Z",
          fill: "#0049DB"
        })]
      })
    })]
  });
}

/***/ },

/***/ "./editor/components/icons/CaretIcon/CaretIcon.jsx"
/*!*********************************************************!*\
  !*** ./editor/components/icons/CaretIcon/CaretIcon.jsx ***!
  \*********************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ CaretIcon)
/* harmony export */ });
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__);

function CaretIcon() {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("span", {
    className: "bce-caret-icon"
  });
}

/***/ },

/***/ "./editor/blocks/src/location/editor.scss"
/*!************************************************!*\
  !*** ./editor/blocks/src/location/editor.scss ***!
  \************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ },

/***/ "react"
/*!************************!*\
  !*** external "React" ***!
  \************************/
(module) {

module.exports = window["React"];

/***/ },

/***/ "react/jsx-runtime"
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
(module) {

module.exports = window["ReactJSXRuntime"];

/***/ },

/***/ "@wordpress/api-fetch"
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
(module) {

module.exports = window["wp"]["apiFetch"];

/***/ },

/***/ "@wordpress/block-editor"
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
(module) {

module.exports = window["wp"]["blockEditor"];

/***/ },

/***/ "@wordpress/blocks"
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
(module) {

module.exports = window["wp"]["blocks"];

/***/ },

/***/ "@wordpress/core-data"
/*!**********************************!*\
  !*** external ["wp","coreData"] ***!
  \**********************************/
(module) {

module.exports = window["wp"]["coreData"];

/***/ },

/***/ "@wordpress/data"
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
(module) {

module.exports = window["wp"]["data"];

/***/ },

/***/ "@wordpress/editor"
/*!********************************!*\
  !*** external ["wp","editor"] ***!
  \********************************/
(module) {

module.exports = window["wp"]["editor"];

/***/ },

/***/ "@wordpress/element"
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
(module) {

module.exports = window["wp"]["element"];

/***/ },

/***/ "@wordpress/i18n"
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
(module) {

module.exports = window["wp"]["i18n"];

/***/ },

/***/ "@wordpress/preferences"
/*!*************************************!*\
  !*** external ["wp","preferences"] ***!
  \*************************************/
(module) {

module.exports = window["wp"]["preferences"];

/***/ },

/***/ "@wordpress/url"
/*!*****************************!*\
  !*** external ["wp","url"] ***!
  \*****************************/
(module) {

module.exports = window["wp"]["url"];

/***/ },

/***/ "./editor/blocks/src/location/edit.js"
/*!********************************************!*\
  !*** ./editor/blocks/src/location/edit.js ***!
  \********************************************/
(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Edit)
/* harmony export */ });
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_preferences__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/preferences */ "@wordpress/preferences");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/url */ "@wordpress/url");
/* harmony import */ var _components_utils_store_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../components/_utils/store.js */ "./editor/components/_utils/store.js");
/* harmony import */ var _components_SectionToggle_SectionToggle_jsx__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../../components/SectionToggle/SectionToggle.jsx */ "./editor/components/SectionToggle/SectionToggle.jsx");
/* harmony import */ var _components_sections_Loader_Loader_jsx__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../../components/_sections/Loader/Loader.jsx */ "./editor/components/_sections/Loader/Loader.jsx");
/* harmony import */ var _components_formParts_BasicText_BasicText_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../../components/_formParts/BasicText/BasicText.js */ "./editor/components/_formParts/BasicText/BasicText.js");
/* harmony import */ var _components_formParts_BasicTextArea_BasicTextArea_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../../../components/_formParts/BasicTextArea/BasicTextArea.js */ "./editor/components/_formParts/BasicTextArea/BasicTextArea.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");













function Edit() {
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__.useBlockProps)({
    className: 'bc-event-dates'
  });
  const [isLoading, setIsLoading] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useState)(true);
  const [isLoadingError, setIsLoadingError] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useState)(false);
  const {
    keys,
    meta,
    setMeta
  } = (0,_components_utils_store_js__WEBPACK_IMPORTED_MODULE_7__.getStore)();
  const isCondensed = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useSelect)(select => select(_wordpress_preferences__WEBPACK_IMPORTED_MODULE_2__.store).get('bc-events/location-details-condensed', 'condensed'));
  const {
    set
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useDispatch)(_wordpress_preferences__WEBPACK_IMPORTED_MODULE_2__.store);
  const handleToggle = value => {
    set('bc-events/location-details-condensed', 'condensed', value);
  };
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useEffect)(() => {
    const fetchKeys = async () => {
      try {
        const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5__({
          path: `/${_components_utils_store_js__WEBPACK_IMPORTED_MODULE_7__.REST_NAMESPACE}/get-locations-keys`
        });
        (0,_components_utils_store_js__WEBPACK_IMPORTED_MODULE_7__.setKeys)(response);
      } catch (error) {
        console.error('Error fetching keys:', error);
        setIsLoadingError(true);
      } finally {
        setIsLoading(false);
      }
    };
    fetchKeys();
  }, []);
  const META_ADDRESS = meta?.[(0,_components_utils_store_js__WEBPACK_IMPORTED_MODULE_7__.getKey)('address', keys)] ?? '';
  const META_DESCRIPTION = meta?.[(0,_components_utils_store_js__WEBPACK_IMPORTED_MODULE_7__.getKey)('description', keys)] ?? '';
  const META_WEBSITE = meta?.[(0,_components_utils_store_js__WEBPACK_IMPORTED_MODULE_7__.getKey)('website', keys)] ?? '';
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_12__.jsx)("div", {
    ...blockProps,
    children: isLoading ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_12__.jsx)(_components_sections_Loader_Loader_jsx__WEBPACK_IMPORTED_MODULE_9__["default"], {}) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_12__.jsxs)("div", {
      className: "bc-event-dates__container bc-event-dates__location",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_12__.jsx)(_components_SectionToggle_SectionToggle_jsx__WEBPACK_IMPORTED_MODULE_8__["default"], {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Event Location Details', 'basecadet'),
        value: isCondensed,
        onChange: value => handleToggle(value),
        asTitle: true,
        titleTag: "h2"
      }), !isCondensed && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_12__.jsx)("div", {
        className: "bc-event__content-section",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_12__.jsxs)("div", {
          className: "bc-events__flex-fieldset",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_12__.jsx)(_components_formParts_BasicTextArea_BasicTextArea_js__WEBPACK_IMPORTED_MODULE_11__["default"], {
            id: "location-address",
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Location Address', 'basecadet'),
            value: META_ADDRESS,
            onChange: val => setMeta({
              ...meta,
              [(0,_components_utils_store_js__WEBPACK_IMPORTED_MODULE_7__.getKey)('address', keys)]: val
            }),
            rows: 4,
            useLineBreak: true
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_12__.jsx)(_components_formParts_BasicTextArea_BasicTextArea_js__WEBPACK_IMPORTED_MODULE_11__["default"], {
            id: "location-description",
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Location Description', 'basecadet'),
            value: META_DESCRIPTION,
            onChange: val => setMeta({
              ...meta,
              [(0,_components_utils_store_js__WEBPACK_IMPORTED_MODULE_7__.getKey)('description', keys)]: val
            }),
            rows: 4,
            useLineBreak: true
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_12__.jsx)(_components_formParts_BasicText_BasicText_js__WEBPACK_IMPORTED_MODULE_10__["default"], {
            id: "location-website",
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Location Website', 'basecadet'),
            value: META_WEBSITE,
            onChange: val => setMeta({
              ...meta,
              [(0,_components_utils_store_js__WEBPACK_IMPORTED_MODULE_7__.getKey)('website', keys)]: val
            })
          })]
        })
      })]
    })
  });
}

/***/ },

/***/ "./editor/components/_formParts/BasicText/BasicText.js"
/*!*************************************************************!*\
  !*** ./editor/components/_formParts/BasicText/BasicText.js ***!
  \*************************************************************/
(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ BasicText)
/* harmony export */ });
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");

function BasicText({
  id,
  value,
  options,
  onChange,
  label,
  className = null
}) {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsxs)("div", {
    className: `bc-event-dates__text-group bc-event-dates__input-row ${className ? className : ''}`,
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("label", {
      className: "bc-event-dates__label bc-event-dates__text-group-label",
      htmlFor: id,
      children: label
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("input", {
      type: "text",
      className: "bc-event-dates__input bc-event-dates__text bc-event-dates__text-group-text",
      id: id,
      value: value,
      onChange: e => onChange(e.target.value)
    })]
  });
}

/***/ },

/***/ "./editor/components/_formParts/BasicTextArea/BasicTextArea.js"
/*!*********************************************************************!*\
  !*** ./editor/components/_formParts/BasicTextArea/BasicTextArea.js ***!
  \*********************************************************************/
(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ BasicTextArea)
/* harmony export */ });
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");

function BasicTextArea({
  id,
  value,
  options,
  onChange,
  label,
  className = null,
  rows = 2,
  useLineBreak = false,
  useParagraphBreak = false
}) {
  const handleValue = val => {
    let newVal = val;
    if (useLineBreak) {
      newVal = newVal.replaceAll('\n', '<br>');
    }
    if (useParagraphBreak) {
      newVal = newVal.replaceAll('\n', '</p><p>');
      newVal = `<p>${newVal}</p>`;
    }
    onChange(newVal);
  };
  const displayValue = val => {
    if (!val) return val;
    let display = val;
    if (useLineBreak) {
      display = display.replaceAll('<br>', '\n');
    }
    if (useParagraphBreak) {
      display = display.replace(/^<p>/, '').replace(/<\/p>$/, '');
      display = display.replaceAll('<\/p><p>', '\n');
    }
    return display;
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsxs)("div", {
    className: `bc-event-dates__text-group bc-event-dates__input-row ${className ? className : ''}`,
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("label", {
      className: "bc-event-dates__label bc-event-dates__text-group-label",
      htmlFor: id,
      children: label
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("textarea", {
      className: "bc-event-dates__input bc-event-dates__text bc-event-dates__text-group-text",
      id: id,
      value: displayValue(value),
      onChange: e => handleValue(e.target.value),
      rows: rows ? rows : 2
    })]
  });
}

/***/ },

/***/ "./editor/components/_utils/store.js"
/*!*******************************************!*\
  !*** ./editor/components/_utils/store.js ***!
  \*******************************************/
(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   META_KEY: () => (/* binding */ META_KEY),
/* harmony export */   META_SCOPE: () => (/* binding */ META_SCOPE),
/* harmony export */   REST_NAMESPACE: () => (/* binding */ REST_NAMESPACE),
/* harmony export */   getKey: () => (/* binding */ getKey),
/* harmony export */   getPostType: () => (/* binding */ getPostType),
/* harmony export */   getStore: () => (/* binding */ getStore),
/* harmony export */   loadKeys: () => (/* binding */ loadKeys),
/* harmony export */   setKeys: () => (/* binding */ setKeys)
/* harmony export */ });
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_preferences__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/preferences */ "@wordpress/preferences");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/core-data */ "@wordpress/core-data");





const REST_NAMESPACE = 'bc-events/v1';

/** META KEY NAMESPACING */
const META_SCOPE = 'bc-events/meta-keys';
const META_KEY = 'meta-keys';

/**
 * Set Key values
 * 
 * todo: normalize data
 */
function setKeys(value) {
  (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.dispatch)(_wordpress_preferences__WEBPACK_IMPORTED_MODULE_1__.store).set(META_SCOPE, META_KEY, value);
}

/**
 * Load keys from preferencesStore
 */
function loadKeys() {
  const value = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.useSelect)(select => select(_wordpress_preferences__WEBPACK_IMPORTED_MODULE_1__.store).get(META_SCOPE, META_KEY), [META_KEY]);
  return value;
}

/**
 * Get Key
 * 
 * @param {Object} keys - Object of keys
 * @param {string} keyName - Name of the key to retrieve
 */
function getKey(keyName, keys = false) {
  if (!keys) {
    keys = loadKeys();
  }
  return keys[keyName] || null;
}
function getPostType() {
  const postType = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.useSelect)(select => select(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__.store).getCurrentPostType(), []);
  return postType;
}
function getStore() {
  const keys = loadKeys();
  const postType = getPostType();
  const [meta, setMeta] = (0,_wordpress_core_data__WEBPACK_IMPORTED_MODULE_3__.useEntityProp)('postType', postType, 'meta');
  return {
    keys,
    postType,
    meta,
    setMeta
  };
}

/***/ },

/***/ "./editor/blocks/src/location/block.json"
/*!***********************************************!*\
  !*** ./editor/blocks/src/location/block.json ***!
  \***********************************************/
(module) {

module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"bc-events/location","version":"0.1.0","title":"Location Meta Block","category":"bluecadetEvents","icon":"location","description":"Set meta data for an event location","example":{},"supports":{"html":false},"textdomain":"bc-events","editorScript":"file:./index.js","editorStyle":"file:./index.css","render":"file:./render.php"}');

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
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!*********************************************!*\
  !*** ./editor/blocks/src/location/index.js ***!
  \*********************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _edit_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit.js */ "./editor/blocks/src/location/edit.js");
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./editor.scss */ "./editor/blocks/src/location/editor.scss");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./block.json */ "./editor/blocks/src/location/block.json");
/* harmony import */ var _components_BlockIcon_BlockIcon_jsx__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../components/BlockIcon/BlockIcon.jsx */ "./editor/components/BlockIcon/BlockIcon.jsx");





(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_3__.name, {
  ..._block_json__WEBPACK_IMPORTED_MODULE_3__,
  icon: _components_BlockIcon_BlockIcon_jsx__WEBPACK_IMPORTED_MODULE_4__["default"],
  edit: _edit_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: () => null
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map