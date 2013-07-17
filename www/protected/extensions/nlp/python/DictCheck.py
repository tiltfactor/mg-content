#!/usr/bin/python
# -*- tab-width:2; indent-tabs-mode:nil; python-indent:2; -*-
#
#
# @BEGIN_LICENSE
#
# Metadata Games - A FOSS Electronic Game for Archival Data Systems
# Copyright (C) 2013 Mary Flanagan, Tiltfactor Laboratory
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
#

import sys
# add your path here
# sys.append()
import nltk
from nltk.corpus import wordnet as wn

# SpellChecks a single item
def Checkitem(item):
  item = item[1]
  
  # Checks if it is a word
  if not wn.synsets(item):
    return "False"
  else:
    return "True"
  
print Checkitem(sys.argv)    
