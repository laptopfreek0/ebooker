#!/usr/bin/python3

import sys
import os
import re

from lib.database import Database

database = Database()
cur = database['cursor']
database = database['database']
cur.execute("SELECT location FROM book_folders")

database2 = Database()
cur2 = database2['cursor']
database2 = database2['database']

for row in cur.fetchall() :
    for root, dirs, files in os.walk(row[0]):
        for file in files:
            if file.endswith(".epub") | file.endswith("mobi"):
                filepath = os.path.join(root, file)
                cur2.execute('SELECT id FROM books WHERE location = %s', (filepath))
                if (len(cur2.fetchall()) == 0) :
                    # New file found
                    filename = os.path.splitext(file)[0]
                    extension = os.path.splitext(file)[1]
                    extension = re.search(r'\.(.*)$', extension, re.I|re.M).group(1)
                    output = os.popen('ebook-meta "' + filepath + '"')
                    output = output.read()
                    print output
                    if "Title" in output:
                        bookname = re.search(r'.*Title.*\: (.*)$', output, re.I|re.M).group(1)
                        if "Author" in output:
                            author = re.search(r'.*Author.*\: (.*)$', output, re.I|re.M).group(1)
                        else:
                            author = 'Unknown'
                        if "Publisher" in output:
                            pub = re.search(r'.*Publisher.*\: (.*)$', output, re.I|re.M)
                            if pub is not None:
                                publisher = pub.group(1)
                            else:
                                publisher = ''
                        else:
                            publisher = ''
                        if "Published" in output:
                            publ = re.search(r'.*Published.*\: (.*)$', output, re.I|re.M)
                            if publ is not None:
                                publised_date = publ.group(1)
                            else:
                                published_date = ''
                        else:
                            published_date = ''
                        if "Language" in output:
                            lan = re.search(r'.*Language.*\: (.*)$', output, re.I|re.M)
                            if lan is not None:
                                language = lan.group(1)
                            else:
                                language = 'en'
                        else:
                            language = 'en'
                        if "Identifiers" in output:
                            iden = re.search(r'.*Identifiers.*\: (.*)$', output, re.I|re.M)
                            if iden is not None:
                                identifiers = iden.group(1)
                            else:
                                identifiers = ''
                        else:
                            identifiers = ''
                        if "Comments" in output:
                            comm = re.search(r'.*Comments.*\: (.*)', output, re.I)
                            if comm is not None:
                                comments = comm.group(1)
                            else:
                                comments = ''
                        else:
                            comments = ''
                        if "Tags" in output:
                            tag = re.search(r'.*Tags.*\: (.*)$', output, re.I|re.M)
                            if tag is not None:
                                tags = tag.group(1)
                            else:
                                tags = ''
                        else:
                            tags = ''
                        if "Rating" in output:
                            rat = re.search(r'.*Rating.*\: (.*)$', output, re.I|re.M)
                            if rat is not None:
                                rating = rat.group(1)
                            else:
                                rating = ''
                        else:
                            rating = ''
                        cur2.execute('SELECT id, image_location FROM book_metas WHERE name = %s AND author = %s', (bookname, author))
                        results = cur2.fetchall() 
                        if (len(results) == 0):
                            # No Metadata Found
                            print "No Metadata found in database"
                            if not(os.path.isfile(root + filename + ".jpg")):
                                # No cover found saving it
                                print "No Cover found saving it"
                                output = os.popen('ebook-meta --get-cover="' + root + filename + '.jpg" "' + filepath + '"') 
                            try:
                                cur2.execute('INSERT into book_metas (name, author, published_date, publisher, description, image_location, identifiers, tags, rating) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)',
                                            (bookname, author, published_date, publisher, comments, root + filename + ".jpg", identifiers, tags, rating))
                                database2.commit()
                            except:
                                database2.rollback() 
                        else:
                            # MetaData Found
                            # For now do nothing
                            results[0][1]
                        cur2.execute('SELECT id FROM book_metas WHERE name = %s AND author = %s', (bookname, author))
                        meta_id = cur2.fetchone()[0]
                        cur2.execute('INSERT into books (book_meta_id, type, location) VALUES (%s, %s, %s)', (meta_id, extension, filepath))
                        database2.commit()
                        
                        print 
