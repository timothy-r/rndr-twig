# render
A service that renders templates

An example:

Run the app using (in the public directory)
    
    php -S 127.0.0.1:8080 

Make a request to render the hello template with the name var set to 'Brian'

    curl -X POST \
        -d '{"name":"Brian"}' \
        -H "Content-Type: application/json" \
        http://127.0.0.1:8080/hello
    
The response will be the result of rendering the template file name hello.twig with the variable $name set to "Brian"

Or if you would prefer to use a different content type to send the data

    curl -X POST \
        -d "name=Brian" \
        -H "Content-Type: application/x-www-form-urlencoded" \
        http://127.0.0.1:8080/hello
    
Or even
    
    curl -X POST \
        http://127.0.0.1:8080/hello?name=Brian


* Missing template files receive a 404 response. 
* All requests must be POSTs.
