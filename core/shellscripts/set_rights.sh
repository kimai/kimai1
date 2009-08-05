#!/bin/sh

# set rights for all smarty template-cache directories
# it may be nessesary to change the mode to 777 ...

chmod 775 ../temporary
chmod 775 ../temporary/logfile.txt
chmod 775 ../compile
chmod 775 ../extensions/**/compile
