# PinteREST
PinteREST is a REST API for Twitter. It is implemented in PHP and scrapes the Pinterest website to provide a RESTful API in a
 * JSON
 * JSONP
 * or XML
response.

## Instructions for use
PinteREST accepts the following GET parameters:
 1. user - The username of the Pinterest account
 2. boards - A comma separated list of boards. The data returned will be structured as an array of boards
 3. limit - The max number of pins to return. If boards are specified, this is the limit of pins per board.
 4. format - Format can be json, jsonp, or xml.

## Examples

## Future Features
 * Smart caching
 * Better error codes
 * Support for scraping new Pinterest design

## Other Notes
PinteREST uses the Simple HTML DOM parser to scrape the Pinterest site.