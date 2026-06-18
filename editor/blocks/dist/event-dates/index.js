/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

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

/***/ "./editor/components/_formParts/CheckboxButton/CheckboxButton.jsx"
/*!************************************************************************!*\
  !*** ./editor/components/_formParts/CheckboxButton/CheckboxButton.jsx ***!
  \************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ CheckboxButton)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


function CheckboxButton({
  id,
  value,
  checked,
  onChange,
  label,
  pressedLabel = null,
  smallOnChecked = false,
  smallButton = false
}) {
  const [isPressed, setIsPressed] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(value);
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    setIsPressed(value);
  }, [value]);
  const pressedLabelText = pressedLabel ? pressedLabel : label;
  const classes = smallButton ? "bc-events__checkbox-button bc-events__button bc-events__button--secondary bc-events__button--medium" : "bc-events__checkbox-button bc-events__button bc-events__button--secondary";
  const checkedClasses = smallOnChecked ? "bc-events__checkbox-button bc-events__button bc-events__button--secondary bc-events__button--small" : classes;
  const togglePressed = () => {
    setIsPressed(!isPressed);
    onChange(!isPressed);
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("button", {
    type: "button",
    className: isPressed ? checkedClasses : classes,
    onClick: togglePressed,
    "aria-pressed": isPressed,
    children: isPressed ? `${pressedLabelText}` : `${label}`
  });
}

/***/ },

/***/ "./editor/components/_formParts/DateRow/DateRow.jsx"
/*!**********************************************************!*\
  !*** ./editor/components/_formParts/DateRow/DateRow.jsx ***!
  \**********************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ DateRow)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


function DateRow({
  dateID,
  dateValue,
  dateLabel,
  dateMin = null,
  onDateChange = null,
  onDateBlur = null,
  timeID,
  timeValue,
  timeLabel,
  timeMin = null,
  onTimeChange = null,
  onTimeBlur = null,
  required = false
}) {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
    className: `bc-event-dates__date-row ${required ? 'bc-event-dates__date-row--required' : ''}`,
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
      className: "bc-event-dates__date-row-date",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("label", {
        className: "bc-event-dates__label",
        htmlFor: dateID,
        children: [dateLabel, required ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("span", {
          className: "req",
          children: "*"
        }) : '']
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("input", {
        className: "bc-event-dates__input",
        type: "date",
        id: dateID,
        value: dateValue,
        onChange: e => onDateChange && onDateChange(e.target.value),
        onBlur: e => onDateBlur && onDateBlur(e.target.value),
        min: dateMin,
        ...(required ? {
          required: true
        } : {})
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
      className: "bc-event-dates__date-row-time",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("label", {
        className: "bc-event-dates__label",
        htmlFor: timeID,
        children: [timeLabel, required ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("span", {
          className: "req",
          children: "*"
        }) : '']
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("input", {
        className: "bc-event-dates__input",
        type: "time",
        id: timeID,
        value: timeValue,
        min: timeMin,
        onChange: e => onTimeChange && onTimeChange(e.target.value),
        onBlur: e => onTimeBlur && onTimeBlur(e.target.value),
        ...(required ? {
          required: true
        } : {})
      })]
    })]
  });
}

/***/ },

/***/ "./editor/components/_formParts/RecurringButton/RecurringButton.jsx"
/*!**************************************************************************!*\
  !*** ./editor/components/_formParts/RecurringButton/RecurringButton.jsx ***!
  \**************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ RecurringButton)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


function RecurringButton({
  id,
  value,
  onChange
}) {
  const [isPressed, setIsPressed] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(value);
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    setIsPressed(value);
  }, [value]);
  const togglePressed = () => {
    setIsPressed(!isPressed);
    onChange(!isPressed);
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("button", {
    id: id,
    className: `bce-events-recurring-button ${isPressed ? 'is-pressed' : ''}`,
    onClick: togglePressed,
    "aria-pressed": isPressed,
    children: isPressed ? 'Recurring Event Settings' : 'Add Recurring Dates'
  });
}

/***/ },

/***/ "./editor/components/_formParts/StartEndDate/StartEndDate.jsx"
/*!********************************************************************!*\
  !*** ./editor/components/_formParts/StartEndDate/StartEndDate.jsx ***!
  \********************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ StartEndDate)
/* harmony export */ });
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/core-data */ "@wordpress/core-data");
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/url */ "@wordpress/url");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _DateRow_DateRow__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../DateRow/DateRow */ "./editor/components/_formParts/DateRow/DateRow.jsx");
/* harmony import */ var _utils_store__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../_utils/store */ "./editor/components/_utils/store.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_11__);












function StartEndDate({
  startDateKey = null,
  startTimeKey = null,
  startTimestampKey = null,
  endDateKey = null,
  endTimeKey = null,
  endTimestampKey = null,
  idPrefix = "bc-event-dates"
}) {
  const {
    meta,
    setMeta
  } = (0,_utils_store__WEBPACK_IMPORTED_MODULE_10__.getStore)();
  const startDate = meta?.[startDateKey] ?? '';
  const startTime = meta?.[startTimeKey] ?? '';
  const endDate = meta?.[endDateKey] ?? '';
  const endTime = meta?.[endTimeKey] ?? '';
  const getTimestamp = async (date, time) => {
    try {
      const params = {
        date,
        time
      };
      const url = (0,_wordpress_url__WEBPACK_IMPORTED_MODULE_8__.addQueryArgs)(`/${_utils_store__WEBPACK_IMPORTED_MODULE_10__.REST_NAMESPACE}/to-timestamp`, params);
      const data = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_7___default()({
        path: url
      });
      return data.timestamp;
    } catch (error) {
      console.error(error);
    }
  };
  const handleBlur = async () => {
    const updates = {};
    const [startTs, endTs] = await Promise.all([startDate && startTime ? getTimestamp(startDate, startTime) : null, endDate && endTime ? getTimestamp(endDate, endTime) : null]);
    if (startTs != null) updates[startTimestampKey] = startTs;
    if (endTs != null) updates[endTimestampKey] = endTs;
    setMeta({
      ...meta,
      ...updates
    });
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_11__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_11__.Fragment, {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_11__.jsx)("div", {
      className: "bc-event-dates__group bc-event-dates__group--start",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_11__.jsx)(_DateRow_DateRow__WEBPACK_IMPORTED_MODULE_9__["default"], {
        dateID: "bce-start-date",
        dateValue: startDate,
        dateLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Start Date', 'basecadet'),
        onDateChange: val => setMeta({
          ...meta,
          [startDateKey]: val
        }),
        onDateBlur: () => handleBlur(),
        timeID: "bce-start-time",
        timeValue: startTime,
        timeLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Start Time', 'basecadet'),
        onTimeChange: val => setMeta({
          ...meta,
          [startTimeKey]: val
        }),
        onTimeBlur: () => handleBlur(),
        required: true
      })
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_11__.jsx)("div", {
      className: "bc-event-dates__group bc-event-dates__group--end",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_11__.jsx)(_DateRow_DateRow__WEBPACK_IMPORTED_MODULE_9__["default"], {
        dateID: "bce-end-date",
        dateValue: endDate,
        dateLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('End Date', 'basecadet'),
        dateMin: startDate || undefined,
        onDateChange: val => setMeta({
          ...meta,
          [endDateKey]: val
        }),
        onDateBlur: () => handleBlur(),
        timeID: "bce-end-time",
        timeValue: endTime,
        timeLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('End Time', 'basecadet'),
        timeMin: endDate === startDate && startTime ? startTime : undefined,
        onTimeChange: val => setMeta({
          ...meta,
          [endTimeKey]: val
        }),
        onTimeBlur: () => handleBlur(),
        required: true
      })
    })]
  });
}

/***/ },

/***/ "./editor/components/_sections/ChildEventDetails/ChildEventDetails.jsx"
/*!*****************************************************************************!*\
  !*** ./editor/components/_sections/ChildEventDetails/ChildEventDetails.jsx ***!
  \*****************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ ChildEventDetails)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/core-data */ "@wordpress/core-data");
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../_utils/store */ "./editor/components/_utils/store.js");
/* harmony import */ var _formParts_BasicCheckbox_BasicCheckbox_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../_formParts/BasicCheckbox/BasicCheckbox.js */ "./editor/components/_formParts/BasicCheckbox/BasicCheckbox.js");
/* harmony import */ var _wordpress_date__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/date */ "@wordpress/date");
/* harmony import */ var _wordpress_date__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_date__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__);







