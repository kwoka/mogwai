# Lesson One

## Overview

This lesson covers a very basic front end login form, which can be seen here:
http://mogwai.sveltefox.com/lesson-one/front-end/

This form submits the login request to a very basic API which performs the expected steps:
* routes the request to the correct action
* validates the submitted data
* checks the supplied credentials match what's in the database
* outputs response according to outcome

Since this sample uses very basic raw HTML and no javascript, when the form is submitted, control is directed to the page that's the form's target, which happens to be:
http://mogwai.sveltefox.com/lesson-one/public/login

This is the API which accepts the POST request with the email and password, validates the input, checks the database for the supplied credentials, and returns the outcome.

## Structure

There are a few directories in this lesson, which might need some explaination:

### front-end

This directory, as the name obviously implies, is the front end portion of the app. This is the HTML/CSS files that accept the user input and submit the request to the server to verify the login credentials.

### data

This contains the SQL to set up your own version of the database. Right now there's one table and this script creates the database, user, table, and single test data row.

### src

These are the PHP classes that make up this very basic MVC implementation. The classes include:

* App - This is the app container, this is the main class of the MVC
* Request - Represents and incoming request
* Response - Represents an outgoing response
* Router - Handles defining and resolving routes
* Validator - Handles input validation

### public

This is the publicly available entry point into the API. Contained within are:

* htaccess - this is for apache to redirect all requests to index.php. This should be renamed to .htaccess when deployed, an optional RewriteBase can be set, if you need to do so, it will be honored.
* index.php - This is the driver for the app. Here is where the classes in the src directory are utilized and the app is executed.

### config

This is the directory where you store your application configurations. You can add your own settings.php file to customize the settings you need to be provided to your app.

## Quiz Questions

Currently when you submit the form, control is redirected to the server itself, and you will see the JSON output from the API method, similar to:
```
{
	"returnCode": 3,
	"error": "Invalid Credentials."
}
```

In a more proper app, you would not send the user to an API endpoint because JSON output isn't very useful to your end users. Therefore, we want to submit the login request to the API and remain on the login page, as opposed to redirecting the user to the API itself.

In order to submit an HTTP request without reloading the page, we need to utilize Javascripts ability to asynchronously send requests in the background without a page reload.

1. Utilizing native JavaScript or a library of your choice, implement an AJAX request to submit the login data without reloading the page. The page should NOT reload when you submit the form, but your JavaScript code should capture the input fields, send the data to the server at http://mogwai.sveltefox.com/lesson-one/public/login, read the response, and display to the user whether or not their login attempt was successful.

The API has a number of returnCodes that indicate success or failure. Here are the possible outcomes and the response from the request:

### Invalid Parameters

The required parameters are:
email - valid email address
password - user's password

If you do not provide both of these pieces of data, you will recieve a response with an HTTP code of 400 (Bad Request) and the following JSON response body:
```
{
	"returnCode": 1,
	"error": "Invalid Parameters."
}
```

### Invalid Credentials

When you submit a request and the credentials are not correct, you will receive a response with an HTTP code of 401 (Unauthorized) and the following JSON response body:
```
{
	"returnCode": 3,
	"error": "Invalid Credentials."
}
```

### User Not Found

When you provide an email that does not match a user in the database, you will receive a response with an HTTP code of 404 (Not Found) and the following JSON response body:
```
{
	"returnCode": 2,
	"error": "User Not Found."
}
```

### Database Error

In the case where something unexpected happens when checking the user in the database, you will receive a response with an HTTP code of 500 (Server Error) and the following JSON response body:
```
{
	"returnCode": 4,
	"error": "Database Error Occurred."
}
```

### Success

In the case where you provide both a correct email and password, you will receive a response with an HTTP code of 200 (OK) and the following JSON response body:
```
{
	"returnCode": 0,
	"userId": 1
}
```

The returnCode of 0 indicates success, and the userId is the unique id for the user you have logged in as.

## Sample User

Here's the test data which you can use for a valid user account:
* email - test@test.com
* password - tester

## How To Submit Your Exercise

* Fork this project. 
* Create your feature branch (`git checkout -b your-new-branch`)
* Make the changes
* Test them
* When you are satisfied, commit your changes (`git commit -am 'Implemented AJAX for Login'`)
* Push your changes to your feature branch (`git push origin your-new-branch`)
* Create a new pull request, setting the base as this project and the branch your request to merge as your new feature branch
