#!/bin/bash
# -*- tab-width:2; indent-tabs-mode:nil -*-
#
# @BEGIN_LICENSE
#
# Metadata Games - A FOSS Electronic Game for Archival Data Systems
# Copyright (C) 2011 Mary Flanagan, Tiltfactor Laboratory
#
# This program is free software: you can redistribute it and/or
# modify it under the terms of the GNU Affero General Public License
# as published by the Free Software Foundation, either version 3 of
# the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public
# License along with this program.  If not, see
# <http://www.gnu.org/licenses/>.
#
# @END_LICENSE
# 

# DESCRIPTION:
#
# This script is used to fix permissions on a number of the files in
# the directory.
#


# If no argument is passed to this script, we assume that the webserver
# is running as user 'www-data'.
webserver_user=$1
if [ -z $webserver_user ]
then
  webserver_user="www-data"
fi

echo Webserver user is: $webserver_user

# Make sure we're in the right (tools) directory.

# This is the path from the current directory up to the root of the
# files in version control.
ROOT_DIRECTORY="../"

# Fix permissions on various files.
echo Fixing perms

FILES="www/protected/data/fbvsettings.php
www/protected/config/main.php"

for f in $FILES
do
  FILE=$ROOT_DIRECTORY$f

  echo Processing $FILE

  chgrp -v $webserver_user $FILE
  chmod -v g+w $FILE
done

echo Done fixing perms.

echo 
echo "NOTE: If you have trouble changing perms, make sure that"
echo  "(1) Your current user is in the $webserver_user group"
echo  "(2) You've activated group access by logging-out and back in"
echo  "(3) The webserver user and group are named _exactly_ the same"
