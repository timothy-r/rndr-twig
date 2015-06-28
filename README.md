# render
A service that renders templates

## Example

Run the app in a docker container to make redis available
    
    docker-compose build
    docker-compose up

Make a request to render the hello.twig template with name set to 'Brian' (requires the hello.twig template to have been uploaded)

    curl -X POST \
        -d '{"name":"Brian"}' \
        -H "Content-Type: application/json" \
        http://192.168.59.103:49300/hello.twig
    
The response will be the result of rendering the template file named "hello.twig" with the variable $name set to "Brian"

Or if you would prefer to use a different content type to send the data

    curl -X POST \
        -d "name=Brian" \
        -H "Content-Type: application/x-www-form-urlencoded" \
        http://192.168.59.103:49300/hello.twig
    
Or even
    
    curl -X POST \
        http://192.168.59.103:49300/hello.twig?name=Brian


* Missing template files receive a 404 response 
* Requests to render templates must be POSTs

## Adding templates

To add a template to the service make a PUT request

    curl -X PUT \
        -d @template.twig \
        http://192.168.59.103:49300/new-template.twig
        
The template will be available at http://192.168.59.103/new-template.twig

## Viewing template contents

To view the raw contents of a template make a GET request

    curl -X GET \
        http://192.168.59.103:49300/new-template.twig
        
The response body will be the unrendered template text

## Removing stale templates

To remove a template that's no longer needed make a DELETE request

    curl -X DELETE \
        http://192.168.59.103:49300/old-template.twig
