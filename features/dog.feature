Feature:
  I want to test a mock guzzle client

  Scenario: I want to test a mock guzzle client
    Given The DogClient Is Mocked
      And The DogClient Will Return a Mocked Response
      And I send a "GET" request to "/api/dogs"
    Then the response status code should not be 200
      And the response status code should be 402
      And the response should be equal to
      """
      {"mock":true}
      """
