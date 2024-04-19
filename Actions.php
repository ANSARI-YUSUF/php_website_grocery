<?php 
session_start();
require_once('DBConnection.php');

Class Actions extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function login(){
        extract($_POST);
        $sql = "SELECT * FROM admin_list where username = '{$username}' and `password` = '".md5($password)."' ";
        @$qry = $this->query($sql)->fetchArray();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
    function customer_login(){
        extract($_POST);
        $sql = "SELECT * FROM customer_list where username = '{$username}' and `password` = '".md5($password)."' ";
        @$qry = $this->query($sql)->fetchArray();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            if($qry['status'] != 1){
            $resp['status'] = "failed";
            $resp['msg'] = "Your Account has been blocked by the management. Contact the management to settle.";
            }else{
                $resp['status'] = "success";
                $resp['msg'] = "Login successfully.";
                foreach($qry as $k => $v){
                    if(!is_numeric($k))
                    $_SESSION[$k] = $v;
                }
            }
        }
        return json_encode($resp);
    }
    function logout(){
        session_destroy();
        header("location:./admin");
    }
    function customer_logout(){
        session_destroy();
        header("location:./");
    }
    function update_credentials(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        }else{
            $sql = "UPDATE `admin_list` set {$data} where admin_id = '{$_SESSION['admin_id']}'";
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function update_credentials_customer(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        }else{
            $sql = "UPDATE `customer_list` set {$data} where customer_id = '{$_SESSION['customer_id']}'";
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function save_category(){
        extract($_POST);
        if(empty($id))
            $sql = "INSERT INTO `category_list` (`name`,`status`)VALUES('{$name}','{$status}')";
        else{
            $data = "";
             foreach($_POST as $k => $v){
                 if(!in_array($k,array('id'))){
                     if(!empty($data)) $data .= ", ";
                     $data .= " `{$k}` = '{$v}' ";
                 }
             }
            $sql = "UPDATE `category_list` set {$data} where `category_id` = '{$id}' ";
        }
        @$check= $this->query("SELECT COUNT(category_id) as count from `category_list` where `name` = '{$name}' ".($id > 0 ? " and category_id != '{$id}'" : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Category Name already exists.';
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Category successfully saved.";
                else
                    $resp['msg'] = "Category successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Category Failed.";
                else
                    $resp['msg'] = "Updating Category Failed.";
                $resp['error']=$this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_category(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `category_list` where category_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Category successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_stat_cat(){
        extract($_POST);
        @$update = $this->query("UPDATE `category_list` set `status` = '{$status}' where category_id = '{$id}'");
        if($update){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Category Status successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_fee(){
        extract($_POST);
        if(empty($id))
            $sql = "INSERT INTO `fees_list` (`location`,`amount`,`status`)VALUES('{$location}','{$amount}','{$status}')";
        else{
            $data = "";
             foreach($_POST as $k => $v){
                 if(!in_array($k,array('id'))){
                     if(!empty($data)) $data .= ", ";
                     $data .= " `{$k}` = '{$v}' ";
                 }
             }
            $sql = "UPDATE `fees_list` set {$data} where `fee_id` = '{$id}' ";
        }
        @$check= $this->query("SELECT COUNT(fee_id) as count from `fees_list` where `location` = '{$location}' ".($id > 0 ? " and fee_id != '{$id}'" : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Location already exists.';
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Delivery Fee successfully saved.";
                else
                    $resp['msg'] = "Delivery Fee successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Delivery Fee Failed.";
                else
                    $resp['msg'] = "Updating Delivery Fee Failed.";
                $resp['error']=$this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_fee(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `fees_list` where fee_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Delivery Fee successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_stat_fee(){
        extract($_POST);
        @$update = $this->query("UPDATE `fees_list` set `status` = '{$status}' where fee_id = '{$id}'");
        if($update){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Delivery Fee Status successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_stock(){
        extract($_POST);
        if(empty($id))
            $sql = "INSERT INTO `inventory_list` (`product_id`,`quantity`)VALUES('{$product_id}','{$quantity}')";
        else{
            $data = "";
             foreach($_POST as $k => $v){
                 if(!in_array($k,array('id'))){
                     if(!empty($data)) $data .= ", ";
                     $data .= " `{$k}` = '{$v}' ";
                 }
             }
            $sql = "UPDATE `inventory_list` set {$data} where `inventory_id` = '{$id}' ";
        }
      
        @$save = $this->query($sql);
        if($save){
            $resp['status']="success";
            if(empty($id))
                $resp['msg'] = "Delivery stock successfully saved.";
            else
                $resp['msg'] = "Delivery stock successfully updated.";
        }else{
            $resp['status']="failed";
            if(empty($id))
                $resp['msg'] = "Saving New Delivery stock Failed.";
            else
                $resp['msg'] = "Updating Delivery stock Failed.";
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function delete_stock(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `inventory_list` where stock_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Delivery stock successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_user(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
        if(!in_array($k,array('id','type'))){
            if(!empty($id)){
                if(!empty($data)) $data .= ",";
                $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if(empty($id)){
            $cols[] = 'password';
            $values[] = "'".md5($username)."'";
        }
        if(isset($cols) && isset($values)){
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
        

       
        @$check= $this->query("SELECT count(admin_id) as `count` FROM admin_list where `username` = '{$username}' ".($id > 0 ? " and admin_id != '{$id}' " : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        }else{
            if(empty($id)){
                $sql = "INSERT INTO `admin_list` {$data}";
            }else{
                $sql = "UPDATE `admin_list` set {$data} where admin_id = '{$id}'";
            }
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'New Admin User successfully saved.';
                else
                $resp['msg'] = 'Admin User Details successfully updated.';
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving Admin User Details Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_user(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `admin_list` where rowid = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Admin User successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_customer(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
        if(!in_array($k,array('id'))){
            if($k == 'password'){
                if(empty($v))
                    continue;
                else
                    $v= md5($v);
            }
            if(!empty($id)){
                if(!empty($data)) $data .= ",";
                $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if(isset($cols) && isset($values)){
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
        @$check= $this->query("SELECT count(customer_id) as `count` FROM customer_list where `username` = '{$username}' ".($id > 0 ? " and customer_id != '{$id}' " : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        }else{
            if(empty($id)){
                $sql = "INSERT INTO `customer_list` {$data}";
            }else{
                $sql = "UPDATE `customer_list` set {$data} where customer_id = '{$id}'";
            }
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'Account successfully Created.';
                else
                $resp['msg'] = 'Account Details successfully updated.';
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving Details Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_customer(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `customer_list` where rowid = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Customer successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_product(){
        extract($_POST);
        @$check= $this->query("SELECT count(product_id) as `count` FROM `product_list` where `name` = '{$name}' ".($id > 0 ? " and product_id != '{$id}'" : ''))->fetchArray()['count'];
        if($check> 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Product Name already exists.";
        }else{
            $data = "";
            foreach($_POST as $k =>$v){
                if(!in_array($k,array('id','thumbnail','img'))){
                    if(empty($id)){
                        $columns[] = "`{$k}`"; 
                        $values[] = "'{$v}'"; 
                    }else{
                        if(!empty($data)) $data .= ", ";
                        $data .= " `{$k}` = '{$v}'";
                    }
                }
            }
            if(isset($columns) && isset($values)){
                $data = "(".(implode(",",$columns)).") VALUES (".(implode(",",$values)).")";
            }
            if(empty($id)){
                $sql = "INSERT INTO `product_list` {$data}";
            }else{
                $sql = "UPDATE `product_list` set {$data} where product_id = '{$id}'";
            } 
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'Product Successfully added.';
                else
                $resp['msg'] = 'Product Successfully updated.';
                if(empty($id))
                $last_id = $this->query("SELECT max(product_id) as last_id from `product_list`")->fetchArray()['last_id'];
                $pid = !empty($id) ? $id : $last_id;
                if(isset($_FILES)){
                    foreach($_FILES as $k=>$v){
                        $$k=$v;
                    }
                }
                if(isset($thumbnail) && !empty($thumbnail['tmp_name'])){
                    $thumb_file = $thumbnail['tmp_name'];
                    $thumb_fname = $pid.'.png';
                    $file_type = mime_content_type($thumb_file);
                    list($width, $height) = getimagesize($thumb_file);
                    $t_image = imagecreatetruecolor('350', '350');
                    if(in_array($file_type,array('image/png','image/jpeg','image/jpg'))){
                        $gdImg = ($file_type =='image/png') ? imagecreatefrompng($thumb_file) : imagecreatefromjpeg($thumb_file);
                        imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, '350', '350', $width, $height);
                        if($t_image){
                            if(is_file(__DIR__.'/uploads/thumbnails/'.$thumb_fname))
                                unlink(__DIR__.'/uploads/thumbnails/'.$thumb_fname);
                                imagepng($t_image,__DIR__.'/uploads/thumbnails/'.$thumb_fname);
                                imagedestroy($t_image);
                        }else{
                            $resp['msg'] = 'Product Successfully saved but Thumbnail image failed to upload.';
                        }
                    }else{
                            $resp['msg'] = 'Product Successfully saved but Thumbnail image failed to upload due to invalid file type.';
                    }
                }
                if(isset($img) && count($img['tmp_name']) > 0){
                    if(!is_dir(__DIR__.'/uploads/images/'.$pid))
                    mkdir(__DIR__.'/uploads/images/'.$pid);
                    for($i = 0;$i < count($img['tmp_name']); $i++){
                        if(!empty($img['tmp_name'][$i])){
                            $img_file = $img['tmp_name'][$i];
                            $ex = explode('.',$img['name'][$i]);
                            $_fname = $ex[0];
                            $_i = 1;
                            while(true){
                                $_i++;
                                if(is_file(__DIR__.'/uploads/images/'.$pid.'/'.$_fname.'.png')){
                                    $_fname =$ex[0].'_'.$_i;
                                }else{
                                    break;
                                }
                            }
                            $img_fname = $_fname.'.png';
                            $file_type = mime_content_type($img_file);
                            list($width, $height) = getimagesize($img_file);
                            $t_image = imagecreatetruecolor('350', '350');
                            if(in_array($file_type,array('image/png','image/jpeg','image/jpg'))){
                                $gdImg = ($file_type =='image/png') ? imagecreatefrompng($img_file) : imagecreatefromjpeg($img_file);
                                imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, '350', '350', $width, $height);
                                if($t_image){
                                    imagepng($t_image,__DIR__.'/uploads/images/'.$pid.'/'.$img_fname);
                                    imagedestroy($t_image);
                                }else{
                                    $resp['msg'] = 'Product Successfully saved but Product image failed to upload.';
                                }
                            }else{
                                $resp['msg'] = 'Product Successfully saved but Product image failed to upload due to invalid file type.';
                            }

                        }
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'An error occured. Error: '.$this->lastErrorMsg();
                $resp['sql'] = $sql;
            }
        }
        return json_encode($resp);
    }
    function delete_product(){
        extract($_POST);
        @$delete = $this->query("DELETE FROM `product_list` where product_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Employee successfully deleted.';
            if(is_file(__DIR__.'/uploads/thumbnails/'.$id.'.png'))
                unlink(__DIR__.'/uploads/thumbnails/'.$id.'.png');
            if(is_dir(__DIR__.'/uploads/images/'.$id)){
                $scan = scandir(__DIR__.'/uploads/images/'.$id);
                foreach($scan as $img){
                    if(!in_array($img,array('.','..'))){
                        unlink(__DIR__.'/uploads/images/'.$id.'/'.$img);
                    }
                }
                rmdir(__DIR__.'/uploads/images/'.$id);
            }
        }else{
            $resp['status']='failed';
            $resp['msg'] = 'An error occure. Error: '.$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_attendance(){
        extract($_POST);
        @$employee_id = $this->query("SELECT employee_id FROM `employee_list` where `employee_code` = '{$employee_code}'")->fetchArray()['employee_id'];
        if($employee_id > 0){
            $check = $this->query("SELECT count(attendance_id) as `count` FROM `attendance_list` where `employee_id` = '{$employee_id}' and `att_type_id` = '{$att_type_id}' and date(`date_created`) = '".date("Y-m-d",strtotime($date_created))."' ")->fetchArray()['count'];
            if($check > 0){
                $resp['status'] = 'failed';
                $resp['msg'] = "You already have ".$att_type. " record today.";
            }else{
            $sql = "INSERT INTO `attendance_list` (`employee_id`,`att_type_id`,`date_created`) VALUES ('{$employee_id}','{$att_type_id}','{$date_created}')";
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
            } else{
                $resp['status'] = 'failed';
                $resp['msg'] = "An error occured. Error: ". $this->lastErrorMsg();
            }
        }

        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Uknown Employee Code.";
        }
        return json_encode($resp);
    }
    function delete_img(){
        extract($_POST);
        if(is_file(__DIR__.$path)){
            unlink(__DIR__.$path);
        }
        $resp['status'] = 'success';
        return json_encode($resp);
    }
    function add_to_cart(){
        extract($_POST);
        $customer_id = $_SESSION['customer_id'];
        $check = $this->query("SELECT count(product_id) as `count` FROM `cart_list` where `product_id` = '{$product_id}' and `customer_id` = '{$customer_id}'")->fetchArray()['count'];
        if($check > 0){
            $sql = "UPDATE `cart_list` set `quantity` = `quantity`+1 where `product_id` = '{$product_id}' and `customer_id` = '{$customer_id}'";
        }else{
            $sql = "INSERT INTO `cart_list` (`product_id`,`customer_id`,`quantity`)VALUES('{$product_id}','{$customer_id}','{$quantity}')";
        }
        $save = $this->query($sql);
        if($save){
            $resp['status'] ='success';
            $count = $this->query("SELECT SUM(quantity) as total FROM `cart_list` where `customer_id` = '{$customer_id}'")->fetchArray()['total'];
            $resp['cart_count'] = $count;
        }else{
            $resp['status'] ='failed';
            $resp['sql'] =$sql;
        }
        return json_encode($resp);
    }
    function update_cart(){
        extract($_POST);
        $in = $this->query("SELECT SUM(quantity) as `in` from inventory_list where product_id = '{$product_id}' and `type` = 1")->fetchArray()['in'];
        $out = $this->query("SELECT SUM(quantity) as `out` from inventory_list where product_id = '{$product_id}' and `type` = 2")->fetchArray()['out'];
        $available = $in - $out;
        $sql = "UPDATE `cart_list` set `quantity` = '{$quantity}' where `product_id` = '{$product_id}' and `customer_id` = '{$customer_id}'";
        if($quantity > $available){
            $resp['status'] ='failed';
            $resp['msg'] ="Product has only {$available} item/s left.";
            $resp['available'] =$available;
        }else{ 
            $save = $this->query($sql);
            if($save){
                $resp['status'] ='success';
                $count = $this->query("SELECT SUM(quantity) as total FROM `cart_list` where `customer_id` = '{$customer_id}'")->fetchArray()['total'];
                $resp['cart_count'] = $count;
            }else{
                $resp['status'] ='failed';
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_from_cart(){
        extract($_POST);
        $customer_id = $_SESSION['customer_id'];
        $sql = "DELETE FROM `cart_list` where `product_id` = '{$id}' and `customer_id` = '{$customer_id}'";
        $delete = $this->query($sql);
        if($delete){
            $resp['status'] ='success';
            $count = $this->query("SELECT SUM(quantity) as total FROM `cart_list` where `customer_id` = '{$customer_id}'")->fetchArray()['total'];
            $resp['cart_count'] = $count;
        }else{
            $resp['status'] ='failed';
            $resp['sql'] =$sql;
        }
        return json_encode($resp);
    }
    function place_order(){
        extract($_POST);
        $customer_id = $_SESSION['customer_id'];
        $data = "";
        $data_items = "";
        $data_items2 = "";
        $code = "";
        while(true){
            $code = mt_rand(1,9999999999);
            $code = sprintf("%.9d",$code);
            $check = $this->query("SELECT count(order_id) as `count` from `order_list` where transaction_code = '{$code}' ")->fetchArray()['count'];
            if($check <= 0)
            break;
        }
        $sql = "INSERT INTO `order_list` (`customer_id`,`transaction_code`,`delivery_address`,`total_amount`,`fee_id`)VALUES('{$customer_id}','{$code}','{$delivery_address}','{$total_amount}','{$fee_id}')";
        $save = $this->query($sql);
        if($save){
            $resp['status'] = 'success';
            $last_id = $this->query("SELECT max(order_id) as last_id from `order_list`")->fetchArray()['last_id'];

            $cart = $this->query("SELECT * FROM `cart_list` where `customer_id` = '{$customer_id}'");
            while($row = $cart->fetchArray()){
                $data_items1 = "('{$row['product_id']}','{$row['quantity']}','2')";
                $save_inv = $this->query("INSERT INTO `inventory_list` (`product_id` , `quantity`,`type`) VALUES {$data_items1}");
                if(!$save_inv){
                     $resp['status'] = 'failed';
                     $resp['msg'] = 'failed.Error: '.$this->lastErrorMsg();
                }
                $inv_id = $this->query("SELECT last_insert_rowid() ")->fetchArray()[0];
                $data_items = "('{$last_id}','{$row['product_id']}','{$row['quantity']}','{$inv_id}')";
            $this->query("INSERT INTO `order_items` (`order_id`,`product_id`,`quantity`,`inventory_id`) VALUES {$data_items}");
        }
            $this->query("DELETE FROM `cart_list` where `customer_id` = '{$customer_id}'");
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Order successfully placed.';
        }else{
            $resp['status'] = 'failed';
        }
        
        return json_encode($resp);
    }
    function update_order_status(){
        extract($_POST);
        $sql = "UPDATE `order_list` set `status` = '{$status}' where `order_id` = '{$order_id}' ";
        @$update = $this->query($sql);
        if($update){
            $resp['status']='success';
            $resp['msg'] = "Order Status successfully updated";
            $resp['return_status'] = $status;
        }else{
            $resp['status']='failed';
            $resp['msg'] = "Failed to Update status. Error: ".$this->lastErrorMsg();
            $resp['sql'] = $sql;
        }
        return json_encode($resp);
    }
    function update_customer_status(){
        extract($_POST);
        $sql = "UPDATE `customer_list` set `status` = '{$status}' where `customer_id` = '{$id}' ";
        @$update = $this->query($sql);
        if($update){
            $resp['status']='success';
            $_SESSION['flashdata']['msg'] = "Customer Status successfully updated";
            $_SESSION['flashdata']['type'] = "success";
        }else{
            $resp['status']='failed';
            $resp['msg'] = "Failed to Update status. Error: ".$this->lastErrorMsg();
            $resp['sql'] = $sql;
        }
        return json_encode($resp);
    }
    function delete_transaction(){
        extract($_POST);
        $inv_ids = array();
        $qry = $this->query("SELECT * FROM order_items where order_id = '{$id}'");
        while($row = $qry->fetchArray()){
            $inv_ids[] = $row['inventory_id'];
        }
        @$delete = $this->query("DELETE FROM `order_list` where order_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Order successfully deleted.';
            if(count($inv_ids) > 0){
                $ids = implode(",",$inv_ids);
                $this->query("DELETE FROM `inventory_list` where inventory_id in ({$ids})");
            }
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$action = new Actions();
switch($a){
    case 'login':
        echo $action->login();
    break;
    case 'customer_login':
        echo $action->customer_login();
    break;
    case 'logout':
        echo $action->logout();
    break;
    case 'customer_logout':
        echo $action->customer_logout();
    break;
    case 'update_credentials':
        echo $action->update_credentials();
    break;
    case 'update_credentials_customer':
        echo $action->update_credentials_customer();
    break;
    case 'save_category':
        echo $action->save_category();
    break;
    case 'delete_category':
        echo $action->delete_category();
    break;
    case 'update_stat_cat':
        echo $action->update_stat_cat();
    break;
    case 'save_fee':
        echo $action->save_fee();
    break;
    case 'delete_fee':
        echo $action->delete_fee();
    break;
    case 'save_stock':
        echo $action->save_stock();
    break;
    case 'delete_stock':
        echo $action->delete_stock();
    break;
    case 'update_stat_fee':
        echo $action->update_stat_fee();
    break;
    case 'save_user':
        echo $action->save_user();
    break;
    case 'delete_user':
        echo $action->delete_user();
    break;
    case 'save_customer':
        echo $action->save_customer();
    break;
    case 'delete_customer':
        echo $action->delete_customer();
    break;
    case 'save_product':
        echo $action->save_product();
    break;
    case 'delete_product':
        echo $action->delete_product();
    break;
    case 'save_attendance':
        echo $action->save_attendance();
    break;
    case 'delete_img':
        echo $action->delete_img();
    break;
    case 'add_to_cart':
        echo $action->add_to_cart();
    break;
    case 'update_cart':
        echo $action->update_cart();
    break;
    case 'delete_from_cart':
        echo $action->delete_from_cart();
    break;
    case 'place_order':
        echo $action->place_order();
    break;
    case 'update_order_status':
        echo $action->update_order_status();
    break;
    case 'update_customer_status':
        echo $action->update_customer_status();
    break;
    case 'delete_transaction':
        echo $action->delete_transaction();
    break;
    default:
    // default action here
    break;
}