function ChildEventDetails({
  parentData
}) {
  const {
    keys,
    meta,
    setMeta
  } = (0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getStore)();
  const {
    parent_url,
    parent_id,
    parent_title
  } = parentData;
  const DENY_OVERRIDE = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getKey)('child_deny_override', keys)] ?? false;
  const START_DATE = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getKey)('start_date', keys)];
  const END_DATE = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getKey)('end_date', keys)];
  const START_TIME = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getKey)('start_time', keys)];
  const END_TIME = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getKey)('end_time', keys)];
  const convertTo12Hour = time => {
    if (!time) return null;
    const [hour, minute] = time.split(':');
    const hourNum = parseInt(hour);
    const ampm = hourNum >= 12 ? 'PM' : 'AM';
    const hour12 = hourNum % 12 || 12; // Convert to 12-hour format
    return `${hour12}:${minute} ${ampm}`;
  };
  const convertYMD = date => {
    if (!date) return null;
    const [year, month, day] = date.split('-');
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    return `${months[parseInt(month, 10) - 1]} ${day}, ${year}`;
  };
  const startDate = convertYMD(START_DATE);
  const startTime = START_TIME ? convertTo12Hour(START_TIME) : null;
  const endDate = convertYMD(END_DATE);
  const endTime = END_TIME ? convertTo12Hour(END_TIME) : null;
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
    className: "bc-event-details bc-event__content-section",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
      className: "bc-events__flex-fieldset",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
        className: "bc-event__child-content",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("p", {
          className: "bc-event__child-content-title",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("strong", {
            children: ["This is a child event of ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("a", {
              href: parent_url,
              target: "_blank",
              rel: "noopener noreferrer",
              children: parent_title
            })]
          })
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
        className: "bc-event__child-overview",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("p", {
          className: "bc-event-dates__description bc-events__child-overview-row",
          children: ["Event Start: ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("span", {
            children: [startDate ? startDate : 'N/A', " ", startTime ? `at ${startTime}` : '']
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("p", {
          className: "bc-event-dates__description bc-events__child-overview-row",
          children: ["Event End: ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("span", {
            children: [endDate ? endDate : 'N/A', " ", endTime ? `at ${endTime}` : '']
          })]
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
        className: "bc-event__child-content",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("p", {
          className: "bc-event-dates__description",
          children: ["By default, content from ", parent_title, " will overwrite all content changes made in the editor for this event. You can override this behavior by checking the option below."]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
          className: "bc-events__flex-fieldset",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_formParts_BasicCheckbox_BasicCheckbox_js__WEBPACK_IMPORTED_MODULE_4__["default"], {
            id: "bce-deny-content-overrides",
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Override content for this event', 'basecadet'),
            checked: DENY_OVERRIDE,
            onChange: val => setMeta({
              ...meta,
              [(0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getKey)('child_deny_override', keys)]: val
            })
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
          className: "bc-event__child-content-notes",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("p", {
            className: "bc-event-dates__description",
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("strong", {
              children: "Note that by checking this option:"
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("ul", {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("li", {
              className: "bc-event-dates__description",
              children: "Content will not longer sync from the parent. Any changes from the parent will need to be added manually."
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("li", {
              className: "bc-event-dates__description",
              children: "This will only effect content from the editor. Taxonomies and other meta values will still be overwritten by parent data."
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("li", {
              className: "bc-event-dates__description",
              children: ["If unchecked, content will not be synced until the ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("strong", {
                children: "parent"
              }), " event is saved."]
            })]
          })]
        })]
      })]
    })
  });
}

/***/ },

/***/ "./editor/components/_sections/CustomOccurences/CustomOccurences.jsx"
/*!***************************************************************************!*\
  !*** ./editor/components/_sections/CustomOccurences/CustomOccurences.jsx ***!
  \***************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ CustomOccurences)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_store_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../_utils/store.js */ "./editor/components/_utils/store.js");
/* harmony import */ var _formParts_BasicCheckbox_BasicCheckbox_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../_formParts/BasicCheckbox/BasicCheckbox.js */ "./editor/components/_formParts/BasicCheckbox/BasicCheckbox.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);





function CustomOccurences() {
  const {
    keys,
    meta,
    setMeta
  } = (0,_utils_store_js__WEBPACK_IMPORTED_MODULE_2__.getStore)();
  const id = 'custom-values';
  const META_KEY = (0,_utils_store_js__WEBPACK_IMPORTED_MODULE_2__.getKey)('custom_occurrences', keys);
  const values = meta?.[META_KEY] ?? [];
  const addOccurence = () => {
    const newValues = [...values, {
      start_date: '',
      customize: false,
      start_time: '',
      end_date: '',
      end_time: ''
    }];
    setMeta({
      ...meta,
      [META_KEY]: newValues
    });
  };
  const removeOccurence = index => {
    const newValues = values.filter((_, i) => i !== index);
    setMeta({
      ...meta,
      [META_KEY]: newValues
    });
  };
  const onChange = newValues => {
    setMeta({
      ...meta,
      [META_KEY]: newValues
    });
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
    className: "bc-event-dates__custom-occurences",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
      className: "bc-event-dates__description",
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('If your event occurs on days or times that do not fit a regular schedule, you can add the individual dates here. Individual dates can be added in addition to setting a frequency, or on their own.', 'bluecadet-events')
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
      className: "bc-event-dates__description",
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Start Date is the only required field for each set, but you can configure end date and times as needed.', 'bluecadet-events')
    }), values.map((occurence, index) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(OccurenceRow, {
      occurence: occurence,
      index: index,
      values: values,
      onChange: onChange,
      removeOccurence: removeOccurence,
      id: id
    }, `${id}-occurence-${index}`)), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
      className: "bc-event-dates__custom-occurence-add",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("button", {
        type: "button",
        className: "bc-events__button bc-events__button--secondary",
        onClick: addOccurence,
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Add Specific Date', 'basecadet')
      })
    })]
  });
}
function OccurenceRow({
  occurence,
  index,
  values,
  onChange,
  removeOccurence,
  id
}) {
  const [showCustomize, setShowCustomize] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)(occurence.customize || false);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
    className: "bc-event-dates__custom-occurence",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
      className: "bc-event-dates__custom-occurence-inputs",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
        className: "bc-event-dates__custom-occurence-dates",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
          className: "bc-events__flex-fieldset",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
            className: "bc-event-dates__date-row-date",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("label", {
              htmlFor: `${id}-start-${index}`,
              className: "bc-event-dates__label",
              children: "Start Date"
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("input", {
              className: `bc-event-dates__input`,
              type: "date",
              id: `${id}-start-${index}`,
              value: occurence.start_date,
              onChange: e => {
                onChange(values.map((item, i) => i === index ? {
                  ...item,
                  start_date: e.target.value
                } : item));
              }
            })]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
            className: "bc-event-dates__custom-occurence-customize",
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_formParts_BasicCheckbox_BasicCheckbox_js__WEBPACK_IMPORTED_MODULE_3__["default"], {
              id: `${id}-customize-${index}`,
              label: "Add custom times",
              checked: showCustomize,
              onChange: val => {
                setShowCustomize(val);
                onChange(values.map((item, i) => i === index ? {
                  ...item,
                  customize: val
                } : item));
              }
            })
          }), showCustomize && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.Fragment, {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
              className: "bc-event-dates__date-row-date",
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("label", {
                htmlFor: `${id}-start-time-${index}`,
                className: "bc-event-dates__label",
                children: "Start Time"
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("input", {
                className: `bc-event-dates__input`,
                type: "time",
                id: `${id}-start-time-${index}`,
                value: occurence.start_time,
                onChange: e => {
                  onChange(values.map((item, i) => i === index ? {
                    ...item,
                    start_time: e.target.value
                  } : item));
                }
              })]
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
              className: "bc-event-dates__date-row-date",
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("label", {
                htmlFor: `${id}-end-${index}`,
                className: "bc-event-dates__label",
                children: "End Date"
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("input", {
                className: `bc-event-dates__input`,
                type: "date",
                id: `${id}-end-${index}`,
                value: occurence.end_date,
                onChange: e => {
                  onChange(values.map((item, i) => i === index ? {
                    ...item,
                    end_date: e.target.value
                  } : item));
                }
              })]
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
              className: "bc-event-dates__date-row-date",
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("label", {
                htmlFor: `${id}-end-time-${index}`,
                className: "bc-event-dates__label",
                children: "End Time"
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("input", {
                className: `bc-event-dates__input`,
                type: "time",
                id: `${id}-end-time-${index}`,
                value: occurence.end_time,
                onChange: e => {
                  onChange(values.map((item, i) => i === index ? {
                    ...item,
                    end_time: e.target.value
                  } : item));
                }
              })]
            })]
          })]
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
        className: "bc-event-dates__custom-occurence-remove",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("button", {
          type: "button",
          className: "bc-event-dates__remove-occurence bc-events__button bc-events__button--small bc-events__button--warning",
          onClick: () => removeOccurence(index),
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Remove', 'basecadet')
        })
      })]
    })
  }, `${id}-occurence-${index}`);
}

/***/ },

/***/ "./editor/components/_sections/EventDetails/EventDetails.jsx"
/*!*******************************************************************!*\
  !*** ./editor/components/_sections/EventDetails/EventDetails.jsx ***!
  \*******************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ EventDetails)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/core-data */ "@wordpress/core-data");
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../_utils/store */ "./editor/components/_utils/store.js");
/* harmony import */ var _formParts_StartEndDate_StartEndDate_jsx__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../_formParts/StartEndDate/StartEndDate.jsx */ "./editor/components/_formParts/StartEndDate/StartEndDate.jsx");
/* harmony import */ var _formParts_BasicCheckbox_BasicCheckbox_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../_formParts/BasicCheckbox/BasicCheckbox.js */ "./editor/components/_formParts/BasicCheckbox/BasicCheckbox.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__);







function EventDetails() {
  const {
    keys,
    meta,
    setMeta
  } = (0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getStore)();
  const META_START_DATE = (0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getKey)('start_date', keys);
  const META_START_TIME = (0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getKey)('start_time', keys);
  const META_START_TIMESTAMP = (0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getKey)('start_timestamp', keys);
  const META_END_DATE = (0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getKey)('end_date', keys);
  const META_END_TIME = (0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getKey)('end_time', keys);
  const META_END_TIMESTAMP = (0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getKey)('end_timestamp', keys);
  const META_HIDE_TIME = (0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getKey)('hide_time_display', keys);
  const META_HIDE_END_TIME = (0,_utils_store__WEBPACK_IMPORTED_MODULE_3__.getKey)('hide_end_time_display', keys);
  const HIDE_TIME_VALUE = meta?.[META_HIDE_TIME] ?? false;
  const HIDE_END_TIME_VALUE = meta?.[META_HIDE_END_TIME] ?? false;
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
    className: "bc-event-details bc-event__content-section",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
      className: "bc-events__flex-fieldset",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_formParts_StartEndDate_StartEndDate_jsx__WEBPACK_IMPORTED_MODULE_4__["default"], {
        startDateKey: META_START_DATE,
        startTimeKey: META_START_TIME,
        startTimestampKey: META_START_TIMESTAMP,
        endDateKey: META_END_DATE,
        endTimeKey: META_END_TIME,
        endTimestampKey: META_END_TIMESTAMP,
        idPrefix: "bc-event-dates"
      })
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
      class: "bc-event-dates__divider"
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
      className: "bc-events__flex-fieldset",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_formParts_BasicCheckbox_BasicCheckbox_js__WEBPACK_IMPORTED_MODULE_5__["default"], {
        id: "hide-time",
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Hide Time Display', 'basecadet'),
        checked: HIDE_TIME_VALUE,
        onChange: val => setMeta({
          ...meta,
          [META_HIDE_TIME]: val
        })
      }), !HIDE_TIME_VALUE && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_formParts_BasicCheckbox_BasicCheckbox_js__WEBPACK_IMPORTED_MODULE_5__["default"], {
        id: "hide-end-time",
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Hide End Time Display', 'basecadet'),
        checked: HIDE_END_TIME_VALUE,
        onChange: val => setMeta({
          ...meta,
          [META_HIDE_END_TIME]: val
        })
      })]
    })]
  });
}

