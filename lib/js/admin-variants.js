var itExchangeVariants = itExchangeVariants || {};

(function($){
	$(function() {
		itExchangeVariants.adminMetaBoxView = new itExchangeVariants.AdminMetaBoxView();
		itExchangeVariants.adminMetaBoxView.render();

		// Inventory if available
		if (typeof itExchangeVariants.ProductInventoryVariantsMetaBoxView == 'function') { 
			itExchangeVariants.productInventoryVariantsMetaBoxView = new itExchangeVariants.ProductInventoryVariantsMetaBoxView(); 
			itExchangeVariants.productInventoryVariantsMetaBoxView.render();
		}
	});
}(jQuery));
