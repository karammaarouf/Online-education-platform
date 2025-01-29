 <!-- صفحة الاتصال بقاعدة البيانات -->
<?php

   $conn = new mysqli("localhost","root","","database_db");
   $conn->set_charset("utf8mb4");
// لتوليد رقم مميز لكل عملية اتصال جديدة
   function unique_id() {
      $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
      $rand = array();
      $length = strlen($str) - 1;
      for ($i = 0; $i < 10; $i++) {
          $n = mt_rand(0, $length);
          $rand[] = $str[$n];
      }
      return implode($rand);
   }    

?>