/***/ },

/***/ "./editor/components/_sections/Frequency/Frequency.jsx"
/*!*************************************************************!*\
  !*** ./editor/components/_sections/Frequency/Frequency.jsx ***!
  \*************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Frequency)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../_utils/store */ "./editor/components/_utils/store.js");
/* harmony import */ var _blocks_src_event_dates_data_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../blocks/src/event-dates/data.js */ "./editor/blocks/src/event-dates/data.js");
/* harmony import */ var _formParts_BasicSelect_BasicSelect__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../_formParts/BasicSelect/BasicSelect */ "./editor/components/_formParts/BasicSelect/BasicSelect.js");
/* harmony import */ var _formParts_CheckboxButton_CheckboxButton_jsx__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../_formParts/CheckboxButton/CheckboxButton.jsx */ "./editor/components/_formParts/CheckboxButton/CheckboxButton.jsx");
/* harmony import */ var _formParts_CheckboxFormGroup_CheckboxFormGroup_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../_formParts/CheckboxFormGroup/CheckboxFormGroup.js */ "./editor/components/_formParts/CheckboxFormGroup/CheckboxFormGroup.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__);







function Frequency() {
  const {
    keys,
    meta,
    setMeta
  } = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getStore)();
  const META_USE_FREQUENCY = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('use_frequency', keys);
  const META_FREQUENCY = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq', keys);
  const META_WEEKLY_DAYS = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_days', keys);
  const META_MONTHLY_SCHEDULE = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_mo_schedule', keys);
  const META_MONTHLY_DAY = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_mo_day', keys);
  const META_MONTHLY_DATE = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_mo_date', keys);
  const META_END_TYPE = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_end_type', keys);
  const META_END_DATE = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_end_date', keys);
  const META_END_AFTER_X = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_end_after_x', keys);
  const META_START_DATE = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('start_date', keys);
  const META_FREQ_IS = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_is', keys);
  const META_FREQ_WAS = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_was', keys);
  const END_TYPE_VALUE = meta?.[META_END_TYPE] ?? 'on_date';
  const END_DATE_VALUE = meta?.[META_END_DATE] ?? '';
  const END_AFTER_X_VALUE = meta?.[META_END_AFTER_X] ?? 2;
  const USE_FREQUENCY = meta?.[META_USE_FREQUENCY] ?? false;
  const FREQUENCY_VALUE = meta?.[META_FREQUENCY] ?? '';
  const WEEKLY_DAYS = meta?.[META_WEEKLY_DAYS] ?? [];
  const MONTHLY_SCHED = meta?.[META_MONTHLY_SCHEDULE] ?? '';
  const MONTHLY_DAY = meta?.[META_MONTHLY_DAY] ?? '';
  const MONTHLY_DATE = meta?.[META_MONTHLY_DATE] ?? '';
  const START_DATE_VALUE = meta?.[META_START_DATE];
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
    className: "bc-event-dates__frequency",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("p", {
      className: "bc-event-dates__description",
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('If your event recurs on a specific schedule, you can set the frequency here.', 'bluecadet-events')
    }), USE_FREQUENCY && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
      className: "bc-events__flex-fieldset",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
        className: "bc-event-dates__frequency-row",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_formParts_BasicSelect_BasicSelect__WEBPACK_IMPORTED_MODULE_3__["default"], {
          id: "recurring-frequency",
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Frequency', 'basecadet'),
          value: FREQUENCY_VALUE,
          options: _blocks_src_event_dates_data_js__WEBPACK_IMPORTED_MODULE_2__.FREQUENCY_OPTIONS,
          onChange: val => setMeta({
            ...meta,
            [META_FREQUENCY]: val
          })
        })
      }), FREQUENCY_VALUE === 'weekly' && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
        className: "bc-event-dates__frequency-row",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_formParts_CheckboxFormGroup_CheckboxFormGroup_js__WEBPACK_IMPORTED_MODULE_5__["default"], {
          id: "recurring-custom-occurrences",
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Day(s) of the week', 'basecadet'),
          values: WEEKLY_DAYS,
          options: _blocks_src_event_dates_data_js__WEBPACK_IMPORTED_MODULE_2__.DAY_OF_WEEK_OPTIONS,
          onChange: vals => {
            setMeta({
              ...meta,
              [META_WEEKLY_DAYS]: vals
            });
          }
        })
      }), FREQUENCY_VALUE === 'monthly' && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.Fragment, {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
          className: "bc-event-dates__frequency-row",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_formParts_BasicSelect_BasicSelect__WEBPACK_IMPORTED_MODULE_3__["default"], {
            id: "recurring-monthly-schedule",
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Schedule', 'basecadet'),
            value: MONTHLY_SCHED,
            options: _blocks_src_event_dates_data_js__WEBPACK_IMPORTED_MODULE_2__.RECURRING_MONTHLY_FREQUENCY_OPTIONS,
            onChange: val => setMeta({
              ...meta,
              [META_MONTHLY_SCHEDULE]: val
            })
          })
        }), MONTHLY_SCHED === 'date' ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
          className: "bc-event-dates__frequency-row bc-event-dates__input-row",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("label", {
            className: "bc-event-dates__label",
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Day of Month', 'basecadet')
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("input", {
            className: "bc-event-dates__input",
            type: "number",
            value: MONTHLY_DATE,
            required: true,
            step: 1,
            min: 1,
            max: 31,
            onChange: e => {
              const value = e.target.value.replace(/^0+(?=\d)/, '');
              setMeta({
                ...meta,
                [META_MONTHLY_DATE]: value
              });
            }
          })]
        }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
          className: "bc-event-dates__frequency-row",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_formParts_BasicSelect_BasicSelect__WEBPACK_IMPORTED_MODULE_3__["default"], {
            id: "recurring-monthly-day",
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Weekday', 'basecadet'),
            value: MONTHLY_DAY,
            options: _blocks_src_event_dates_data_js__WEBPACK_IMPORTED_MODULE_2__.DAY_OF_WEEK_OPTIONS,
            onChange: val => setMeta({
              ...meta,
              [META_MONTHLY_DAY]: val
            })
          })
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
        className: "bc-event-dates__frequency-row",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_formParts_BasicSelect_BasicSelect__WEBPACK_IMPORTED_MODULE_3__["default"], {
          id: "end-type",
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Ends', 'basecadet'),
          value: END_TYPE_VALUE,
          options: [{
            value: 'on_date',
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('On Selected Date', 'basecadet')
          }, {
            value: 'after_x',
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('After [X] Events', 'basecadet')
          }],
          onChange: val => {
            console.log(val);
            setMeta({
              ...meta,
              [META_END_TYPE]: val
            });
          }
        })
      }), END_TYPE_VALUE === 'on_date' && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
        className: "bc-event-dates__frequency-row bc-event-dates__input-row",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("label", {
          className: "bc-event-dates__label",
          htmlFor: "recurring-end-date",
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('At the end of day:', 'basecadet')
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("input", {
          className: `bc-event-dates__input`,
          type: "date",
          id: "recurring-end-date",
          value: END_DATE_VALUE,
          min: START_DATE_VALUE || undefined,
          onChange: e => setMeta({
            ...meta,
            [META_END_DATE]: e.target.value
          })
        })]
      }), END_TYPE_VALUE === 'after_x' && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
        className: "bc-event-dates__frequency-row bc-event-dates__input-row",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("label", {
          className: "bc-event-dates__label",
          htmlFor: "recurring-end-after-x",
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('After', 'basecadet')
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("input", {
          className: `bc-event-dates__input`,
          type: "number",
          id: "recurring-end-after-x",
          value: END_AFTER_X_VALUE,
          min: 1,
          onChange: e => setMeta({
            ...meta,
            [META_END_AFTER_X]: e.target.value
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("span", {
          className: "bc-event-dates__label bc-event-dates__recurring-end-after-x-label",
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('events', 'basecadet')
        })]
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
      className: "bc-event-dates__frequency-row bc-event-dates__frequency-toggle",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_formParts_CheckboxButton_CheckboxButton_jsx__WEBPACK_IMPORTED_MODULE_4__["default"], {
        id: "use-frequency",
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Set a Frequency', 'basecadet'),
        pressedLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Remove Frequency', 'basecadet'),
        value: USE_FREQUENCY,
        onChange: val => {
          setMeta({
            ...meta,
            [META_USE_FREQUENCY]: val
          });
        },
        smallOnChecked: true
      })
    })]
  });
}

/***/ },

/***/ "./editor/components/_sections/OmitDates/OmitDates.jsx"
/*!*************************************************************!*\
  !*** ./editor/components/_sections/OmitDates/OmitDates.jsx ***!
  \*************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ OmitDates)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_store_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../_utils/store.js */ "./editor/components/_utils/store.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__);




