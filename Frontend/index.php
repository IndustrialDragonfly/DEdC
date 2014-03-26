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
            <script data-main="${web_client_location}js/app" src="${web_client_location}js/require.js"></script>
    </head>
    <body>
            <div id ="dialog-modal" title="DataFlowDiagram Chooser">
                <ol id="selectable"></ol>
            </div>
            
            <div id ="login-dialog" title="Login">
                <p>All form fields are required.</p>
                <form>
                    <fieldset>
                        <label for="organization">Organization</label>
                        <input type="text" name="organization" id="organization" class="text ui-widget-content ui-corner-all">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="text ui-widget-content ui-corner-all">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all">
                    </fieldset>
                </form>
            </div>

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
                            <button id="load">DFD Chooser</button>
                            <button id="newTab">New Tab</button>
                            <button id="save">Save DFD</button>
                    </div>
                    <ul id="menu">
                    </ul>

                    <div id="tabsContainer" class="ui-layout-content ui-widget-content">
                    </div>
            </div>

            <div id="footer" class="ui-layout-south">
                    &copy;2014 Industrial Dragonfly
            </div>

    </body>
    </html>
EOF;
}
?>