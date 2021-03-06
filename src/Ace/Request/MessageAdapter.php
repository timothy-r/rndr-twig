<?php namespace Ace\Request;

use Symfony\Component\HttpFoundation\Request;

/**
 * Provides access to a Request's data as an assoc array
 * Accepts form data, json and query parameters
 */
class MessageAdapter
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $req)
    {
        $this->request = $req;
    }

    /**
     * Using the request content type return its data as an array
     * @return array
     */
    public function getData()
    {
        $req_vars = [];

        switch($this->request->headers->get('Content-Type')) {

            case 'application/json':
                $req_vars = json_decode($this->request->getContent(), 1);
                if (!is_array($req_vars)){
                    $req_vars = [];
                }
                break;

            case 'application/x-www-form-urlencoded':
            case 'multipart/form-data':
                $req_vars = $this->request->request->all();
                break;
        }

        $query = $this->request->query->all();

        if (is_array($query)) {
            // values in $req_vars overwrite those in $query
            $req_vars = array_merge($query, $req_vars);
        }

        return $req_vars;
    }

}