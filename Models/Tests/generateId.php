<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../ID.php';

$newId = new ID();
var_dump($newId);

$id = new ID("asdf");
var_dump($id);

$tagId = new ID("asdf_id");
var_dump($tagId);

