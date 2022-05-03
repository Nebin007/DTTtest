<?php
require_once '../API/facility.php';
$fc = new facility();

$router->get('', App\Controllers\IndexController::class . '@index');

$router->get('/read', function(){
    global $fc;
    if($fc->isauth())
    {
        $fc->getdetails();
    }
});

$router->post('/create/{q}',function($q){
    global $fc;
    if($fc->isauth())
    {
        $q = ltrim($q, '?');
        parse_str($q, $postval);
        $tags = explode(",",$postval['tags']);
        $fc->addDetails(
            $postval['facilityname'],
            $tags,
            $postval['city'],
            $postval['address'],
            $postval['zipcode'],
            $postval['countrycode'],
            $postval['phonenumber']
        );
    }

});

$router->put('/update/{q}',function($q){
    global $fc;
    if($fc->isauth())
    {
        $q = ltrim($q, '?');
        parse_str($q, $updateval);
        $tags = explode(",",$updateval['tags']);
        $fc->updetails(
            $updateval['id'],
            $updateval['fname'],
            $updateval['doc'],
            $tags
        );
    }
});

$router->delete('/delete/{q}',function($q){
    global $fc;
    if($fc->isauth()){
    $q = ltrim($q, '?');
    parse_str($q, $delval);
    $fc->deletefac($delval['id']);
    }
});

$router->get('/search/{q}',function($q){
    global $fc;
    if($fc->isauth()){
    $q = ltrim($q, '?');
    parse_str($q, $searchqury);
    $fc->searchitems($searchqury['search']);}
});

$router->get('/auth',function(){
    global $fc;
    $fc->auth();
});
