/**
 * Usage:
 *
 * $('id_element').select('a.className').each(function(item){
 *	new SwitcherFlag(item);
 * });
 *
 */
var SwitcherFlag = Class.create({
	initialize: function(elem) {
		this.elem = elem;
		this.url = this.elem.getAttribute('href');
		
		this.elem.observe('click', this.onClickHandler.bindAsEventListener(this));
	},
	
	onClickHandler: function(evt) {
		Event.stop(evt);
		
		var img = this.elem.select('img')[0];
		img.src = img.src.sub(/(.*?)([^\/]+$)/, '#{1}') + 'loading16.gif';
		
		new Ajax.Request(this.url, {
			onSuccess: this.onSuccessHandler.bind(this)
		});
	},
	
	onSuccessHandler: function(transport) {
		this.elem.update(transport.responseText);
	}
});