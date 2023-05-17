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
        $n = count($this->request->getPost('meta_key'));
        $meta = [];
        for ($i = 0; $i < $n; $i++) {
            $key = $_POST['meta_key'][$i];
            $value = $_POST['meta_value'][$i];
            $meta[$key] = $value;
        }
        $m = count($this->request->getPost('variation_key'));
        $variation = [];
        for ($i = 0; $i < $m; $i++) {
            $key = $_POST['variation_key'][$i];
            $value = $_POST['variation_value'][$i];
            $variation[$key] = $value;
        }
        $arr = [
            "name" => $_POST['name'],
            "category" => $_POST['category'],
            "price" => $_POST['price'],
            "stock" => $_POST['stock'],
            "meta" => $meta,
            "attributes" => $variation
        ];
        $collection = $this->mongo->product;
        $collection->insertOne($arr);
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

    public function quickViewAction() {
        $id = $_GET['id'];
        $collection = $this->mongo->product;
        $data = $collection->findOne(["_id" => new MongoDB\BSON\ObjectId($id)]);
        $this->view->message = $data;
    }
}
