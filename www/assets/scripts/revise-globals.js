// GLOBAL VARIABLES
var iframe;

// IDs
var user_ID;
var device_ID;
var page_ID;
var page_URL;
var remote_URL;
var page_ready = false;
var page_redirected = false;

// HTML Element Index
var fileIndexed = false;
var indexCount = 0;
var elementCount = 0;
var easy_html_elements = [
    "A",
	"B",
	"I",
	"U",
	"EM",
	"STRONG",
	"STRIKE",
	"SMALL",
	"TEXTAREA",
	"LABEL",
	"BUTTON",
	"TIME",
	"DATE",
	"ADDRESS",
	"P",
	"DIV",
	"SPAN",
	"LI",
	"H1",
	"H2",
	"H3",
	"H4",
	"H5",
	"H6"
];
var easy_with_br = easy_html_elements;
easy_with_br.push("BR");
easy_with_br.push("IMG");

// Focus Variables
var focused_element,
	focused_element_children,
	focused_element_grand_children,
	focused_element_index,
	focused_element_text,
	focused_element_pin,
	focused_element_has_pin,
	focused_element_has_live_pin,
	focused_element_editable,
	focused_element_html_editable,
	focused_element_edited_parents,
	focused_element_has_edited_child;

// Activator Pin
var activator;
var cursorActive;
var cursorWasActive;

// PINS
var pinModes = {
	'live' 			: 'Content and View Changes',
	'standard' 		: 'View Changes',
	'private-live'	: 'Private Content and View Changes',
	'private'		: 'Private View Changes'
};

// Pin Mode Selector
var pinTypeSelector;
var pinTypeSelectorOpen;

// Detect initial cursor mode
var cursor;
var currentCursorType = "standard";

var currentPinType = "live";
var currentPinPrivate = 0;
var currentPinLabel = pinModes.live;

var currentPinNumber = 1;

// Mouse
var offset = 0;
var mouseInTheFrame = false;
var screenX = 0;
var screenY = 0;
var containerX = 0;
var containerY = 0;
var screenWidth = 10;
var screenHeight = 10;

// Hovers
var hoveringText = false;
var hoveringImage = false;
var hoveringButton = false;
var hoveringPin = false;
var focusedPin = null;

// Scrolls
var scrollOffset_top = 0;
var scrollOffset_left = 0;
var oldScrollOffset_top = 0;
var oldScrollOffset_left = 0;
var scrollX;
var scrollY;

// Initial Scale
var iframeScale = 1;
var iframeWidth = 0;
var iframeHeight = 0;

// Pin Window
var	pinWindow;
var pinWindowOpen = false;
var pinWindowWasOpen = false;
var pinWindowWidth = 350;
var pinWindowHeight = 515;

// Pins
var Pins = [];
var pinDragging = false;
var autoRefreshTimer;
var autoRefreshInterval = 5000;
var autoRefreshRequest = null;
var pinsListOpen = false;
var pinAnimation, pinAnimationTimeout;
var commentsGetRequest = null;


// When document is ready, fill the variables
$(function() {

	activator = $('.current-mode');
	cursorActive = activator.hasClass('active');
	cursorWasActive = cursorActive;

	pinWindow = $('#pin-window');
	pinWindowWidth = pinWindow.outerWidth();
	pinWindowHeight = pinWindow.outerHeight();

	pinTypeSelector = $('.pin-type-selector');
	pinTypeSelectorOpen = pinTypeSelector.parent().hasClass('selector-open');

	cursor = $('.mouse-cursor');

	currentPinType = activator.data('pin-type');
	currentPinPrivate = activator.data('pin-private');

	currentPinNumber = $('#pins > pin').length + 1;

	pinsListOpen = $('#top-bar .pins').hasClass('open');

});