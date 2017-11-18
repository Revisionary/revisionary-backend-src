// GLOBAL VARIABLES
var iframe,
	pins;

// IDs
var page_ID;
var version_ID;

// HTML Element Index
var fileIndexed = false;
var indexCount = 0;
var elementCount = 0;
var element_index_nonce;

// Focus Variables
var focused_element,
	focused_element_children,
	focused_element_grand_children,
	focused_element_index,
	focused_element_text,
	focused_pin_type,
	focused_element_has_pin,
	focused_element_has_live_pin,
	focused_element_editable,
	focused_element_html_editable;

// Activator Pin
var activator;
var cursorActive;

// Pin Mode Selector
var pinTypeSelector;
var pinTypeSelectorOpen;

// Detect initial cursor mode
var cursor;
var currentCursorType = "standard";

var currentPinType = "live";
var currentPinPrivate = 0;
var currentPinComplete = 0;

var currentPinNumber = 1;

// Iframe ???
var mouseInTheFrame = false;

// Hovers
var hoveringText = false;
var hoveringImage = false;
var hoveringButton = false;
var hoveringPin = false;

// Scrolls
var scrollOffset_top = 0;
var scrollOffset_left = 0;

// Initial Scale
var iframeScale = 1;

// Pin Window
var pinWindowOpen = false;

/*
MIGHT BE NEEDED

addMode = false;
pinWindowOpen = false;
currentText = "";
currentDevice = "Desktop";
*/


// When document is ready, fill the variables
$(function() {

	pins = $('#pins > pin');

	activator = $('.inspect-activator').children('pin');
	cursorActive = activator.hasClass('active');

	pinTypeSelector = $('.pin-type-selector');
	pinTypeSelectorOpen = pinTypeSelector.parent().hasClass('selector-open');

	cursor = $('.mouse-cursor');
	currentPinType = activator.data('pin-type');
	currentPinNumber = pins.length + 1;

});