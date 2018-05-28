Feature: I want to test a mock guzzle client

  Scenario: The api return a success response
    Given The DogClient Will Return the "api_dog_success.php" response
      And I send a "GET" request to "/api/dogs"
    Then the response status code should be 200
      And the response should be equal to
      """
      {"mock":true,"success":true}
      """

  Scenario: The api throw an error
    Given The DogClient Will Return the "api_dog_error.php" response
      And I send a "GET" request to "/api/dogs"
    Then the response status code should be 402
      And the response should be equal to
      """
      {"mock":true,"error":"fail"}
      """
