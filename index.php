<?php
class product
{
    //定义一个商品列表
    public $product_list;
    //定一个数据存储路径
    public $file_path = 'product.json';
    public $file_path_id = 'last_id.json';
    //定义一个从页面获取数据的变量
    public $params;
    //定一个  最后一个ID
    public $last_id;

    //定一个初始运行的函数
    function __construct()
    {
        //获取ID的值
        $this->last_id = $this->read_id() ?: 0;
        //从json文件中取出数据 赋给product_list
        $this->product_list = $this->read() ?: [];
        //获取页面输入的信息  并放到定义好的变量中
        $this->params = array_merge($_POST, $_GET);
        //判断调用那个函数  方法是页面输入的
        $action = $this->params['action'];
        //每个方法返回的数据  再把它返回给页面
        $result = $this->$action();
        $this->json($result);
    }

    function add()
    {
        $title = $this->params['title'];
        $price = $this->params['price'];
        if (!$title || !$price) {
            return ['success' => false];
        }
        $this->product_list[] = [
            'title' => $title,
            'price' => $price,
            'id' => $this->last_id + 1,
        ];
        $this->add_id();
        $this->up_json();

        return ['success' => true];
    }

    function del()
    {
        $index = $this->params['id'];
        unset($this->product_list[$index]);
        $this->up_json();
        return ['success' => true];
    }

    function up_data()
    {
        $index = $this->params['id'];
        $up_item = $this->product_list[$index];
        $this->product_list[$index] = array_merge($up_item, [
            'title' => $this->params['title'],
            'price' => $this->params['price'],
        ]);
        $this->up_json();
        return ['success' => true];
    }


    function read_id()
    {
        $cc = file_get_contents($this->file_path_id);
        return json_decode($cc);
    }

    function add_id()
    {
        file_put_contents($this->file_path_id, json_encode($this->last_id + 1));
    }

    //从文件中获取数据
    function read()
    {
        $json = file_get_contents($this->file_path);
        return json_decode($json);
    }

    //项文件中存储数据
    function up_json()
    {
        file_put_contents($this->file_path, json_encode($this->product_list));
    }

    //返回给页面的data
    function json($data)
    {
        header('Content-Type:application/json');
        return json_encode($data);
    }


}

$product = new product;
?>