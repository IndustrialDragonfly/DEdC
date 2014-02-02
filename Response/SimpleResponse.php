<?php
require_once "Response.php";
/**
 * Simple class to allow for testing of the abstract Request object.
 *
 * @author eugene
 */

class SimpleResponse extends Response
{
    public function __construct($element)
    {
        parent::__construct($element);
        // Set the very hard coded DFD up
        $body =<<<EOT
                {
                    "name": "DFD",
                    "elements": [
                            {
                                    "id": "1234",
                                    "type": "process",
                                    "label": "Example Process 1",
                                    "x": "50",
                                    "y": "50"
                            },
                            {
                                    "id": "1235",
                                    "type": "process",
                                    "label": "Example Process 2",
                                    "x": "200",
                                    "y": "50"
                            }
                    ],
                    "dataflows": [
                        {
                            "id": "1236",
                            "origin_id": "1234",
                            "dest_id": "1235"
                        }
                    ]
                }
EOT;
        $this->setBody($body);
    }
}
