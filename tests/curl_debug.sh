# Simple script that allows you to enter the ID of an element in DEdC
# as a command line parameter, and will then use curl to get a result
# back from DEdC

# Assumes that localhost/DEdC is the location of DEdC

#!/bin/bash

curl localhost/DEdC/${1}_id -H "Accept: application/json"
echo