function OmitDates() {
  const {
    keys,
    meta,
    setMeta
  } = (0,_utils_store_js__WEBPACK_IMPORTED_MODULE_2__.getStore)();
  const idPrefix = 'bc-event-omit-dates';
  const META_KEY = (0,_utils_store_js__WEBPACK_IMPORTED_MODULE_2__.getKey)('omissions', keys);
  const values = meta?.[META_KEY] ?? [];
  const addDate = () => {
    const newValues = [...values, ''];
    onChange(newValues);
  };
  const removeDate = index => {
    const newValues = values.filter((_, i) => i !== index);
    onChange(newValues);
  };
  const onChange = newValues => {
    setMeta({
      ...meta,
      [META_KEY]: newValues
    });
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
    className: "bc-event-dates__exclusions",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
      className: "bc-event-dates__description",
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('If your event has specific dates that should be excluded from a schedule, you can add them here.', 'bluecadet-events')
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
      className: "bc-event-dates__exclusions-dates",
      children: values.map((omitDate, index) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        className: "bc-event-dates__exclusion",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
          className: "bc-event-dates__input-row",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("label", {
            htmlFor: `${idPrefix}-date-${index}`,
            className: "u-sr-only",
            children: ["Omit Date ", index + 1]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("input", {
            className: `bc-event-dates__input`,
            type: "date",
            id: `${idPrefix}-date-${index}`,
            value: omitDate,
            onChange: e => {
              const newValues = [...values];
              newValues[index] = e.target.value;
              onChange(newValues);
            }
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("button", {
          type: "button",
          className: "bc-events__button bc-events__button--small bc-events__button--warning",
          onClick: () => removeDate(index),
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Remove', 'basecadet')
        })]
      }, `${idPrefix}-exclusion-${index}`))
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("button", {
      type: "button",
      className: "bc-events__button bc-events__button--secondary",
      onClick: addDate,
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Add Exclusion', 'basecadet')
    })]
  });
}

/***/ },

/***/ "./editor/components/_sections/Recurring/Recurring.jsx"
/*!*************************************************************!*\
  !*** ./editor/components/_sections/Recurring/Recurring.jsx ***!
  \*************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Recurring)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_preferences__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/preferences */ "@wordpress/preferences");
/* harmony import */ var _wordpress_preferences__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_preferences__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _utils_store_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../_utils/store.js */ "./editor/components/_utils/store.js");
/* harmony import */ var _Frequency_Frequency_jsx__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../Frequency/Frequency.jsx */ "./editor/components/_sections/Frequency/Frequency.jsx");
/* harmony import */ var _CustomOccurences_CustomOccurences_jsx__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../CustomOccurences/CustomOccurences.jsx */ "./editor/components/_sections/CustomOccurences/CustomOccurences.jsx");
/* harmony import */ var _OmitDates_OmitDates_jsx__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../OmitDates/OmitDates.jsx */ "./editor/components/_sections/OmitDates/OmitDates.jsx");
/* harmony import */ var _SectionToggle_SectionToggle_jsx__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../SectionToggle/SectionToggle.jsx */ "./editor/components/SectionToggle/SectionToggle.jsx");
/* harmony import */ var _formParts_RecurringButton_RecurringButton_jsx__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../_formParts/RecurringButton/RecurringButton.jsx */ "./editor/components/_formParts/RecurringButton/RecurringButton.jsx");
/* harmony import */ var _RemoveRecurring_RemoveRecurring_jsx__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../RemoveRecurring/RemoveRecurring.jsx */ "./editor/components/_sections/RemoveRecurring/RemoveRecurring.jsx");
/* harmony import */ var _RecurringAltered_jsx__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./RecurringAltered.jsx */ "./editor/components/_sections/Recurring/RecurringAltered.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__);














function Recurring() {
  const {
    keys,
    meta,
    setMeta
  } = (0,_utils_store_js__WEBPACK_IMPORTED_MODULE_5__.getStore)();
  const {
    set
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useDispatch)(_wordpress_preferences__WEBPACK_IMPORTED_MODULE_3__.store);
  const META_IS_RECURRING = (0,_utils_store_js__WEBPACK_IMPORTED_MODULE_5__.getKey)('is_recurring', keys);
  const META_IS_RECURRING_WAS = (0,_utils_store_js__WEBPACK_IMPORTED_MODULE_5__.getKey)('is_recurring_was', keys);
  const IS_RECURRING = meta?.[META_IS_RECURRING] ?? false;
  const IS_RECURRING_WAS = meta?.[META_IS_RECURRING_WAS] ?? IS_RECURRING;
  const isFreqCondensed = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useSelect)(select => select(_wordpress_preferences__WEBPACK_IMPORTED_MODULE_3__.store).get('bc-events/frequency-condensed', 'condensed'));
  const isCustomOccurrencesCondensed = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useSelect)(select => select(_wordpress_preferences__WEBPACK_IMPORTED_MODULE_3__.store).get('bc-events/custom-occurrences-condensed', 'condensed'));
  const isOmitDatesCondensed = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useSelect)(select => select(_wordpress_preferences__WEBPACK_IMPORTED_MODULE_3__.store).get('bc-events/omit-dates-condensed', 'condensed'));
  const toggleCondensed = (key, value) => {
    set(`bc-events/${key}-condensed`, 'condensed', value);
  };
  const updateIsRecurring = val => {
    setMeta({
      ...meta,
      [META_IS_RECURRING]: val
    });
  };
  const LOCK_KEY = 'event-dates-remove-recurring-warning';
  const {
    lockPostSaving,
    unlockPostSaving
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useDispatch)(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__.store);
  const [userAllowDelete, setUserAllowDelete] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)(false);
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useEffect)(() => {
    if (!IS_RECURRING && IS_RECURRING !== IS_RECURRING_WAS && !userAllowDelete) {
      lockPostSaving(LOCK_KEY);
    } else {
      unlockPostSaving(LOCK_KEY);
    }
  }, [IS_RECURRING, IS_RECURRING_WAS, userAllowDelete]);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsxs)("div", {
    className: "bc-event-recurring",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)("div", {
      className: "bc-event__content-section",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(_formParts_RecurringButton_RecurringButton_jsx__WEBPACK_IMPORTED_MODULE_10__["default"], {
        id: "is-recurring",
        value: meta?.[META_IS_RECURRING] ?? false,
        onChange: val => updateIsRecurring(val)
      })
    }), IS_RECURRING && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsxs)("div", {
      className: "bc-event-recurring__inner",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(_RecurringAltered_jsx__WEBPACK_IMPORTED_MODULE_12__["default"], {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsxs)("div", {
        className: "bc-event-recurring__section",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(_SectionToggle_SectionToggle_jsx__WEBPACK_IMPORTED_MODULE_9__["default"], {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Frequency Options', 'basecadet'),
          value: isFreqCondensed,
          onChange: value => toggleCondensed('frequency', value),
          asTitle: false,
          titleTag: "h3"
        }), !isFreqCondensed && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)("div", {
          className: "bc-event__content-section",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(_Frequency_Frequency_jsx__WEBPACK_IMPORTED_MODULE_6__["default"], {})
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsxs)("div", {
        className: "bc-event-recurring__section",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(_SectionToggle_SectionToggle_jsx__WEBPACK_IMPORTED_MODULE_9__["default"], {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Specific Dates', 'basecadet'),
          value: isCustomOccurrencesCondensed,
          onChange: value => toggleCondensed('custom-occurrences', value),
          asTitle: false
        }), !isCustomOccurrencesCondensed && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)("div", {
          className: "bc-event__content-section",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(_CustomOccurences_CustomOccurences_jsx__WEBPACK_IMPORTED_MODULE_7__["default"], {})
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsxs)("div", {
        className: "bc-event-recurring__section",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(_SectionToggle_SectionToggle_jsx__WEBPACK_IMPORTED_MODULE_9__["default"], {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Exclusions', 'basecadet'),
          value: isOmitDatesCondensed,
          onChange: value => toggleCondensed('omit-dates', value),
          asTitle: false
        }), !isOmitDatesCondensed && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)("div", {
          className: "bc-event__content-section",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(_OmitDates_OmitDates_jsx__WEBPACK_IMPORTED_MODULE_8__["default"], {})
        })]
      })]
    }), !IS_RECURRING && IS_RECURRING_WAS && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(_RemoveRecurring_RemoveRecurring_jsx__WEBPACK_IMPORTED_MODULE_11__["default"], {
      onRecurringChange: val => updateIsRecurring(val),
      onDeleteChange: val => setUserAllowDelete(val),
      userAllowDelete: userAllowDelete
    })]
  });
}

/***/ },

/***/ "./editor/components/_sections/Recurring/RecurringAltered.jsx"
/*!********************************************************************!*\
  !*** ./editor/components/_sections/Recurring/RecurringAltered.jsx ***!
  \********************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ RecurringAltered)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_store_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../_utils/store.js */ "./editor/components/_utils/store.js");
/* harmony import */ var _blocks_src_event_dates_data_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../blocks/src/event-dates/data.js */ "./editor/blocks/src/event-dates/data.js");
/* harmony import */ var _formParts_BasicSelect_BasicSelect_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../_formParts/BasicSelect/BasicSelect.js */ "./editor/components/_formParts/BasicSelect/BasicSelect.js");
/* harmony import */ var _formParts_CheckboxButton_CheckboxButton_jsx__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../_formParts/CheckboxButton/CheckboxButton.jsx */ "./editor/components/_formParts/CheckboxButton/CheckboxButton.jsx");
/* harmony import */ var _formParts_CheckboxFormGroup_CheckboxFormGroup_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../_formParts/CheckboxFormGroup/CheckboxFormGroup.js */ "./editor/components/_formParts/CheckboxFormGroup/CheckboxFormGroup.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__);









