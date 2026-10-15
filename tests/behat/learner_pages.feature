@theme @theme_iqralight
Feature: Iqralight guides learners through their courses
  In order to learn with few taps and little data
  As a learner
  I need to see where to continue and what each activity asks of me

  Background:
    Given the following "courses" exist:
      | fullname | shortname | enablecompletion |
      | Course 1 | C1        | 1                |
    And the following "users" exist:
      | username | firstname | lastname |
      | student1 | Amina     | Learner  |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following "activities" exist:
      | activity | name                 | course | content |
      | page     | Introducing yourself | C1     | Hello   |
      | quiz     | Short quiz           | C1     |         |
    And the following config values are set as admin:
      | theme | iqralight |

  Scenario: The course page shows an action verb above each activity
    When I am on the "Course 1" course page logged in as student1
    Then I should see "Read"
    And I should see "Take the quiz"

  Scenario: The home page welcomes the learner and offers to resume
    Given I am on the "Introducing yourself" "page activity" page logged in as student1
    When I visit "/my/courses.php"
    Then I should see "Hello, Amina"
    And I should see "Pick up where you left off"

  Scenario: A large Next button leads to the following activity
    When I am on the "Introducing yourself" "page activity" page logged in as student1
    Then I should see "Next: Short quiz"
