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
                                    "label": "Example Process",
                                    "x": "50",
                                    "y": "50"
                            },
                            {
                                    "id": "1235",
                                    "type": "multiprocess",
                                    "label": "Example Multiprocess",
                                    "x": "200",
                                    "y": "50"
                            },
                            {
                                    "id": "1237",
                                    "type": "datastore",
                                    "label": "Example Datastore",
                                    "x": "50",
                                    "y": "200"
                            },
                            {
                                    "id": "1238",
                                    "type": "extinteractor",
                                    "label": "Example Ext. Interactor",
                                    "x": "200",
                                    "y": "200"
                            }
                    ],
                    "dataflows": [
                        {
                            "id": "1236",
                            "origin_id": "1234",
                            "dest_id": "1235"
                        },
                        {
                            "id": "1239",
                            "origin_id": "1238",
                            "dest_id": "1234"
                        },
                        {
                            "id": "1240",
                            "origin_id": "1237",
                            "dest_id": "1234"
                        }
                    ]
                }
EOT;
        $this->setBody($body);
    }
}
