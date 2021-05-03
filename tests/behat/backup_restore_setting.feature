@quizaccess @quizaccess_wifiresilience @quizaccess_wifiresilience_2
Feature: Wifi Resilience Mode backup and restore of quiz settings
  In order to reuse quizzes using Wifi Resilience Mode
  As a teacher
  I need be able to backup courses with and without that setting.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity   | name                | course | idnumber | wifiresilience_enabled |
      | quiz       | Quiz Wifiresilience | C1     | quiz1    | 1                      |
      | quiz       | Quiz normal         | C1     | quiz2    | 0                      |
    And I log in as "admin"

  @javascript
  Scenario: Change the setting for a quiz from off to on.
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 2 |
    And I follow "Wifiresilience"
    And I navigate to "Edit settings" in current page administration
    Then the field "Wifi Resilience Mode" matches value "Yes"
    And I follow "Course 2"
    And I follow "Quiz normal"
    And I navigate to "Edit settings" in current page administration
    And the field "Wifi Resilience Mode" matches value "No"
