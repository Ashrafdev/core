@api @setsTokenAuthEnforced
Feature: tokenAuth

	Background:
		Given using API version "1"
		And user "user0" has been created
		And token auth has been enforced

	Scenario: download a file with range
		Given using new DAV path
		When user "user0" downloads file "/welcome.txt" with range "bytes=51-77" using the API
		Then the downloaded content should be "example file for developers"

	# FILES APP

	Scenario: access files app with an app password when token auth is enforced
		And a new browser session for "user0" has been started
		And the user has generated a new app password named "my-client"
		When the user requests "/index.php/apps/files" with "GET" using the generated app password
		Then the HTTP status code should be "200"

	Scenario: Create a user
		Given user "brand-new-user" has been deleted
		When the administrator sends a user creation request for user "brand-new-user" password "456firstpwd" using the API
		Then the OCS status code should be "100"
		And the HTTP status code should be "200"

	Scenario: cannot access files app with basic auth when token auth is enforced
		When user "user0" requests "/index.php/apps/files" with "GET" using basic auth
		Then the HTTP status code should be "999"

	# WebDAV

	Scenario: using WebDAV with basic auth when token auth is enforced
		When user "user0" requests "/remote.php/webdav" with "PROPFIND" using basic auth
		Then the HTTP status code should be "207"

	# OCS
