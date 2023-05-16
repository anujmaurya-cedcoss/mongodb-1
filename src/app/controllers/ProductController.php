<?php
use Phalcon\Mvc\Controller;

class ProductController extends Controller
{
    public function indexAction()
    {
        // redirected to view
    }

    public function addAction()
    {
        // echo "<pre>";
        // print_r($_POST); 
        $name = $this->request->getPost('name');
        $category = $this->request->getPost('category');

        $n = count($this->request->getPost('meta_key'));
        $key = [];
        $value = [];
        for ($i=0; $i < $n; $i++) {
            // array_push($key, $_POST['meta_key'][$i]);
            // array_push($value, $_POST['meta_value'][$i]);
            $key[] = $_POST['meta_key'][$i];
            $value[] = $_POST['meta_value'][$i];
        }
        echo "<pre>";
        print_r(array_values($key));
        print_r($value);
        $meta = array_combine($key, $value);
        $m = count($this->request->getPost('variation_key'));
        $variation = [];
        for ($i=0; $i < $m; $i++) {
            array_push($variation, [$this->request->getPost('variation_key')[$i] => $this->request->getPost('variation_value')[$i]]);
        }

        echo "<pre>";
        print_r(array_values($meta));
        print_r(array_values($variation));
        die;
        
        $collection = $this->mongo->product;
        $collection->insertOne($this->request->getPost());
        $this->response->redirect('/product/show');
    }

    public function showAction()
    {
        if ($this->request->get('search')) {
            $keyword = $this->request->getPost('search');
            $collection = $this->mongo->product;
            $data = $collection->find(["name" => $keyword]);
            $this->view->message = $data;
        } else {
            $collection = $this->mongo->product;
            $data = $collection->find();
            $this->view->message = $data;
        }
    }

    public function editAction()
    {
        $id = $_GET['id'];
        $collection = $this->mongo->product;
        $data = $collection->findOne(["_id" => new MongoDB\BSON\ObjectId($id)]);
        $this->view->message = $data;
    }

    public function updateAction()
    {
        $id = $_GET['id'];
        $collection = $this->mongo->product;
        $collection->updateOne(["_id" => new MongoDB\BSON\ObjectId($id)], ['$set' => $_POST]);
        $this->response->redirect('/product/show');
    }

    public function deleteAction()
    {
        $id = $_GET['id'];
        $collection = $this->mongo->product;
        $collection->deleteOne(["_id" => new MongoDB\BSON\ObjectId($id)]);
        $this->response->redirect('/product/show');
    }
}
