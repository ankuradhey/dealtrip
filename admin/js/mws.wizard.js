/*
 * MWS Admin v1.6 - Wizard Form JS
 *
 * This file is part of MWS Admin, an Admin template build for sale at ThemeForest.
 * All copyright to this file is hold by Mairel Theafila <maimairel@yahoo.com> a.k.a nagaemas on ThemeForest.
 *
 * Last Updated:
 * May 20, 2012
 *
 * 'Highly configurable' mutable plugin boilerplate
 * Author: @markdalgleish
 * Further changes, comments: @addyosmani
 * Licensed under the MIT license
 *
 */

;(function( $, window, document, undefined ) {
	// our plugin constructor
	var mwsWizard = function( elem, options ) {
		this.$elem = $(elem);
		this.options = options;
    };
	
	// the plugin prototype
	mwsWizard.prototype = {
		defaults: {
			element: 'fieldset', 
			navigationContainer: '.mws-wizard-nav', 
			buttonContainerClass: 'mws-button-row', 
			nextButtonClass: 'mws-button red', 
			prevButtonClass: 'mws-button gray left', 
			submitButtonClass: 'mws-button green', 
			nextButtonLabel: 'Next', 
			prevButtonLabel: 'Prev', 
			submitButtonLabel: 'Submit', 
			forwardOnly: true, 
			onLeaveStep: null, 
			onShowStep: null, 
			onBeforeSubmit: null
		}, 
		
		init: function() {				
			// Introduce defaults that can be extended either
			// globally or using an object literal.
			this.config = $.extend({}, this.defaults, this.options);
			
			this.steps = $(this.config.element, this.$elem);
			
			this.$elem.addClass('mws-wizard-form');
			
			this.data = $.extend({}, this._parseNavigation(), this._buildButtons());
			
			this.initSteps();
			
			return this;
		}, 
		
		// Public methods
		
		initSteps: function() {
			this.steps.hide().first().show();
			this.data.activeStep = 0;
			this.data.stepsMap[this.data.activeStep].anchor.parent().addClass('active');
			this._processSteps();
		}, 
		
		goTo: function(stepNumber) {
			var self = this;
			
			if(stepNumber !== self.data.activeStep) {
				if(this.config.forwardOnly && stepNumber < self.data.activeStep)
					return;
				
				if(self.config.onLeaveStep && $.isFunction(self.config.onLeaveStep)) {
					if(false === self.config.onLeaveStep.apply(self, [self.data.activeStep, self.steps.eq(self.data.activeStep)]))
						return;
				}
				
				self.steps.filter(self.data.stepsMap[self.data.activeStep].id).data('done', true).fadeOut('fast', function() {
					self.steps.filter(self.data.stepsMap[stepNumber].id).fadeIn('fast');
					
					self.data.stepsMap[self.data.activeStep].anchor.parent().removeClass('current').addClass('done');
					self.data.stepsMap[stepNumber].anchor.parent().addClass('current');
							
					self.data.activeStep = stepNumber;
					self._processSteps();
					
					if(self.config.onShowStep && $.isFunction(self.config.onShowStep)) {
						self.config.onShowStep.apply(self, [stepNumber, self.steps.eq(stepNumber)]);
					}
				});
			}
		}, 
		
		goBackward: function() {
			if(this.data.activeStep > 0) {
				this.goTo(this.data.activeStep - 1);
			}
		}, 
		
		goForward: function() {
			if(this.data.activeStep < this.steps.size() - 1) {
				this.goTo(this.data.activeStep + 1);
			}
		}, 
		
		submitForm: function() {
			var shouldSubmit = true;
			if(this.config.onBeforeSubmit && $.isFunction(this.config.onBeforeSubmit)) {
				shouldSubmit = this.config.onBeforeSubmit.apply(self, []);
			}
			
			if(false !== shouldSubmit) this.$elem.submit();
		}, 
		
		// Private methods
		
		_navigate: function(stepNumber) {
			if(this._isStepDone(stepNumber))
				this.goTo(stepNumber);
		}, 
		
		_isStepDone: function(stepNumber) {
			return (typeof(this.steps.filter(this.data.stepsMap[stepNumber].id).data('done')) !== 'undefined');
		}, 
		
		_parseNavigation: function() {
			var self = this, 
				navContainer = this.$elem.find(this.config.navigationContainer), 
				stepsMap = navContainer.find('ul li a').map(function(k, v) {
					$(v).bind('click', function(event) {
						self._navigate(k);
						event.preventDefault();
					});
					
					return { id: $(v).attr('href'), anchor: $(v) };
				}).get();
				
			return { activeStep: -1, stepsMap: stepsMap };
		}, 
		
		_buildButtons: function() {
			var btnContainer = $(this.config.buttonContainer, this.$elem), 
				self = this, 
				$button = $('<input />').attr('type', 'button'), 
				$prevButton = $button.clone().val(this.config.prevButtonLabel).addClass(this.config.prevButtonClass).bind('click', function(event) {
					if(!self.config.forwardOnly)
						self.goBackward();
					event.preventDefault();
				}), 
				$nextButton = $button.clone().val(this.config.nextButtonLabel).addClass(this.config.nextButtonClass).bind('click', function(event) {
					self.goForward();
					event.preventDefault();
				}), 
				$submitButton = $button.clone().val(this.config.submitButtonLabel).addClass(this.config.submitButtonClass).bind('click', function(event) {
					self.submitForm();
					event.preventDefault();
				});
			
			if(!btnContainer.get(0)) {
				btnContainer = $('<div></div>').addClass(this.config.buttonContainerClass).appendTo(this.$elem);
			}
			
			if(!self.config.forwardOnly)
				btnContainer.append($prevButton);
			btnContainer.append($nextButton).append($submitButton);
			
			return { nextButton: $nextButton, prevButton: $prevButton, submitButton: $submitButton };
		}, 
		
		_processSteps: function() {
			this.data.prevButton.toggle((this.data.activeStep > 0) && !this.data.forwardOnly);
			this.data.nextButton.toggle((this.data.activeStep < this.steps.size() - 1));
			this.data.submitButton.toggle((this.data.activeStep >= this.steps.size() - 1));
		}
	}
	
	mwsWizard.defaults = mwsWizard.prototype.defaults;
	
	$.fn.mwsWizard = function(options) {
		return this.each(function() {
			new mwsWizard(this, options).init();
		});
	};

})( jQuery, window , document );