function RecurringAltered() {
  const {
    keys,
    meta,
    setMeta
  } = (0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getStore)();
  const [hasRecurDiff, setHasRecurDiff] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.useState)(false);
  const USE_FREQUENCY = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('use_frequency', keys)];
  const START_DATE = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('start_date', keys)];
  const START_TIME = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('start_time', keys)];
  const END_DATE = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('end_date', keys)];
  const END_TIME = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('end_time', keys)];
  const FREQUENCY_VALUE = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq', keys)];
  const WEEKLY_DAYS = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_days', keys)];
  const MONTHLY_SCHED = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_mo_schedule', keys)];
  const MONTHLY_DAY = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_mo_day', keys)];
  const MONTHLY_DATE = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_mo_date', keys)];
  const END_TYPE_VALUE = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_end_type', keys)];
  const END_DATE_VALUE = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_end_date', keys)];
  const END_AFTER_X_VALUE = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('freq_end_after_x', keys)];
  const START_TIMESTAMP = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('start_timestamp', keys)];
  const OCCURENCES = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('custom_occurrences', keys)];
  const OMISSIONS = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('omissions', keys)];
  const RECUR_WAS = meta?.[(0,_utils_store_js__WEBPACK_IMPORTED_MODULE_1__.getKey)('recur_strategy_was', keys)];
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.useEffect)(() => {
    if (!RECUR_WAS) {
      setHasRecurDiff(false);
    } else {
      const currentRecur = {
        use_frequency: USE_FREQUENCY,
        frequency: FREQUENCY_VALUE,
        weekly_days: WEEKLY_DAYS,
        month_schedule: MONTHLY_SCHED,
        month_day: MONTHLY_DAY,
        month_date: MONTHLY_DATE,
        end_type: END_TYPE_VALUE,
        end_date: END_DATE_VALUE,
        end_after_x: END_AFTER_X_VALUE,
        start_date_timestamp: `${START_TIMESTAMP}`,
        occurences: OCCURENCES,
        omissions: OMISSIONS,
        primary_start_date: START_DATE,
        primary_start_time: START_TIME,
        primary_end_date: END_DATE,
        primary_end_time: END_TIME
      };
      setHasRecurDiff(!(0,lodash__WEBPACK_IMPORTED_MODULE_7__.isEqual)(currentRecur, RECUR_WAS));
    }
  }, [USE_FREQUENCY, FREQUENCY_VALUE, WEEKLY_DAYS, MONTHLY_SCHED, MONTHLY_DAY, MONTHLY_DATE, END_TYPE_VALUE, END_DATE_VALUE, END_AFTER_X_VALUE, START_TIMESTAMP, RECUR_WAS, OCCURENCES, OMISSIONS, START_DATE, START_TIME, END_DATE, END_TIME]);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.Fragment, {
    children: hasRecurDiff && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)("div", {
      className: "bc-events__recurring-notice",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsxs)("div", {
        className: "bc-events__recurring-notice-inner",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)("p", {
          className: "bc-event-dates__description",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)("strong", {
            children: "The recurring event pattern has changed."
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)("p", {
          className: "bc-event-dates__description",
          children: "Existing child events with dates that are not in the current recurring pattern will be deleted. This may include customized child events. Proceed with caution."
        })]
      })
    })
  });
}

/***/ },

/***/ "./editor/components/_sections/RemoveRecurring/RemoveRecurring.jsx"
/*!*************************************************************************!*\
  !*** ./editor/components/_sections/RemoveRecurring/RemoveRecurring.jsx ***!
  \*************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ RemoveRecurring)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../_utils/store */ "./editor/components/_utils/store.js");
/* harmony import */ var _formParts_BasicSelect_BasicSelect_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../_formParts/BasicSelect/BasicSelect.js */ "./editor/components/_formParts/BasicSelect/BasicSelect.js");
/* harmony import */ var _formParts_BasicCheckbox_BasicCheckbox_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../_formParts/BasicCheckbox/BasicCheckbox.js */ "./editor/components/_formParts/BasicCheckbox/BasicCheckbox.js");
/* harmony import */ var _formParts_CheckboxButton_CheckboxButton_jsx__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../_formParts/CheckboxButton/CheckboxButton.jsx */ "./editor/components/_formParts/CheckboxButton/CheckboxButton.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__);






function RemoveRecurring({
  onRecurringChange,
  onDeleteChange,
  userAllowDelete
}) {
  const {
    keys,
    meta,
    setMeta
  } = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getStore)();
  const META_REMOVE_RECURRING = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('remove_recurring', keys);
  const REMOVE_RECURRING = meta?.[META_REMOVE_RECURRING] ?? 'delete';
  const META_IS_RECURRING = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('is_recurring', keys);
  const IS_RECURRING = meta?.[META_IS_RECURRING] ?? false;
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("div", {
    className: "bc-event__content-section",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
      className: "bc-event__remove-recurring",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
        className: "bc-event__remove-recurring-content bc-event__content-section",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("h2", {
          className: "bc-events-title-h1",
          children: "Remove Recurring Events"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("p", {
          className: "bc-event-dates__description",
          children: "It seems like you're trying to remove recurring settings from an event that was previously set as recurring. This action will not take place until you save the post."
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_formParts_CheckboxButton_CheckboxButton_jsx__WEBPACK_IMPORTED_MODULE_4__["default"], {
          id: "use-recurring-reapply",
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Reset Recurring Settings', 'basecadet'),
          value: IS_RECURRING,
          onChange: val => setMeta({
            ...meta,
            [META_IS_RECURRING]: val
          }),
          smallButton: true
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
        className: "bc-event__remove-recurring-action bc-event__content-section",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
          className: "bc-event__remove-recurring-desc",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("p", {
            className: "bc-event__remove-recurring-desc-title",
            children: "This action cannot be undone."
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("p", {
            children: "Once saved:"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("ul", {
            className: "bc-event__remove-recurring-list",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("li", {
              children: "All child recurring events will be deleted"
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("li", {
              children: "All existing recurring settings will be reset to default settings"
            })]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("p", {
            className: "bc-event__important",
            children: "To continue, confirm by checking below:"
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_formParts_BasicCheckbox_BasicCheckbox_js__WEBPACK_IMPORTED_MODULE_3__["default"], {
          id: "user-allow-delete",
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('I understand that this action cannot be undone and I want to proceed.', 'basecadet'),
          checked: userAllowDelete,
          onChange: val => {
            onDeleteChange(val);
            setMeta({
              ...meta,
              [META_REMOVE_RECURRING]: 'delete'
            });
          }
        })]
      })]
    })
  });
}

/***/ },

/***/ "./editor/components/_sections/Validation/Validation.jsx"
/*!***************************************************************!*\
  !*** ./editor/components/_sections/Validation/Validation.jsx ***!
  \***************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Validation)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../_utils/store */ "./editor/components/_utils/store.js");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _Validators_MissingFields_jsx__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./Validators/MissingFields.jsx */ "./editor/components/_sections/Validation/Validators/MissingFields.jsx");
/* harmony import */ var _Validators_TimestampError_jsx__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./Validators/TimestampError.jsx */ "./editor/components/_sections/Validation/Validators/TimestampError.jsx");
/* harmony import */ var _Validators_MissingRecurringStrategy_jsx__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./Validators/MissingRecurringStrategy.jsx */ "./editor/components/_sections/Validation/Validators/MissingRecurringStrategy.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__);









const LOCK_KEY = 'event-dates-required-fields';
function Validation() {
  const {
    keys,
    meta
  } = (0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getStore)();
  const {
    lockPostSaving,
    unlockPostSaving
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.useDispatch)(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__.store);
  const [lockedBy, setLockedBy] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)([]);
  const [hasError, setHasError] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)(false);
  const handleLockPost = key => {
    setLockedBy(prev => prev.includes(key) ? prev : [...prev, key]);
  };
  const handleUnlockPost = key => {
    setLockedBy(prev => prev.filter(k => k !== key));
  };
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useEffect)(() => {
    if (lockedBy.length > 0) {
      setHasError(true);
      lockPostSaving(LOCK_KEY);
    } else {
      setHasError(false);
      unlockPostSaving(LOCK_KEY);
    }
  }, [lockedBy]);
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useEffect)(() => {
    return () => unlockPostSaving(LOCK_KEY);
  }, []);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsxs)("div", {
    className: `bc-event-dates__validation ${hasError ? 'has-error' : ''}`,
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_Validators_TimestampError_jsx__WEBPACK_IMPORTED_MODULE_6__["default"], {
      onError: handleLockPost,
      onSuccess: handleUnlockPost
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_Validators_MissingFields_jsx__WEBPACK_IMPORTED_MODULE_5__["default"], {
      onError: handleLockPost,
      onSuccess: handleUnlockPost
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_Validators_MissingRecurringStrategy_jsx__WEBPACK_IMPORTED_MODULE_7__["default"], {
      onError: handleLockPost,
      onSuccess: handleUnlockPost
    })]
  });
}

/***/ },

/***/ "./editor/components/_sections/Validation/ValidationNotice.jsx"
/*!*********************************************************************!*\
  !*** ./editor/components/_sections/Validation/ValidationNotice.jsx ***!
  \*********************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ ValidationNotice)
/* harmony export */ });
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__);

