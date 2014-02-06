<?php
function webClient($web_client_location)
{
echo <<<EOF
    <!DOCTYPE html>
    <html>
    <head>
            <title>DEd C</title>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <link rel="stylesheet" type="text/css" href="${web_client_location}css/smoothness/jquery-ui-1.10.3.css"/>
            <link rel="stylesheet" type="text/css" href="${web_client_location}css/jquery-layout-1.3.0-rc30.79.css"/>
            <link rel="stylesheet" type="text/css" href="${web_client_location}css/styles.css"/>
            <script type="text/javascript" src="${web_client_location}js/lib/jquery-1.10.2.js"></script>
            <script type="text/javascript" src="${web_client_location}js/lib/jquery-ui-1.10.3.js"></script>
            <script type="text/javascript" src="${web_client_location}js/lib/jquery-layout-1.3.0-rc30.79.js"></script>
            <script type="text/javascript" src="${web_client_location}js/lib/jquery-layout-resizeAccordionCallback-1.2.js"></script>
            <script type="text/javascript" src="${web_client_location}js/lib/jquery-layout-resizeTabLayout-1.3.js"></script>
            <script type="text/javascript" src="${web_client_location}js/lib/raphael-2.1.2.js"></script>
            <script type="text/javascript" src="${web_client_location}js/canvas.js"></script>
            <script type="text/javascript" src="${web_client_location}js/connector.js"></script>
            <script>
                    $(document).ready(function () {
                        DEdC.setupUi(
                            "#content", 
                            "#sidebar1", 
                            "#users",
                            "#tabsContainer", 
                            "#process", 
                            "#multiprocess", 
                            "#datastore", 
                            "#extinteractor", 
                            "#connect", 
                            "#delete", 
                            "#load",
                            "#newTab"
                        ); 
                    });
            </script>

    </head>
    <body>
            <div id="header" class="ui-layout-north"><a href="#"><img src="${web_client_location}images/logo.png" width="144" height="57"/></a></div>

            <!-- sidebar1Container is required by layout plugin for sidebar1 resizing -->
            <div id="sidebar1Container" class="ui-layout-west" style="display: none;">
            <div id="sidebar1">
                    <h3>Elements</h3>
                    <ul id="elements">
                            <li>
                                    <div id="process">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52">
                                                    <circle cx="26" cy="26" r="25" fill="white" stroke="black" stroke-width="1px"></circle>
                                            </svg>
                                            <p>Process</p>
                                    </div>
                            </li>
                            <li>
                                    <div id="multiprocess">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52">
                                                    <circle cx="26" cy="26" r="25" fill="white" stroke="black" stroke-width="1px"></circle>
                                                    <circle cx="26" cy="26" r="18" fill="white" stroke="black" stroke-width="1px"></circle>
                                            </svg>
                                            <p>Multi-process</p>
                                    </div>
                            </li>
                            <li>
                                    <div id="datastore">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52">
                                                    <path d="M0 0 L52 0 Z" stroke="black" stroke-width="1px"></path>
                                                    <path d="M0 52 L52 52 Z" stroke="black" stroke-width="1px"></path>
                                                    <rect x="0" y="1" width="52" height="50" fill="white"></rect>
                                            </svg>
                                            <p>Datastore</p>
                                    </div>
                            </li>
                            <li>
                                    <div id="extinteractor">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52">
                                                    <rect x="1" y="1" width="50" height="50" fill="white" stroke="black" stroke-width="1px"></rect>
                                            </svg>
                                            <p>External Interactor</p>
                                    </div>
                            </li>
                    </ul>
                    <h3>Layers</h3>
                    <ul id="layers">
                    </ul>
                    <h3>Users</h3>
                    <div id="users">
                    </div>
            </div>
            </div>

            <div id="content" class="ui-layout-center">
                    <div id="toolbar" class="ui-widget-header ui-conrner-all">
                            <button id="connect">Create Dataflow</button>
                            <button id="delete">Delete Element</button>
                            <button id="load">Load DFD</button>
                            <button id="newTab">New Tab</button>
                    </div>
                    <ul id="menu">
                    </ul>

                    <div id="tabsContainer" class="ui-layout-content ui-widget-content">
                    </div>
            </div>

            <div id="footer" class="ui-layout-south">
                    &copy;2013 Industrial Dragonfly
            </div>

    </body>
    </html>
EOF;
}
?>