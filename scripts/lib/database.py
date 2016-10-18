#!/usr/bin/python3

import MySQLdb
import configparser
import sys

from .configsectionmap import ConfigSectionMap

def Database():
    config = configparser.ConfigParser()
    config.read("../config/config.ini")
    config.sections()
    mysql_user = ConfigSectionMap(config, "Database")['mysql_user']
    mysql_pass = ConfigSectionMap(config, "Database")['mysql_password']
    mysql_host = ConfigSectionMap(config, "Database")['mysql_host']
    mysql_db = ConfigSectionMap(config, "Database")['mysql_database']

    db = MySQLdb.connect(host=mysql_host, user=mysql_user, passwd=mysql_pass, db=mysql_db)
    cur = db.cursor()
    database = {'database': db, 'cursor': cur}
    return database

