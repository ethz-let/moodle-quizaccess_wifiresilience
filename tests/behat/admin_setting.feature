@quizaccess @quizaccess_wifiresilience @quizaccess_wifiresilience_1
Feature: Wifi Resilience Mode admin setting
  In order to save teachers time
  As an admin
  I need to set a default for whether Wifi Resilience Mode is enabled for new quizzes.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And I log in as "admin"
    And I am on site homepage

  @javascript
  Scenario: Wifi Resilience Mode defaults to disabled.
    When I follow "Course 1"
    And I turn editing mode on
    And I add a "Quiz" to section "0"
    Then the field "Wifi Resilience Mode" matches value "No"

  @javascript
  Scenario: The default can be changed so that Wifi Resilience Mode is enabled by default.
    When I navigate to "Plugins > Activity modules > Quiz > Quiz Wifi Resilience Mode" in site administration
    And I set the field "Wifi Resilience Mode" to "1"
    And I press "Save changes"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Quiz" to section "0"
    Then the field "Wifi Resilience Mode" matches value "Yes"
