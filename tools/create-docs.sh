#!/bin/bash
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
# This script is used to generate an html version of the documentation
# out of the Markdown encoded .text documents. 
#

for f in ../documentation/*.text
do
  echo Processing $f
  
  echo "<html>
  <head>
    <title>MG Guide</title>
    <style>
      body {
        width: 600px;
        margin:20px;
        font-size: 14px;
        font-family: Arial, Helvetica, sans-serif;
      }
      
      h1, h2, h3, h4, h5, h6 {
        margin: 30px 0 5px 0;
        line-height: 1em;
      }
      
      h2 {
        line-height: 1.2em;
        border-bottom: 2px dashed #999;
      }
      
      pre {
        font-size: 11px;
      }
    </style>
  </head>
  <body>" > "${f%.*}.html"
  markdown/Markdown.pl $f >> "${f%.*}.html"
  echo "</body></html>" >> "${f%.*}.html"
done
