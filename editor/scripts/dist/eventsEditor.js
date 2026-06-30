/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "@wordpress/components"
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
(module) {

module.exports = window["wp"]["components"];

/***/ },

/***/ "./editor/components/_utils/slots.js"
/*!*******************************************!*\
  !*** ./editor/components/_utils/slots.js ***!
  \*******************************************/
(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AfterEventDetailsFill: () => (/* binding */ AfterEventDetailsFill),
/* harmony export */   AfterEventDetailsSlot: () => (/* binding */ AfterEventDetailsSlot),
/* harmony export */   AfterRecurringFill: () => (/* binding */ AfterRecurringFill),
/* harmony export */   AfterRecurringSlot: () => (/* binding */ AfterRecurringSlot)
/* harmony export */ });
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");

const {
  Fill: AfterEventDetailsFill,
  Slot: AfterEventDetailsSlot
} = (0,_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.createSlotFill)('AfterEventDetails');
const {
  Fill: AfterRecurringFill,
  Slot: AfterRecurringSlot
} = (0,_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.createSlotFill)('AfterRecurring');

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
/*!********************************************!*\
  !*** ./editor/scripts/src/eventsEditor.js ***!
  \********************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_utils_slots_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/_utils/slots.js */ "./editor/components/_utils/slots.js");


// Expose slots for editor extensions to use without having to import the slots file directly 
window.BluecadetEvents = window.BluecadetEvents || {};
window.BluecadetEvents.AfterEventDetailsFill = _components_utils_slots_js__WEBPACK_IMPORTED_MODULE_0__.AfterEventDetailsFill;
window.BluecadetEvents.AfterRecurringFill = _components_utils_slots_js__WEBPACK_IMPORTED_MODULE_0__.AfterRecurringFill;

/**
 * Keeping this code for now...it may come back at some point...
 */
// import { useEffect, useRef } from '@wordpress/element';
// import { useSelect, dispatch, select } from '@wordpress/data';
// import { store as blockEditorStore } from '@wordpress/block-editor';
// import { registerPlugin } from '@wordpress/plugins';

// function EventDatesEnforcer() {
// 	const isDragging = useSelect( ( sel ) =>
// 		sel( blockEditorStore ).isDraggingBlocks()
// 	);

// 	const wasDragging = useRef( false );

// 	useEffect( () => {
// 		const dragJustEnded = wasDragging.current && ! isDragging;
// 		wasDragging.current = isDragging;

// 		if ( ! dragJustEnded ) return;

// 		const blocks = select( blockEditorStore ).getBlocks();
// 		const idx    = blocks.findIndex( ( b ) => b.name === 'bc-events/event-dates' );

// 		if ( idx > 0 ) {
// 			dispatch( blockEditorStore ).moveBlocksToPosition(
// 				[ blocks[ idx ].clientId ], '', '', 0
// 			);
// 		}
// 	}, [ isDragging ] );

// 	return null;
// }

// Disabled: the always-available "Event Settings" panel (eventsPluginPanel.js) now
// guarantees the meta is editable regardless of the block's presence/position, so the
// block is freely movable/removable and no longer needs pinning to position 0.
// Re-enable this line to restore the enforced placement.
// registerPlugin( 'bc-events-enforcer', { render: EventDatesEnforcer } );
})();

/******/ })()
;
//# sourceMappingURL=eventsEditor.js.map