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
});
