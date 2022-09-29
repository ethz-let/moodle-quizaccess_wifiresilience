@quizaccess @quizaccess_wifiresilience @quizaccess_wifiresilience_7
Feature: Wifiresilience mode can restore a working session if the use gets logged out.
  In order to not lose data if I am logged out
  As a student
  I need the the system to be able to connect to a new session.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname |
      | student  | Study     |
    And the following "course enrolments" exist:
      | user    | course | role    |
      | student | C1     | student |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name       | questiontext |
      | Test questions   | truefalse | Question A | Answer me A  |
    And the following "activities" exist:
      | activity   | name                | course | idnumber | questionsperpage | wifiresilience_enabled |
      | quiz       | Quiz Wifiresilience | C1     | quiz1    | 1                | 1                      |
    And quiz "Quiz Wifiresilience" contains the following questions:
      | Question A | 1 |
    And the quiz auto-save period is set to "2"

  @javascript
  Scenario: User logs out and logs in again elsewhere, we handle it.
    When I am on the "Quiz Wifiresilience" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I wait "15" seconds
    And  I simulate losing the session by changing sesskey
    And I click on "True" "radio" in the "Answer me A" "question"
    And I wait "4" seconds
    Then I should not see "Save failed."
    And I should not see "Answer changed"
    # The nest step is important to make sure that the data is recorded as all being saved.
    And I click on "C1" "link"
    
