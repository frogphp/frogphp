<?php
    class IndexAction extends Action {
        function index(){
                $this->assign('title','hello world _ Powered by FrogPHP');
              	$this->assign('content','hello world _ Powered by FrogPHP');
				$this->display();
            }
    }