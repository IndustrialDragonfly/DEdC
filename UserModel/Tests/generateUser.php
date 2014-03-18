<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../User.php';
require_once '../../Storage/DatabaseStorage.php';

$storage = new DatabaseStorage();

$user = new User("Jeff", "InD", "password1");

$storage->saveUser($user->getId(), $user->getUserName(), $user->getOrganization(), $user->getHash(), $user->isAdmin());

$gotUser = $stoage->loadUser($user->getId());
