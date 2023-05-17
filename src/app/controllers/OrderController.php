<?php
use Phalcon\Mvc\Controller;

session_start();
class OrderController extends Controller
{
    public function indexAction()
    {
        // generate the dropdown here
        $collection = $this->mongo->product;
        $data = $collection->find();
        $arr = [];
        foreach ($data as $value) {
            $arr["$value[_id]"] = $value['name'];
        }
        $this->view->message = $arr;
    }

    public function addAction()
    {
        $uname = $_POST['uname'];
        $quantity = $_POST['quantity'];
        $productId = $_SESSION['productId'];
        $variation = $_POST['select_variation'];

        $collection = $this->mongo->product;
        $dbData = $collection->findOne(
            ["_id" => new MongoDB\BSON\ObjectId($productId)]
        );
        // assert order quantity < stock
        if ($quantity > $dbData['stock']) {
            $this->view->error = "Enter a lower Quantity";
            return;
        }
        if ($quantity < 0) {
            $this->view->error = "Enter a valid Quantity";
            return;
        }
        $arr = [
            "uname" => $uname,
            "quantity" => $quantity,
            "variation" => $variation,
            "productId" => $productId,
            "status" => "paid",
            "date" => date('Y-m-d'),
        ];
        $collection = $this->mongo->order;
        $collection->insertOne($arr);

        // update the product quantity in db
        $collection = $this->mongo->product;
        $collection->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($productId)],
            ['$set' => ['stock' => ($dbData['stock'] - $quantity)]]
        );

        $this->response->redirect('/order/displayAll');
    }
    public function displayAllAction()
    {
        if ($this->request->get('order_status')) {
            $order_status = $_POST['order_status'];
            $date_range = $_POST['date_range'];
            $start_date = date('Y-m-d');
            if ($date_range == 'today') {
                $end_date = date('Y-m-d');
            } elseif ($date_range == 'week') {
                $end_date = date('Y-m-d', strtotime('+7 days'));
            } elseif ($date_range == 'month') {
                $end_date = date('Y-m-d', strtotime('+30 days'));
            } else {
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
            }
            $collection = $this->mongo->order;
            $data = $collection->find(
                [
                    'status' => $order_status,
                    '$and' => [['date' => ['$gte' => $start_date]], ['date' => ['$lte' => $end_date]]]
                ]
            );
        } else {
            $collection = $this->mongo->order;
            $data = $collection->find();
        }
        $this->view->message = $data;
    }
    public function editAction()
    {
        $oid = $_GET['id'];
        $collection = $this->mongo->order;
        $data = $collection->findOne(
            ["_id" => new MongoDB\BSON\ObjectId($oid)]
        );
        $this->view->message = $data;
    }

    public function updateAction()
    {
        $id = $_GET['id'];
        $status = $_POST['order_status'];
        $collection = $this->mongo->order;
        $collection->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($id)],
            ['$set' => ['status' => $status]]
        );
        $this->response->redirect('/order/displayAll');
    }
    public function generateDropdownAction()
    {
        $id = $_POST['id'];
        $collection = $this->mongo->product;
        $data = $collection->findOne(
            ["_id" => new MongoDB\BSON\ObjectId($id)]
        );
        $output = "";
        $_SESSION['productId'] = $id;
        foreach ($data['attributes'] as $key => $value) {
            $output .= "<option value='$key'>$value</option>";
        }
        
        $_SESSION['dropdown'] = $output;
        return $output;
    }
}