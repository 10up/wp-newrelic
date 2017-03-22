/**
 * WP New Relic
 * http://wordpress.org/plugins
 *
 * Copyright (c) 2017 10up
 * Licensed under the GPLv2+ license.
 */

( function( window, $, _, Backbone, undefined ) {
	'use strict';

	/*
	 * Representation of the settings for a single dashboard widget
	 *
	 */
	var DashboardWidgetModel = Backbone.Model.extend({
		defaults: {
			title: '',
			embedID: '',
			description: '',
		},

		embedUrl: function embedUrl() {
			var embedID = this.get( 'embedID' );
			return embedID ? "https://insights-embed.newrelic.com/embedded_widget/" + embedID : "";
		}
	});

	/*
	 * Backbone view controlling the form for deleting and adding dashboard widgets
	 *
	 */
	var DashboardWidgetTable = Backbone.View.extend({

		widgets: {},

		i18n: {},

		initialize: function() {
			this.widgets = WP_NewRelic.dashboardWidgets;
			this.strings = WP_NewRelic.strings;
			this.render();
		},

		render: function() {
			var previewTemplate = _.template( $( "#view-dashboard-widget" ).html() ),
			    addNewTemplate  = _.template( $( "#add-edit-dashboard-widget" ).html() ),
			    strings         = this.strings,
			    self            = this;

			_.each( self.widgets, function( widget, i ) {
				var widgetModel = new DashboardWidgetModel( widget );
				self.$el.append( previewTemplate( { i: i, model: widgetModel, strings: strings }) );
			} );

			this.$el.append( addNewTemplate( { strings: strings } ) );
		},

		deleteWidget: function( $widgetView ) {
			$widgetView.remove();
			return false;
		},

		events: {
			'click .submitdelete': function(e) {
				var widget =  $( e.currentTarget ).closest( '.form-table' );
				this.deleteWidget( widget );
				return false;
			}
	},

	} );

	// Entry point: on load, render the Backbone view holding the form, using
	// the settings data and translation strings passed in through localization variables.
	document.addEventListener( 'DOMContentLoaded', function() {
		var dashboard_widget = document.getElementById( 'wp-nr-widget-settings-form' );

		if ( ! dashboard_widget ) {
			return;
		}

		new DashboardWidgetTable( { el: $( dashboard_widget ) } );
	} );

} )( this, jQuery, _, Backbone );
