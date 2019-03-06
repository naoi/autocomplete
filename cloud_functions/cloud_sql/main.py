# Updated by yas 2018/11/01.
# Updated by yas 2018/10/30.
# Updated by yas 2018/10/29.
# Updated by yas 2018/10/28.
# Updated by yas 2018/10/22.
# Created by yas 2018/10/20.

import pymysql
from pymysql.err import OperationalError
import redis

import json
import hashlib
from flask import Flask, jsonify, request, Response
from os import getenv
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

# TODO (developer): specify SQL connection details
CONNECTION_NAME = getenv(
  'INSTANCE_CONNECTION_NAME',
  '<PROJECT_ID:REGION:DB_HOST>')
DB_USER = getenv('MYSQL_USER', '<MYSQL_USER>')
DB_PASSWORD = getenv('MYSQL_PASSWORD', '<MYSQL_PASSWORD>')
DB_NAME = getenv('MYSQL_DATABASE', '<MYSQL_DATABASE>')

REDIS_HOST = '<REDIS_HOST>'
REDIS_PORT = 6379

MYSQL_CONFIG = {
  'user': DB_USER,
  'password': DB_PASSWORD,
  'db': DB_NAME,
  'charset': 'utf8mb4',
  'cursorclass': pymysql.cursors.DictCursor,
  'autocommit': True
}

REDIS_CONFIG = {
  'host': REDIS_HOST,
  'port': REDIS_PORT
}

# Create SQL connection globally to enable reuse
# PyMySQL does not include support for connection pooling
MYSQL_CONN = None
REDIS_CONN = None

def __get_cursor():
    """
    Helper function to get a cursor
      PyMySQL does NOT automatically reconnect,
      so we must reconnect explicitly using ping()
    """
    try:
        return MYSQL_CONN.cursor()
    except OperationalError:
        MYSQL_CONN.ping(reconnect=True)
        return MYSQL_CONN.cursor()


@app.route('/')
def autocomplete(request):

    start = time.time()

    global MYSQL_CONN
    global REDIS_CONN

    # Parse typed string from a client.
    typed_string = ''
    request_json = request.get_json()
    if request.args and 'term' in request.args:
        typed_string = request.args.get('term')
    elif request_json and 'term' in request_json:
        typed_string = request_json['term']
    else:
        response = jsonify([{}])
        response.headers.add('Access-Control-Allow-Origin', '*')
        return response

    # Initialize connections lazily, in case SQL access isn't needed for this
    # GCF instance. Doing so minimizes the number of active SQL connections,
    # which helps keep your GCF instances under SQL connection limits.

    if not REDIS_CONN:
        try:
            REDIS_CONN = redis.Redis(host = REDIS_CONFIG['host'], port = REDIS_CONFIG['port'])
        except redis.ConnectionError:
            error_msg = 'Error: Redis Server not available'
            response = jsonify([].append({'label': error_msg,
                                          'value': error_msg}))
            response.headers.add('Access-Control-Allow-Origin', '*')
            return response

    redis_key = hashlib.md5(typed_string.encode('utf-8')).hexdigest()
    cached_results = REDIS_CONN.get(redis_key)
    if cached_results:
        matches = json.loads(cached_results)
        response = jsonify(matches)
        response.headers.add('Access-Control-Allow-Origin', '*')

        elapsed_time = (time.time() - start) * 1000  # Performance Measurement
        print ('Processing time: {0:.0f} ms (cached) [ {1} ] => [ {2} ]'
               .format(elapsed_time,
                       typed_string,
                       matches[0]['label']))
        return response

    # Initialize Datastore Client
    if not MYSQL_CONN:
        try:
            MYSQL_CONN = pymysql.connect(**MYSQL_CONFIG)
        except OperationalError:
            # If production settings fail, use local development ones
            MYSQL_CONFIG['unix_socket'] = f'/cloudsql/{CONNECTION_NAME}'
            MYSQL_CONN = pymysql.connect(**MYSQL_CONFIG)

    # Remember to close SQL resources declared while running this function.
    # Keep any declared in global scope (e.g. MYSQL_CONN) for later reuse.
    with __get_cursor() as cursor:

        cursor.execute('SELECT `name` FROM `product_entity_field_data` WHERE `name` LIKE "' + typed_string + '%" ORDER BY `name` LIMIT 10')  # Hard coded
        results = cursor.fetchall()

        # Create query and get the results
        matches = []
        for result in results:
            matches.append({'label': result['name'],
                            'value': result['name']})
        response = jsonify(matches)
        response.headers.add('Access-Control-Allow-Origin', '*')
        REDIS_CONN.set(redis_key, json.dumps(matches))

        elapsed_time = (time.time() - start) * 1000  # Performance Measurement
        print ('Processing time: {0:.0f} ms [ {1} ] => [ {2} ]'
               .format(elapsed_time,
                       typed_string,
                       matches[0]['label']))
        return response