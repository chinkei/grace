<?php

$hook['priorities'] = 2;

$hook['EVENT_APP_RUN'][] = array(
                                'class'  => 'Test1',
                                'func'   => 'func1',
                                'fName'  => 'test1.php',
                                'fPath'  => 'hooks',
                                'params' => array()
                                );
$hook['EVENT_APP_RUN'][] = array(
                                'class'  => 'Test2',
                                'func'   => 'func2',
                                'fName'  => 'test2.php',
                                'fPath'  => 'hooks',
                                'params' => array()
                                );


$hook['EVENT_APP_END'][] = array(
                                'class'  => 'Test2',
                                'func'   => 'func2',
                                'fName'  => 'test2.php',
                                'fPath'  => 'hooks',
                                'params' => array()
                                );

?>