<?php
    echo $this->Session->flash('auth');
    echo $this->Form->create('User');
    echo $this->Form->input('username', array('div' => 'input text clearfix'));
    echo $this->Form->input('password', array('div' => 'input text clearfix'));
    echo $this->Form->end('Login');
?>