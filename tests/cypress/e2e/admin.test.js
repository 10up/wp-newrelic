describe("Admin can login and open dashboard", () => {
  beforeEach(() => {
    cy.login();
  });

  it("Open dashboard", () => {
    cy.visit(`/wp-admin`);
    cy.get("h1").should("contain", "Dashboard");
  });

  it("Deactivate 'New Relic Reporting for WordPress' and activate it back", () => {
    cy.deactivatePlugin("wp-newrelic");
    cy.activatePlugin("wp-newrelic");
  });

  it( 'Can see "New Relic" menu and Can visit "New Relic" settings page.', () => {
		cy.visit( '/wp-admin/' );

		// Check ClassifAI menu.
		cy.get( '#adminmenu li#menu-tools ul.wp-submenu li' ).contains(
			'New Relic'
		);

		// Check Heading
		cy.visit( '/wp-admin/tools.php?page=wp-nr-settings' );
		cy.get( 'h1' ).contains( 'New Relic for WordPress' ).should( 'exist' );
	} );

  it( 'Can save "New Relic" settings.', () => {
		cy.visit( '/wp-admin/tools.php?page=wp-nr-settings' );
		cy.get( 'input[name="wp_nr_capture_urls"' ).check();
    cy.get( 'input#submit' ).click();

    cy.visit( '/wp-admin/tools.php?page=wp-nr-settings' );
    cy.get( 'input[name="wp_nr_capture_urls"' ).should( 'be.checked' );

    cy.visit( '/wp-admin/tools.php?page=wp-nr-settings' );
		cy.get( 'input[name="wp_nr_capture_urls"' ).uncheck();
    cy.get( 'input#submit' ).click();

    cy.visit( '/wp-admin/tools.php?page=wp-nr-settings' );
    cy.get( 'input[name="wp_nr_capture_urls"' ).should( 'not.be.checked' );
	} );
});
