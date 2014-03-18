var itExchangeVariantsAdmin = itExchangeVariantsAdmin || {};

(function($){

	'use strict';

	itExchangeVariantsAdmin.addEditVariantValueView = Backbone.View.extend({

		template : wp.template( 'it-exchange-admin-variant-value' ),

		render : function () {
			this.id = 'variant-' + this.model.get('id');
			this.$el.html( 'THIS IS A TEST' );
			//this.$el.html( this.template( this.model.toJSON() ) );

			return this;
		}
		
	}),

	itExchangeVariantsAdmin.addEditVariantView = Backbone.View.extend({

		tagName : 'div',

		className : 'variant',

		template : wp.template( 'it-exchange-admin-variant' ),

		render : function () {
			this.id = 'variant-' + this.model.get('id');
			this.$el.html( this.template( this.model.toJSON() ) );

			//this.$el.find('.variant-values').html('I need to find a way to throw a template in here for each of this variant\'s values');
			//var variantValue = new itExchangeVariantsAdmin.addEditVariantValueView( { el: this.$('.variant-values') });
			return this;
		}

	});
}(jQuery));
