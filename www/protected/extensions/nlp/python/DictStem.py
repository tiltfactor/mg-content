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
import os

# Put the path of your enchant library here
# (NOTE: PyEnchant is no longer necessary, AFAIK)
import nltk
from nltk import WordNetLemmatizer

# SpellChecks items in an array
def Check(mArray):
  
  # what am I checking?
  item = mArray[1]
  lmtzr = WordNetLemmatizer()
  item = lmtzr.lemmatize(item)
  
  # converts to a string
  return ''.join(item)

print Check(sys.argv)    
