<?php


/**
 * @namespace Tonic\Exerciser\Username
 * @uri /([A-Za-z0-9_]+)
 */
class UsernameResource extends Resource {
    
    /**
     * Handle a GET request for this resource
     * @param Request request
     * @return Response
     */
    function get($request) {
        $response = new Response($request);
        
        $etag = md5($request->uri);
        if ($request->ifNoneMatch($etag)) {
            
            $response->code = Response::NOTMODIFIED;
            
        } else {
            
            $response->code = Response::OK;
            $response->addHeader('Content-type', 'text/plain');
            $response->addEtag($etag);
            $response->body = $request->method . "User";
            
        }
        
        return $response;
        
    }


    /**
     * Handle a PUT request for this resource
     * @param Request request
     * @return Response
     */
    function put($request) {
        $response = new Response($request);
        
        $etag = md5($request->uri);
        if ($request->ifNoneMatch($etag)) {
            
            $response->code = Response::NOTMODIFIED;
            
        } else {
            
            $response->code = Response::OK;
            $response->addHeader('Content-type', 'text/plain');
            $response->addEtag($etag);
            $response->body = $request->method . "User";
            
        }
        
        return $response;
        
    }


    /**
     * Handle a DELETE request for this resource
     * @param Request request
     * @return Response
     */
    function delete($request) {
        $response = new Response($request);
        
        $etag = md5($request->uri);
        if ($request->ifNoneMatch($etag)) {
            
            $response->code = Response::NOTMODIFIED;
            
        } else {
            
            $response->code = Response::OK;
            $response->addHeader('Content-type', 'text/plain');
            $response->addEtag($etag);
            $response->body = $request->method . "User";
            
        }
        
        return $response;
        
    }
    
}
?>