function ValidationNotice({
  status = 'error',
  children
}) {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("div", {
    className: `bce-events-validation-notice bce-events-validation-notice--${status}`,
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsxs)("div", {
      className: "bce-events-validation-notice__inner",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("div", {
        className: "bce-events-validation-notice__icon",
        "aria-hidden": "true",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsxs)("svg", {
          width: "24",
          height: "24",
          viewBox: "0 0 24 24",
          fill: "none",
          xmlns: "http://www.w3.org/2000/svg",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("path", {
            d: "M0.390362 22.7683C0.867185 23.5396 1.69314 24 2.59966 24H21.4003C22.3071 24 23.1331 23.5396 23.6096 22.7681C24.0865 21.9966 24.1289 21.0518 23.7234 20.2405L14.3232 1.43604C13.8803 0.550293 12.9901 0 11.9999 0C11.0099 0.00024415 10.1197 0.550293 9.67705 1.43604L0.276589 20.2405C-0.128942 21.0518 -0.0864602 21.9968 0.390362 22.7683ZM1.17115 20.6877L10.5716 1.8833C10.848 1.33032 11.3819 1 11.9999 1C12.6181 1 13.1523 1.33008 13.4286 1.8833L22.8288 20.6877C23.0784 21.1868 23.0522 21.7678 22.759 22.2424C22.4658 22.7168 21.958 23 21.4003 23H2.59966C2.04227 23 1.5342 22.7168 1.24122 22.2424C0.947754 21.7681 0.92163 21.1868 1.17115 20.6877Z",
            fill: "currentColor"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("path", {
            d: "M11.1586 16H12.8414C13.0956 16 13.3079 15.816 13.3307 15.5759L13.9981 8.50733C14.0103 8.37703 13.9642 8.24787 13.8712 8.15145C13.7781 8.05503 13.6466 8 13.5087 8H10.4913C10.3534 8 10.2219 8.05503 10.1288 8.15145C10.0358 8.24787 9.98971 8.37703 10.0019 8.50733L10.6693 15.5759C10.6921 15.816 10.9044 16 11.1586 16ZM12.9716 8.93144L12.3921 15.0686H11.6079L11.0284 8.93144H12.9716Z",
            fill: "currentColor"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("path", {
            d: "M12.0001 17C10.8971 17 10 17.8971 10 19.0001C10 20.1029 10.8971 21 12.0001 21C13.1029 21 14 20.1029 14 19.0001C14 17.8971 13.1029 17 12.0001 17ZM12.0001 19.9063C11.5003 19.9063 11.0937 19.4997 11.0937 19.0001C11.0937 18.5003 11.5003 18.0937 12.0001 18.0937C12.4997 18.0937 12.9063 18.5003 12.9063 19.0001C12.9063 19.4997 12.4997 19.9063 12.0001 19.9063Z",
            fill: "currentColor"
          })]
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("div", {
        className: "bce-events-validation-notice__content",
        children: children
      })]
    })
  });
}

/***/ },

/***/ "./editor/components/_sections/Validation/Validators/MissingFields.jsx"
/*!*****************************************************************************!*\
  !*** ./editor/components/_sections/Validation/Validators/MissingFields.jsx ***!
  \*****************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ MissingFields)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../_utils/store */ "./editor/components/_utils/store.js");
/* harmony import */ var _ValidationNotice_jsx__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../ValidationNotice.jsx */ "./editor/components/_sections/Validation/ValidationNotice.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);





function MissingFields({
  onError,
  onSuccess
}) {
  const {
    keys,
    meta
  } = (0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getStore)();
  const [missingFields, setMissingFields] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)([]);
  const IS_RECURRING = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('is_recurring', keys)];
  const USE_FREQ = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('use_frequency', keys)];
  const FREQ = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('freq', keys)] ?? 'daily';
  const WEEKLY_DAYS = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('freq_days', keys)] ?? [];
  const MONTHLY_SCHED = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('freq_mo_schedule', keys)];
  const MONTHLY_DAY = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('freq_mo_day', keys)];
  const MONTHLY_DATE = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('freq_mo_date', keys)];
  const FREQ_END_TYPE = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('freq_end_type', keys)] ?? 'on_date';
  const FREQ_END_DATE = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('freq_end_date', keys)];
  const FREQ_END_AFTER_X = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('freq_end_after_x', keys)];
  const OCCURENCES = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('custom_occurrences', keys)];
  const OMISSIONS = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('omissions', keys)];
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useEffect)(() => {
    const validationChecks = {};
    if (IS_RECURRING) {
      if (USE_FREQ && FREQ) {
        if (FREQ === 'weekly') {
          validationChecks.freq_days = {
            test: WEEKLY_DAYS.length > 0,
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Day(s) of the week', 'basecadet')
          };
        }
        if (FREQ === 'monthly') {
          if (MONTHLY_SCHED === 'date') {
            validationChecks.freq_mo_day = {
              test: MONTHLY_DAY !== '',
              label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Day of Month', 'basecadet')
            };
          }
        }
        if (FREQ_END_TYPE === 'on_date') {
          validationChecks.freq_end_date = {
            test: FREQ_END_DATE !== '',
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('At the end of day', 'basecadet')
          };
        }
        if (FREQ_END_TYPE === 'after_x') {
          validationChecks.freq_end_after_x = {
            test: FREQ_END_AFTER_X !== '',
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('After [X] Events', 'basecadet')
          };
        }
      }
      if (OCCURENCES && OCCURENCES.length && OCCURENCES.some(occ => !occ.start_date)) {
        validationChecks.custom_occurrences = {
          test: false,
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Specific Dates (empty start date values)', 'basecadet')
        };
      }
      if (OMISSIONS && OMISSIONS.length && OMISSIONS.some(occ => occ === '')) {
        validationChecks.omissions = {
          test: false,
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Exclusions (empty date values)', 'basecadet')
        };
      }
    }
    const dateFields = Object.keys(validationChecks).filter(key => validationChecks[key].test === false).map(key => validationChecks[key].label);
    setMissingFields(dateFields);
    if (dateFields.length) {
      onError('missing_fields');
    } else {
      onSuccess('missing_fields');
    }
  }, [IS_RECURRING, USE_FREQ, FREQ, WEEKLY_DAYS, MONTHLY_SCHED, MONTHLY_DAY, MONTHLY_DATE, FREQ_END_TYPE, FREQ_END_DATE, FREQ_END_AFTER_X, OCCURENCES, OMISSIONS]);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.Fragment, {
    children: missingFields && missingFields.length > 0 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)(_ValidationNotice_jsx__WEBPACK_IMPORTED_MODULE_3__["default"], {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("strong", {
          children: "Required Fields are missing values:"
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
        children: missingFields.join(', ')
      })]
    })
  });
}

/***/ },

/***/ "./editor/components/_sections/Validation/Validators/MissingRecurringStrategy.jsx"
/*!****************************************************************************************!*\
  !*** ./editor/components/_sections/Validation/Validators/MissingRecurringStrategy.jsx ***!
  \****************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ MissingRecurringStrategy)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../_utils/store */ "./editor/components/_utils/store.js");
/* harmony import */ var _ValidationNotice_jsx__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../ValidationNotice.jsx */ "./editor/components/_sections/Validation/ValidationNotice.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);





function MissingRecurringStrategy({
  onError,
  onSuccess
}) {
  const {
    keys,
    meta
  } = (0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getStore)();
  const [hasError, setHasError] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)(false);
  const IS_RECURRING = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('is_recurring', keys)] ?? false;
  const USE_FREQUENCY = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('use_frequency', keys)] ?? false;
  const OCCURENCES = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_2__.getKey)('custom_occurrences', keys)];
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useEffect)(() => {
    if (IS_RECURRING && !USE_FREQUENCY && !OCCURENCES.length) {
      setHasError(true);
      onError('missing_recurring_strategy');
    } else {
      setHasError(false);
      onSuccess('missing_recurring_strategy');
    }
  }, [IS_RECURRING, USE_FREQUENCY, OCCURENCES]);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.Fragment, {
    children: hasError && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)(_ValidationNotice_jsx__WEBPACK_IMPORTED_MODULE_3__["default"], {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("strong", {
          children: "Missing Recurring Strategy:"
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
        children: "A recurring event must have a frequency or specific dates defined."
      })]
    })
  });
}

/***/ },

/***/ "./editor/components/_sections/Validation/Validators/TimestampError.jsx"
/*!******************************************************************************!*\
  !*** ./editor/components/_sections/Validation/Validators/TimestampError.jsx ***!
  \******************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ TimestampError)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../_utils/store */ "./editor/components/_utils/store.js");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _ValidationNotice_jsx__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../ValidationNotice.jsx */ "./editor/components/_sections/Validation/ValidationNotice.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);





function TimestampError({
  onError,
  onSuccess
}) {
  const {
    keys,
    meta,
    setMeta
  } = (0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getStore)();
  const [errorMessage, setErrorMessage] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const START_TIMESTAMP_VALUE = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('start_timestamp', keys)] ?? '';
  const END_TIMESTAMP_VALUE = meta?.[(0,_utils_store__WEBPACK_IMPORTED_MODULE_1__.getKey)('end_timestamp', keys)] ?? '';
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (START_TIMESTAMP_VALUE && END_TIMESTAMP_VALUE && START_TIMESTAMP_VALUE === END_TIMESTAMP_VALUE) {
      setErrorMessage('Start and End dates and times cannot be the same.');
      onError('timestamp_error');
    } else if (START_TIMESTAMP_VALUE && END_TIMESTAMP_VALUE && START_TIMESTAMP_VALUE > END_TIMESTAMP_VALUE) {
      setErrorMessage('Start Date and Time must be before End Date and Time.');
      onError('timestamp_error');
    } else if (!START_TIMESTAMP_VALUE && !END_TIMESTAMP_VALUE || START_TIMESTAMP_VALUE && !END_TIMESTAMP_VALUE || !START_TIMESTAMP_VALUE && END_TIMESTAMP_VALUE || START_TIMESTAMP_VALUE === 0 && END_TIMESTAMP_VALUE === 0 || START_TIMESTAMP_VALUE === 0 && END_TIMESTAMP_VALUE || START_TIMESTAMP_VALUE && END_TIMESTAMP_VALUE === 0) {
      setErrorMessage('Start and end dates and times must be valid.');
      onError('timestamp_error');
    } else {
      setErrorMessage(false);
      onSuccess('timestamp_error');
    }
  }, [START_TIMESTAMP_VALUE, END_TIMESTAMP_VALUE]);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.Fragment, {
    children: errorMessage && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ValidationNotice_jsx__WEBPACK_IMPORTED_MODULE_3__["default"], {
      children: errorMessage
    })
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

