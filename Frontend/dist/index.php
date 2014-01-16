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
            <script type="text/javascript" src="${web_client_location}js/lib/raphael-2.1.2.js"></script>
            <script type="text/javascript" src="${web_client_location}js/canvas.js"></script>
            <script>
                    $(document).ready(function () {
                            // Function to resize the canvas to fit in the tab
                            var resizeCanvas = function()
                            {
                                    var width = $('#tabsContainer').width();
                                    var height = $('#tabsContainer').height();
                                    canvas.setSize(width,height);
                            };

                            $("#content").tabs();

                            // Needs to be created after tabs, but before accordions
                            myLayout = $('body').layout({
                                    west__size: 200,
                                    west__onresize: $.layout.callbacks.resizePaneAccordions,
                                    center__onresize: resizeCanvas
                            });

                            $("#sidebar1").accordion({
                                    heightStyle:"fill",
                                    collapsible: true
                            });

                            $("#users").accordion({
                                    heightStyle:"content",
                                    collapsible: true
                            });

                            // Create the canvas, and add some sample elements
                            var canvas = new Canvas("tab1", 640, 480);
                            var p = canvas.addProcess(100,100);
                            p.setText("Test process");
                            canvas.addMultiProcess(200,100);
                            var d = canvas.addDatastore(100,200);
                            canvas.addExtInteractor(200,200);

                            canvas.addDataflow(p,d);

                            canvas.setBackground('#A8A8A8');

                            // Setup drag and drop
                            var draggableHelper = function(event,ui)
                            {
                                    return $(this).clone().appendTo('body').css('zIndex',5).show();
                            };

                            var ELETYPE = {
                                    PROCESS : {value: 0, name: "Process", code: "P"},
                                    MULTIPROCESS: {value:1, name: "Multiprocess", code: "MP"},
                                    DATASTORE: {value:1, name: "Datastore", code: "D"},
                                    EXTINTERACTOR: {value:1, name: "External-Interactor", code: "EI"}

                            };

                            $("#process").draggable({
                                    helper: draggableHelper
                            }).data("type", ELETYPE.PROCESS);

                            $("#multiprocess").draggable({
                                    helper: draggableHelper
                            }).data("type", ELETYPE.MULTIPROCESS);

                            $("#datastore").draggable({
                                    helper: draggableHelper
                            }).data("type", ELETYPE.DATASTORE);

                            $("#extinteractor").draggable({
                                    helper: draggableHelper
                            }).data("type", ELETYPE.EXTINTERACTOR);

                            // Setup drag/drop
                            $("#tab1").droppable({
                                    drop: function(event,ui) {
                                            // Add to canvas
                                            var posx = event.pageX - $('#tab1').offset().left;
                                            var posy = event.pageY - $('#tab1').offset().top;

                                            if ($(ui.draggable).data("type") == ELETYPE.PROCESS)
                                                    canvas.addProcess(posx,posy);
                                            else if ($(ui.draggable).data("type") == ELETYPE.MULTIPROCESS)
                                                    canvas.addMultiProcess(posx,posy);
                                            else if ($(ui.draggable).data("type") == ELETYPE.DATASTORE)
                                                    canvas.addDatastore(posx,posy);
                                            else if ($(ui.draggable).data("type") == ELETYPE.EXTINTERACTOR)
                                                    canvas.addExtInteractor(posx,posy);
                                            else
                                                    alert("Draggable Element was malformed.");
                                    }
                            });

                            $("#connect").button().click(function(){
                                    canvas.addDataflowFromSelection();
                            });

                            $("#delete").button().click(function(){
                                    canvas.removeElementFromSelection();
                            });

                            // Initial resize to fit
                            resizeCanvas();
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
                            <li>Root</li>
                            <ul>
                                    <li>Firing Control</li>
                            </ul>
                    </ul>
                    <h3>Users</h3>
                    <div id="users">
                            <h3>Malcolm</h3>
                            <div>
                                    Edits Made
                                    <ul>
                                            <li><a href="#">Created DFD</a></li>
                                    </ul>
                            </div>
                            <h3>Josh</h3>
                            <div>
                                    Edits Made
                                    <ul>
                                            <li><a href="#">Reposition Firing Control</a></li>
                                            <li><a href="#">Added Firing Control</a></li>
                                    </ul>
                            </div>
                    </div>
            </div>
            </div>

            <div id="content" class="ui-layout-center">
                    <div id="toolbar" class="ui-widget-header ui-conrner-all">
                            <button id="connect">Create Dataflow</button>
                            <button id="delete">Delete Element</button>
                    </div>
                    <ul id="menu">
                            <li><a href="#tab1">DFD</a></li>
                            <li><a href="#tab2">Firing Control</a></li>
                    </ul>

                    <div id="tabsContainer" class="ui-layout-content ui-widget-content">
                            <div id="tab1"></div>

                            <div id="tab2">
                                    <p>Firing Control View<p>
                            </div>
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