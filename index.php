<?php

//Định nghĩa node
class node
{
    public $data;
    public $next;

    public function __construct($data)
    {
        $this->data = $data;
        $this->next = null;
    }
}

//định nghĩa single linked list
class LinkedList
{

    public $head;

    public function __construct()
    {
        $this->head = null;
    }

    public function append($data)
    {
        $newNode = new Node($data);

        if ($this->head === null) {
            $this->head = $newNode;
        } else {
            $current = $this->head;
            while ($current->next !== null) {
                $current = $current->next;
            }
            $current->next = $newNode;
        }
    }

    // Tìm phần tử thứ N từ cuối lên
    public function find($n)
    {
        $first = $this->head;
        $second = $this->head;

        //Di chuyển first lên trước n bước
        for ($i = 0; $i < $n; $i++) {
            if ($first === null) {
                //nếu n lớn hơn độ dài list
                return null;
            }
            $first = $first->next;
        }

        while ($first !== null) {
            $first = $first->next;

            // Kiểm tra nếu $second đã trở thành null
            if ($second === null) {
                // Không tìm thấy phần tử thứ n từ cuối
                return null;
            }

            $second = $second->next;
        }
    }
}

// Ví dụ sử dụng
$list = new LinkedList();
$list->append(10);
$list->append(20);
$list->append(30);
$list->append(40);
$list->append(50);

$n = 2; // Tìm phần tử thứ 2 từ cuối lên
$result = $list->find($n);
echo "Phần tử thứ $n từ cuối lên là: " . ($result !== null ? $result : "Không tồn tại") . PHP_EOL;
