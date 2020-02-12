var preventAction = function(e){

	//console.log(parent.currentPinType, parent);

	//if ( parent.currentPinType != "browse" ) {

		//alert('Clicked');

		e.preventDefault();
		e.stopImmediatePropagation();
		e.stopPropagation();

	//}

};

document.addEventListener('click', preventAction, false);
// document.addEventListener('dblclick', preventAction, false);
// document.addEventListener('mousedown', preventAction, false);
// document.addEventListener('mouseup', preventAction, false);