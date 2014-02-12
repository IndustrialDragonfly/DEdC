# Simple script that allows you to enter the ID of an element in DEdC
# as the first input parameter, and the method type as the second

# Assumes that localhost/DEdC is the location of DEdC

#!/bin/bash

curl localhost/DEdC/${1}_id -H "Accept: application/json" -X ${2}
echo
