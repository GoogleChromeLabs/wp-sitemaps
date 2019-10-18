@db
Feature: Test the tests
  As a developer
  I want to be sure that the functional testing framework is performing as expected
  So that I can write and run accurate functional tests

  Scenario: A developer can rely on the functional testing framework
    Given I am logged in as role administrator
    And I am on the dashboard
    Then I should be on the Dashboard screen
