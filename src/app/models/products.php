<?php
use Phalcon\Mvc\Model;

class Products extends Model
{
    $data = $this->mongo->selectCollection("listingsAndReviews");
        echo "<pre>";
        print_r($data->findOne()); die;    
}
