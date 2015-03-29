# render
A service that renders templates

An example:

Run the app using (in the public directory)
    
    php -S 127.0.0.1:8080 

Make a request to render the hello template with name set to 'Brian'

    curl -X POST \
        -d '{"name":"Brian"}' \
        -H "Content-Type: application/json" \
        http://127.0.0.1:8080/hello.twig
    
The response will be the result of rendering the template file named "hello.twig" with the variable $name set to "Brian"

Or if you would prefer to use a different content type to send the data

    curl -X POST \
        -d "name=Brian" \
        -H "Content-Type: application/x-www-form-urlencoded" \
        http://127.0.0.1:8080/hello.twig
    
Or even
    
    curl -X POST \
        http://127.0.0.1:8080/hello.twig?name=Brian


* Missing template files receive a 404 response. 
* Requests to render templates must be POSTs.

## Maintaining templates

To add a template to the service make a PUT request
    curl -X PUT \
        -d @template.twig \
        http://127.0.0.1/new-template.twig
        
The template will be available at http://127.0.0.1/new-template.twig

To view the raw contents of a template make a GET request
    curl -X PUT \
        http://127.0.0.1/new-template.twig
        
The response body will be the unrendered template text
