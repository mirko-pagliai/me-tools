<?php

//Route for thumbs
Router::connect('/thumb/*', array('plugin' => 'me_tools', 'admin' => false, 'controller' => 'thumbs', 'action' => 'thumb'));