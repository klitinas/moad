<?php
      require_once "HTML/QuickForm.php";

      define ("OK", 0);
      define ("WARNING", 1);
      define ("ERROR", 2);

      $status_list = array(OK=>"All OK",
                           WARNING=>"Warning",
                           ERROR=>"Error");

      $styles = array (OK=>"ok",
                       WARNING=>"warn",
                       ERROR=>"error");

      if (array_key_exists("status", $_GET)) {
          $style = $styles[$_GET['status']];
      }
      else {
          $style = $styles[0];
      }

      $attrs = array('onchange' => 
          "javascript:location.href=
              '?status='+this.options[this.selectedIndex].value;");

      $form = new HTML_QuickForm('frmTest', 'get', null, null, "class=$style");
      $form->addElement('select', 'status', 'Select status:', $status_list, $attrs);

      $form->display();
  ?>
