@quizaccess @quizaccess_wifiresilience @quizaccess_wifiresilience_5
Feature: Fault-tolerant mode quiz setting
  In order to run quizzes with dodgy wifi
  As a teacher
  I need to turn the fault-tolerant quiz mode on and off.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname |
      | teacher  | Teachy    |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name   | course | idnumber | wifiresilience_enabled |
      | quiz       | Quiz 1 | C1     | quiz1    | 0                      |
      | quiz       | Quiz 2 | C1     | quiz2    | 1                      |


  @javascript
  Scenario: Create a quiz with the setting on.
    When I log in as "teacher"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Quiz" to section "0" and I fill the form with:
      | Name                 | Quiz Wifiresilience |
      | Wifi Resilience Mode | Yes                 |
    And I am on the "Quiz Wifiresilience" "mod_quiz > View" page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    Then the field "Wifi Resilience Mode" matches value "Yes"

  @javascript
  Scenario: Create a quiz with the setting off.
    When I log in as "teacher"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Quiz" to section "0" and I fill the form with:
      | Name                 | Quiz Normal |
      | Wifi Resilience Mode | No          |
    And I am on the "Quiz Normal" "mod_quiz > View" page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    Then the field "Wifi Resilience Mode" matches value "No"

  @javascript
  Scenario: Change the setting for a quiz from off to on.
    When I log in as "teacher"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Wifi Resilience Mode" to "Yes"
    And I press "Save and display"
    And I navigate to "Settings" in current page administration
    Then the field "Wifi Resilience Mode" matches value "Yes"

  @javascript
  Scenario: Change the setting for a quiz from on to off.
    When I log in as "teacher"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Quiz 2"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Wifi Resilience Mode" to "No"
    And I press "Save and display"
    And I navigate to "Settings" in current page administration
    Then the field "Wifi Resilience Mode" matches value "No"

  @javascript
  Scenario: The experimental setting is disabled if you select an interactive behaviour.
    When I log in as "teacher"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Quiz" to section "0"
    And I expand all fieldsets
    And I set the field "How questions behave" to "Adaptive mode"
    Then the "Wifi Resilience Mode" "field" should be disabled