/***/ "./editor/blocks/src/event-dates/editor.scss"
/*!***************************************************!*\
  !*** ./editor/blocks/src/event-dates/editor.scss ***!
  \***************************************************/
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

/***/ "lodash"
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
(module) {

module.exports = window["lodash"];

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

/***/ "@wordpress/components"
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
(module) {

module.exports = window["wp"]["components"];

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

/***/ "@wordpress/date"
/*!******************************!*\
  !*** external ["wp","date"] ***!
  \******************************/
(module) {

module.exports = window["wp"]["date"];

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

/***/ "./editor/blocks/src/event-dates/data.js"
/*!***********************************************!*\
  !*** ./editor/blocks/src/event-dates/data.js ***!
  \***********************************************/
(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DAY_OF_WEEK_OPTIONS: () => (/* binding */ DAY_OF_WEEK_OPTIONS),
/* harmony export */   FREQUENCY_OPTIONS: () => (/* binding */ FREQUENCY_OPTIONS),
/* harmony export */   RECURRING_MONTHLY_FREQUENCY_OPTIONS: () => (/* binding */ RECURRING_MONTHLY_FREQUENCY_OPTIONS)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");

const FREQUENCY_OPTIONS = [{
  value: 'daily',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Daily', 'basecadet')
}, {
  value: 'weekly',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Weekly', 'basecadet')
}, {
  value: 'monthly',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Monthly', 'basecadet')
}
// { value: 'yearly', label: __( 'Yearly', 'basecadet' ) },
];
const DAY_OF_WEEK_OPTIONS = [{
  value: 'sunday',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Sunday', 'basecadet')
}, {
  value: 'monday',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Monday', 'basecadet')
}, {
  value: 'tuesday',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Tuesday', 'basecadet')
}, {
  value: 'wednesday',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Wednesday', 'basecadet')
}, {
  value: 'thursday',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Thursday', 'basecadet')
}, {
  value: 'friday',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Friday', 'basecadet')
}, {
  value: 'saturday',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Saturday', 'basecadet')
}];
const RECURRING_MONTHLY_FREQUENCY_OPTIONS = [{
  value: 'first',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Every First', 'basecadet')
}, {
  value: 'second',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Every Second', 'basecadet')
}, {
  value: 'third',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Every Third', 'basecadet')
}, {
  value: 'fourth',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Every Fourth', 'basecadet')
}, {
  value: 'last',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Every Last', 'basecadet')
}, {
  value: 'every_other',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Every Other', 'basecadet')
}, {
  value: 'date',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('On a Specific Date', 'basecadet')
}];

/***/ },

/***/ "./editor/blocks/src/event-dates/edit.js"
/*!***********************************************!*\
  !*** ./editor/blocks/src/event-dates/edit.js ***!
  \***********************************************/
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
/* harmony import */ var _components_formParts_CheckboxButton_CheckboxButton_jsx__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../../components/_formParts/CheckboxButton/CheckboxButton.jsx */ "./editor/components/_formParts/CheckboxButton/CheckboxButton.jsx");
/* harmony import */ var _components_sections_EventDetails_EventDetails_jsx__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../../components/_sections/EventDetails/EventDetails.jsx */ "./editor/components/_sections/EventDetails/EventDetails.jsx");
/* harmony import */ var _components_sections_Recurring_Recurring_jsx__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../../../components/_sections/Recurring/Recurring.jsx */ "./editor/components/_sections/Recurring/Recurring.jsx");
/* harmony import */ var _components_sections_Validation_Validation_jsx__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../../../components/_sections/Validation/Validation.jsx */ "./editor/components/_sections/Validation/Validation.jsx");
/* harmony import */ var _components_sections_ChildEventDetails_ChildEventDetails_jsx__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../../../components/_sections/ChildEventDetails/ChildEventDetails.jsx */ "./editor/components/_sections/ChildEventDetails/ChildEventDetails.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");















function Edit() {
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__.useBlockProps)({
    className: 'bc-event-dates'
  });
  const {
    meta,
    setMeta
  } = (0,_components_utils_store_js__WEBPACK_IMPORTED_MODULE_7__.getStore)();
  const [isLoading, setIsLoading] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useState)(true);
  const [isLoadingError, setIsLoadingError] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useState)(false);
  const [isChild, setIsChild] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useState)(false);
  const [parentInfo, setParentInfo] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useState)({});
  const postID = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useSelect)(select => select('core/editor').getCurrentPostId());
  const isCondensed = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useSelect)(select => select(_wordpress_preferences__WEBPACK_IMPORTED_MODULE_2__.store).get('bc-events/details-condensed', 'condensed'));
  const isChildCondensed = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useSelect)(select => select(_wordpress_preferences__WEBPACK_IMPORTED_MODULE_2__.store).get('bc-events/child-details-condensed', 'condensed'));
  const {
    set
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useDispatch)(_wordpress_preferences__WEBPACK_IMPORTED_MODULE_2__.store);
  const handleToggle = value => {
    set('bc-events/details-condensed', 'condensed', value);
  };
  const handleChildToggle = value => {
    set('bc-events/child-details-condensed', 'condensed', value);
  };
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useEffect)(() => {
    const fetchKeys = async () => {
      try {
        const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5__({
          path: `/${_components_utils_store_js__WEBPACK_IMPORTED_MODULE_7__.REST_NAMESPACE}/get-keys`
        });
        (0,_components_utils_store_js__WEBPACK_IMPORTED_MODULE_7__.setKeys)(response);
      } catch (error) {
        console.error('Error fetching keys:', error);
        setIsLoadingError(true);
      } finally {
        setIsLoading(false);
      }
    };
    const checkIsChild = async () => {
      if (postID) {
        try {
          const url = (0,_wordpress_url__WEBPACK_IMPORTED_MODULE_6__.addQueryArgs)(`/${_components_utils_store_js__WEBPACK_IMPORTED_MODULE_7__.REST_NAMESPACE}/is-child`, {
            id: postID
          });
          const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5__({
            path: url
          });
          setIsChild(response);
        } catch (error) {
          console.error('Error fetching is_child:', error);
        }
      }
    };
    fetchKeys();
    checkIsChild();
  }, [postID]);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)("div", {
    ...blockProps,
    children: isLoading ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsxs)("div", {
      className: "bce-loader",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)("div", {
        className: "bce-sr-only",
        children: "Loading..."
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)("div", {
        className: "bce-loader__icon",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsxs)("svg", {
          width: "16",
          height: "16",
          viewBox: "0 0 16 16",
          fill: "none",
          xmlns: "http://www.w3.org/2000/svg",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)("path", {
            d: "M8.93626 0.37875L15.6063 7.04875C16.1213 7.56475 16.1213 8.41275 15.6063 8.92875L8.93626 15.5988C8.42026 16.1138 7.57226 16.1138 7.05626 15.5988L0.386251 8.92875C-0.12875 8.41275 -0.12875 7.56475 0.386251 7.04875L7.05626 0.37875C7.57626 -0.12625 8.41626 -0.12625 8.93626 0.37875Z",
            fill: "#F0F0F0"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)("path", {
            "fill-rule": "evenodd",
            "clip-rule": "evenodd",
            d: "M15.6063 7.04875L8.93626 0.37875C8.41626 -0.12625 7.57626 -0.12625 7.05626 0.37875L0.386251 7.04875C-0.12875 7.56475 -0.12875 8.41275 0.386251 8.92875L7.05626 15.5988C7.57226 16.1138 8.42026 16.1138 8.93626 15.5988L15.6063 8.92875C16.1213 8.41275 16.1213 7.56475 15.6063 7.04875ZM15.2763 8.59875L8.60626 15.2688C8.44526 15.4308 8.22526 15.5228 7.99626 15.5228C7.76726 15.5228 7.54726 15.4308 7.38626 15.2688L0.716251 8.59875C0.554251 8.43775 0.462251 8.21775 0.462251 7.98875C0.462251 7.75975 0.554251 7.53975 0.716251 7.37875L7.38626 0.70875C7.54626 0.54375 7.76626 0.45075 7.99626 0.45075C8.22626 0.45075 8.44626 0.54375 8.60626 0.70875L15.2763 7.37875C15.4383 7.53975 15.5303 7.75975 15.5303 7.98875C15.5303 8.21775 15.4383 8.43775 15.2763 8.59875Z",
            fill: "#0049DB"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)("path", {
            "fill-rule": "evenodd",
            "clip-rule": "evenodd",
            d: "M9.99622 4.25873C9.67722 4.01073 9.30322 3.84273 8.90622 3.76873C8.70522 3.72773 8.50122 3.70473 8.29622 3.69873H6.84622C6.33021 3.69873 5.90621 4.12273 5.90621 4.63873V5.43873C4.78521 5.77173 4.01221 6.80973 4.01221 7.97873C4.01221 9.14773 4.78521 10.1858 5.90621 10.5188V11.3188C5.90621 11.8348 6.33021 12.2588 6.84622 12.2588H8.24622C8.45022 12.2508 8.65422 12.2308 8.85622 12.1988C9.27122 12.1228 9.66222 11.9478 9.99622 11.6888C10.6313 11.1778 11.0003 10.4038 10.9963 9.58873C10.9823 9.00973 10.7783 8.45073 10.4163 7.99873C10.7923 7.53073 10.9973 6.94873 10.9963 6.34873C11.0003 5.53673 10.6313 4.76573 9.99622 4.25873ZM9.99622 8.12873C10.3573 8.52973 10.5563 9.04973 10.5563 9.58873C10.5733 10.6238 9.83422 11.5298 8.81622 11.7188C8.63822 11.7518 8.45722 11.7718 8.27622 11.7788H6.84622C6.58822 11.7788 6.37621 11.5668 6.37621 11.3088V10.6088C6.48322 10.6138 6.58921 10.6138 6.69622 10.6088H8.54622C8.71222 10.5848 8.86822 10.5158 8.99622 10.4088C9.25322 10.2158 9.40222 9.91073 9.39622 9.58873C9.39522 9.26873 9.24722 8.96673 8.99622 8.76873C8.87222 8.67673 8.72822 8.61573 8.57622 8.58873H6.84622C6.33021 8.58873 5.90621 9.01273 5.90621 9.52873V9.99873C5.05421 9.67873 4.48621 8.85873 4.48621 7.94873C4.48621 7.03873 5.05421 6.21873 5.90621 5.89873V6.43873C5.90621 6.95473 6.33021 7.37873 6.84622 7.37873H7.91622C8.12622 7.39673 8.33622 7.39673 8.54622 7.37873C8.71222 7.35073 8.86822 7.27773 8.99622 7.16873C9.26422 6.98173 9.42422 6.67573 9.42422 6.34873C9.42422 6.02173 9.26422 5.71573 8.99622 5.52873C8.87022 5.43773 8.72722 5.37273 8.57622 5.33873C8.45322 5.33173 8.32922 5.33173 8.20622 5.33873H6.69622C6.58921 5.33373 6.48322 5.33373 6.37621 5.33873V4.63873C6.37321 4.51573 6.42022 4.39673 6.50622 4.30873C6.59622 4.21873 6.71922 4.16873 6.84622 4.16873H8.22622C8.40622 4.15673 8.58622 4.15673 8.76622 4.16873C9.09422 4.23173 9.40322 4.37273 9.66622 4.57873C10.1963 4.99173 10.5063 5.62673 10.5063 6.29873C10.5303 6.85073 10.3483 7.39273 9.99622 7.81873L9.79622 7.99873L9.99622 8.12873ZM6.74622 10.1288C6.64022 10.1388 6.53222 10.1388 6.42621 10.1288V9.47873C6.41921 9.23473 6.60422 9.02473 6.84622 8.99873H8.15622C8.25622 8.98673 8.35622 8.98673 8.45622 8.99873C8.53722 9.00573 8.61322 9.03673 8.67622 9.08873C8.81422 9.19773 8.89522 9.36373 8.89622 9.53873C8.89922 9.71473 8.81722 9.88273 8.67622 9.98873C8.61222 10.0398 8.53622 10.0738 8.45622 10.0888C8.35622 10.0968 8.25622 10.0968 8.15622 10.0888H6.69622L6.74622 10.1288ZM6.37621 5.79873C6.48222 5.78873 6.59021 5.78873 6.69622 5.79873H8.45622C8.53622 5.81373 8.61222 5.84773 8.67622 5.89873C8.81622 6.00573 8.89822 6.17273 8.89622 6.34873C8.89922 6.52473 8.81722 6.69273 8.67622 6.79873C8.61222 6.84973 8.53622 6.88373 8.45622 6.89873C8.28022 6.91373 8.10222 6.91373 7.92622 6.89873H6.84622C6.58822 6.89873 6.37621 6.68673 6.37621 6.42873V5.79873Z",
            fill: "#0049DB"
          })]
        })
      })]
    }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)("div", {
      className: "bc-event-dates__container",
      children: isChild ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.Fragment, {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)(_components_SectionToggle_SectionToggle_jsx__WEBPACK_IMPORTED_MODULE_8__["default"], {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Child Event Details', 'basecadet'),
          value: isChildCondensed,
          onChange: value => handleChildToggle(value),
          asTitle: true,
          titleTag: "h2"
        }), !isChildCondensed && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)(_components_sections_ChildEventDetails_ChildEventDetails_jsx__WEBPACK_IMPORTED_MODULE_13__["default"], {
          parentData: isChild
        })]
      }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.Fragment, {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)(_components_sections_Validation_Validation_jsx__WEBPACK_IMPORTED_MODULE_12__["default"], {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)(_components_SectionToggle_SectionToggle_jsx__WEBPACK_IMPORTED_MODULE_8__["default"], {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Event Details', 'basecadet'),
          value: isCondensed,
          onChange: value => handleToggle(value),
          asTitle: true,
          titleTag: "h2"
        }), !isCondensed && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.Fragment, {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)(_components_sections_EventDetails_EventDetails_jsx__WEBPACK_IMPORTED_MODULE_10__["default"], {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)(_components_sections_Recurring_Recurring_jsx__WEBPACK_IMPORTED_MODULE_11__["default"], {})]
        })]
      })
    })
  });
}

