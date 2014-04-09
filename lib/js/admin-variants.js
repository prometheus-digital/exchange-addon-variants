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

		// Product Images if available
		if (typeof itExchangeVariants.ProductImagesVariantsMetaBoxView == 'function') { 
			itExchangeVariants.productImagesVariantsMetaBoxView = new itExchangeVariants.ProductImagesVariantsMetaBoxView(); 
//			itExchangeVariants.productImagesVariantsMetaBoxView.render();
		}
	});
}(jQuery));