/***/ },

/***/ "./editor/components/_formParts/BasicCheckbox/BasicCheckbox.js"
/*!*********************************************************************!*\
  !*** ./editor/components/_formParts/BasicCheckbox/BasicCheckbox.js ***!
  \*********************************************************************/
(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ BasicCheckbox)
/* harmony export */ });
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");

function BasicCheckbox({
  id,
  checked,
  onChange,
  label,
  flip = false,
  asToggle = true
}) {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsxs)("div", {
    className: `bc-event-dates__checkbox-group bc-event-dates__input-row${flip ? ' bc-event-dates__checkbox-group--flip' : ''}`,
    children: [flip && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("label", {
      className: `bc-event-dates__label ${asToggle ? 'bc-event-dates__checkbox--toggle-label' : 'bc-event-dates__checkbox-label'}`,
      htmlFor: id,
      children: label
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("input", {
      className: `${asToggle ? 'bc-event-dates__checkbox--toggle' : 'bc-event-dates__checkbox'}`,
      type: "checkbox",
      id: id,
      checked: checked,
      onChange: e => onChange(e.target.checked)
    }), !flip && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("label", {
      className: `bc-event-dates__label ${asToggle ? 'bc-event-dates__checkbox--toggle-label' : 'bc-event-dates__checkbox-label'}`,
      htmlFor: id,
      children: label
    })]
  });
}

/***/ },

/***/ "./editor/components/_formParts/BasicSelect/BasicSelect.js"
/*!*****************************************************************!*\
  !*** ./editor/components/_formParts/BasicSelect/BasicSelect.js ***!
  \*****************************************************************/
(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ BasicSelect)
/* harmony export */ });
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");

function BasicSelect({
  id,
  value,
  options,
  onChange,
  label,
  className = null
}) {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsxs)("div", {
    className: `bc-event-dates__select-group bc-event-dates__input-row ${className ? className : ''}`,
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("label", {
      className: "bc-event-dates__label bc-event-dates__select-group-label",
      htmlFor: id,
      children: label
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("select", {
      className: "bc-event-dates__input bc-event-dates__select bc-event-dates__select-group-select",
      id: id,
      value: value,
      onChange: e => onChange(e.target.value),
      children: options.map(option => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("option", {
        value: option.value,
        children: option.label
      }, option.value))
    })]
  });
}

/***/ },

/***/ "./editor/components/_formParts/CheckboxFormGroup/CheckboxFormGroup.js"
/*!*****************************************************************************!*\
  !*** ./editor/components/_formParts/CheckboxFormGroup/CheckboxFormGroup.js ***!
  \*****************************************************************************/
(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ CheckboxFormGroup)
/* harmony export */ });
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");

function CheckboxFormGroup({
  id,
  values,
  options,
  onChange,
  label
}) {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsxs)("formgroup", {
    className: "bc-event-dates__checkbox-form-group",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("div", {
      className: "bc-event-dates__label bc-event-dates__checkbox-form-group-title",
      children: label
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("div", {
      className: "bc-event-dates__checkbox-form-group-inner",
      children: options.map(option => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsxs)("div", {
        className: "bc-event-dates__checkbox-form-group-option",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("input", {
          className: "bc-event-dates__checkbox--toggle bc-event-dates__checkbox-form-group-checkbox",
          type: "checkbox",
          id: `${id}-${option.value}`,
          checked: values.includes(option.value),
          onChange: e => {
            const newValues = e.target.checked ? [...values, option.value] : values.filter(val => val !== option.value);
            onChange(newValues);
          }
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("label", {
          className: "bc-event-dates__label bc-event-dates__checkbox-form-group-label",
          htmlFor: `${id}-${option.value}`,
          children: option.label
        })]
      }, `formgroup-${id}-${option.value}`))
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

/***/ "./editor/blocks/src/event-dates/block.json"
/*!**************************************************!*\
  !*** ./editor/blocks/src/event-dates/block.json ***!
  \**************************************************/
(module) {

module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"bc-events/event-dates","version":"0.1.0","title":"Event Dates","category":"basecadetContent","icon":"calendar","description":"Sets the start and end dates for an event, stored as post meta.","keywords":["event","date","dates","schedule"],"supports":{"html":false,"align":false,"multiple":false},"editorScript":"file:./index.js","editorStyle":"file:./index.css","render":"file:./render.php"}');

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
/*!************************************************!*\
  !*** ./editor/blocks/src/event-dates/index.js ***!
  \************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _edit_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit.js */ "./editor/blocks/src/event-dates/edit.js");
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./editor.scss */ "./editor/blocks/src/event-dates/editor.scss");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./block.json */ "./editor/blocks/src/event-dates/block.json");




(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_3__.name, {
  ..._block_json__WEBPACK_IMPORTED_MODULE_3__,
  edit: _edit_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: () => null
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map